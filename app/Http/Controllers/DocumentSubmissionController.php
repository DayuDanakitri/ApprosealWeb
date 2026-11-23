<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\ApprovalWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;

class DocumentSubmissionController extends Controller
{
    public function store(
        Document $document,
        ApprovalWorkflowService $workflow,
        AuditLogService $auditLog
    ) {
        $workflow->submit(
            $document,
            Auth::user(),
            $auditLog
        );

        return redirect()
            ->route('secretary.documents.show', $document)
            ->with('success', 'Dokumen berhasil diajukan untuk persetujuan.');
    }
}
