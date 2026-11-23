<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Document;
use App\Models\UserWallet;
use App\Models\DocumentApproval;
use App\Models\ApprovalPolicy;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function secretary()
    {
        $user = Auth::user();

        $documents = Document::where('created_by', $user->id)
            ->with('documentType')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            [
                'label' => 'Total Documents',
                'value' => Document::where('created_by', $user->id)->count(),
                'icon' => 'bi-files',
                'color' => 'purple',
            ],
            [
                'label' => 'In Progress',
                'value' => Document::where('created_by', $user->id)
                    ->whereNull('published_at')
                    ->where('application_status', '!=', 'draft')
                    ->count(),
                'icon' => 'bi-hourglass-split',
                'color' => 'blue',
            ],
            [
                'label' => 'Published Documents',
                'value' => Document::where('created_by', $user->id)
                    ->whereNotNull('published_at')
                    ->count(),
                'icon' => 'bi-patch-check',
                'color' => 'green',
            ],
        ];

        return view('dashboards.secretary', compact(
            'stats',
            'documents'
        ));
    }

    public function approver()
    {
        $user = Auth::user();

        $pendingApprovals = DocumentApproval::with([
            'documentVersion.document.documentType',
            'approvalStep',
        ])
            ->where('approver_id', $user->id)
            ->whereNull('decision')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            [
                'label' => 'Pending Approvals',
                'value' => DocumentApproval::where('approver_id', $user->id)
                    ->whereNull('decision')
                    ->count(),
                'icon' => 'bi-hourglass-split',
                'color' => 'purple',
            ],
            [
                'label' => 'Approved',
                'value' => DocumentApproval::where('approver_id', $user->id)
                    ->where('decision', 'approved')
                    ->count(),
                'icon' => 'bi-check-circle',
                'color' => 'green',
            ],
            [
                'label' => 'Rejected',
                'value' => DocumentApproval::where('approver_id', $user->id)
                    ->where('decision', 'rejected')
                    ->count(),
                'icon' => 'bi-x-circle',
                'color' => 'red',
            ],
        ];

        return view('dashboards.approver', compact(
            'stats',
            'pendingApprovals'
        ));
    }

    public function administrator()
    {
        $stats = [
            [
                'label' => 'Total Users',
                'value' => User::count(),
                'icon' => 'bi-people',
                'color' => 'purple',
            ],
            [
                'label' => 'Total Documents',
                'value' => Document::count(),
                'icon' => 'bi-files',
                'color' => 'blue',
            ],
            [
                'label' => 'Approval Policies',
                'value' => ApprovalPolicy::count(),
                'icon' => 'bi-diagram-3',
                'color' => 'green',
            ],
            [
                'label' => 'Registered Wallets',
                'value' => UserWallet::count(),
                'icon' => 'bi-wallet2',
                'color' => 'orange',
            ],
        ];

        $users = User::with('role')
            ->latest()
            ->take(6)
            ->get();

        $documentTypesCount = DocumentType::count();

        return view('dashboards.administrator', compact(
            'stats',
            'users',
            'documentTypesCount'
        ));
    }

    public function verifier()
    {
        $publishedDocuments = Document::with([
            'documentType',
            'creator',
        ])
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(6)
            ->get();

        $stats = [
            [
                'label' => 'Published Documents',
                'value' => Document::whereNotNull('published_at')->count(),
                'icon' => 'bi-file-earmark-check',
                'color' => 'purple',
            ],
            [
                'label' => 'Document Types',
                'value' => DocumentType::count(),
                'icon' => 'bi-collection',
                'color' => 'blue',
            ],
        ];

        return view('dashboards.verifier', compact(
            'stats',
            'publishedDocuments'
        ));
    }
}
