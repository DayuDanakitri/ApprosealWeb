<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretaryApprovalController extends Controller
{
    /**
     * Display approval monitoring for documents
     * created by the logged-in secretary.
     */
    public function index(Request $request)
    {
        $baseQuery = Document::query()
            ->where('created_by', Auth::id());

        // Overall statistics.
        $totalDocuments = (clone $baseQuery)->count();

        $pendingCount = (clone $baseQuery)
            ->where('application_status', 'pending')
            ->count();

        $inProgressCount = (clone $baseQuery)
            ->where('application_status', 'in_progress')
            ->count();

        $approvedCount = (clone $baseQuery)
            ->whereIn('application_status', ['approved', 'published'])
            ->count();

        $rejectedCount = (clone $baseQuery)
            ->where('application_status', 'rejected')
            ->count();

        // Main monitoring query.
        $query = Document::with([
            'documentType',
            'approvalPolicy.steps.assignments.user',
            'versions.approvals.approver',
            'versions.approvals.approvalStep',
        ])
            ->where('created_by', Auth::id());

        // Search by title or reference number.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere(
                        'reference_number',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // Filter by application status.
        $allowedStatuses = [
            'draft',
            'pending',
            'in_progress',
            'approved',
            'rejected',
            'published',
        ];

        if (in_array($request->input('status'), $allowedStatuses, true)) {
            $query->where(
                'application_status',
                $request->input('status')
            );
        }

        $documents = $query
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        // Attach the version currently referenced by each document.
        foreach ($documents as $document) {
            $currentVersion = $document->versions->firstWhere(
                'version_number',
                $document->current_version
            );

            $document->setRelation(
                'monitoringVersion',
                $currentVersion
            );
        }

        return view('secretary.approvals.index', compact(
            'documents',
            'totalDocuments',
            'pendingCount',
            'inProgressCount',
            'approvedCount',
            'rejectedCount'
        ));
    }
}
