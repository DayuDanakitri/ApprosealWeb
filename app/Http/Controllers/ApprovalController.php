<?php

namespace App\Http\Controllers;

use App\Models\DocumentApproval;
use App\Services\ApprovalWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /**
     * Display approvals assigned to the logged-in approver.
     */
    public function index()
    {
        $approvals = DocumentApproval::with([
            'documentVersion.document.documentType',
            'approvalStep',
        ])
            ->where('approver_id', Auth::id())
            ->whereNull('decision')
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    /**
     * Display approval history of the logged-in approver.
     */
    public function history(Request $request)
    {
        $baseQuery = DocumentApproval::query()
            ->where('approver_id', Auth::id())
            ->whereNotNull('decision');

        // Overall statistics, independent of the selected filters.
        $totalDecisions = (clone $baseQuery)->count();

        $approvedCount = (clone $baseQuery)
            ->where('decision', 'approved')
            ->count();

        $rejectedCount = (clone $baseQuery)
            ->where('decision', 'rejected')
            ->count();

        // Query for the history table.
        $query = (clone $baseQuery)->with([
            'documentVersion.document.documentType',
            'approvalStep',
            'approver',
        ]);

        // Search by document title or reference number.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas(
                'documentVersion.document',
                function ($documentQuery) use ($search) {
                    $documentQuery->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('title', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%");
                    });
                }
            );
        }

        // Filter by decision.
        if (in_array($request->input('decision'), ['approved', 'rejected'], true)) {
            $query->where('decision', $request->input('decision'));
        }

        // Filter by decision date.
        if ($request->filled('date_from')) {
            $query->whereDate('decided_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('decided_at', '<=', $request->input('date_to'));
        }

        $history = $query
            ->orderByDesc('decided_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('approvals.history', compact(
            'history',
            'totalDecisions',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Display a specific approval.
     */
    public function show(DocumentApproval $documentApproval)
    {
        abort_unless(
            (int) $documentApproval->approver_id === (int) Auth::id(),
            403
        );

        $documentApproval->load([
            'approvalStep',
            'documentVersion.document.documentType',
            'documentVersion.document.approvalPolicy.steps.assignments.user',
            'documentVersion.uploader',
            'documentVersion.approvals.approver',
            'documentVersion.approvals.approvalStep',
        ]);

        return view('approvals.show', compact('documentApproval'));
    }

    /**
     * Submit an approval or rejection decision.
     */
    public function decide(
        Request $request,
        DocumentApproval $documentApproval,
        ApprovalWorkflowService $workflow,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'decision' => [
                'required',
                'in:approved,rejected',
            ],
            'comments' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:decision,rejected',
            ],
        ]);

        $workflow->decide(
            $documentApproval->id,
            Auth::user(),
            $validated['decision'],
            $validated['comments'] ?? null,
            $auditLog
        );

        return redirect()
            ->route('approver.approvals.index')
            ->with(
                'success',
                $validated['decision'] === 'approved'
                    ? 'Persetujuan berhasil diberikan.'
                    : 'Dokumen berhasil ditolak.'
            );
    }

    /**
     * Download the document assigned to the approver.
     */
    public function download(DocumentApproval $documentApproval)
    {
        abort_unless(
            (int) $documentApproval->approver_id === (int) Auth::id(),
            403
        );

        $version = $documentApproval->documentVersion;

        abort_unless(
            Storage::disk('local')->exists($version->file_path),
            404
        );

        return Storage::disk('local')->download(
            $version->file_path,
            $version->original_filename
        );
    }
}
