<?php

namespace App\Services;

use App\Models\ApprovalPolicy;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\AuditLogService;

class ApprovalWorkflowService
{
    /**
     * Submit a document to its approval workflow.
     */
    public function submit(
        Document $document,
        User $user,
        AuditLogService $auditLog
    ): void {
        DB::transaction(function () use (
            $document,
            $user,
            $auditLog
        ) {
            $document = Document::whereKey($document->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $document->created_by !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }

            if ($document->application_status !== 'draft') {
                throw ValidationException::withMessages([
                    'document' => 'Hanya dokumen berstatus Draft yang dapat dikirim.',
                ]);
            }

            $policy = ApprovalPolicy::with([
                'steps.assignments',
            ])->find($document->approval_policy_id);

            if (!$policy || !$policy->is_active) {
                throw ValidationException::withMessages([
                    'document' => 'Approval policy tidak tersedia atau tidak aktif.',
                ]);
            }

            $steps = $policy->steps;

            if ($steps->isEmpty()) {
                throw ValidationException::withMessages([
                    'document' => 'Approval policy belum memiliki tahapan persetujuan.',
                ]);
            }

            foreach ($steps as $step) {
                if ($step->assignments->isEmpty()) {
                    throw ValidationException::withMessages([
                        'document' => "Tahap {$step->name} belum memiliki approver.",
                    ]);
                }
            }

            $currentVersion = DocumentVersion::where(
                'document_id',
                $document->id
            )->where(
                'version_number',
                $document->current_version
            )->first();

            if (!$currentVersion) {
                throw ValidationException::withMessages([
                    'document' => 'Versi dokumen yang akan diajukan tidak ditemukan.',
                ]);
            }

            // Only create approval records for the first step.
            $firstStep = $steps->first();

            foreach ($firstStep->assignments as $assignment) {
                DocumentApproval::firstOrCreate(
                    [
                        'document_version_id' => $currentVersion->id,
                        'approval_step_id' => $firstStep->id,
                        'approver_id' => $assignment->user_id,
                    ],
                    [
                        'decision' => null,
                    ]
                );
            }

            $document->update([
                'application_status' => 'pending',
            ]);

            // Record document submission.
            $auditLog->record(
                $user,
                'document_submitted',
                'Mengajukan dokumen untuk proses persetujuan.',
                $document,
                [
                    'document_title' => $document->title,
                    'version' => $currentVersion->version_number,
                    'approval_policy' => $policy->name,
                    'first_approval_step' => $firstStep->name,
                ]
            );
        });
    }

    /**
     * Approve or reject an assigned approval.
     */
    public function decide(
        int $approvalId,
        User $user,
        string $decision,
        ?string $comments = null,
        AuditLogService $auditLog
    ): void {
        DB::transaction(function () use (
            $approvalId,
            $user,
            $decision,
            $comments,
            $auditLog
        ) {
            $approval = DocumentApproval::findOrFail($approvalId);

            $version = DocumentVersion::findOrFail(
                $approval->document_version_id
            );

            // Lock the document to prevent simultaneous workflow transitions.
            $document = Document::whereKey($version->document_id)
                ->lockForUpdate()
                ->firstOrFail();

            $approval = DocumentApproval::whereKey($approvalId)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $approval->approver_id !== (int) $user->id) {
                abort(403, 'Anda tidak ditugaskan untuk persetujuan ini.');
            }

            if ($approval->decision !== null) {
                throw ValidationException::withMessages([
                    'decision' => 'Keputusan untuk tahap ini sudah diberikan.',
                ]);
            }

            if (!in_array($decision, ['approved', 'rejected'], true)) {
                throw ValidationException::withMessages([
                    'decision' => 'Keputusan tidak valid.',
                ]);
            }

            if (!in_array(
                $document->application_status,
                ['pending', 'in_progress'],
                true
            )) {
                throw ValidationException::withMessages([
                    'decision' => 'Dokumen tidak sedang dalam proses persetujuan.',
                ]);
            }

            if ($decision === 'rejected' && blank($comments)) {
                throw ValidationException::withMessages([
                    'comments' => 'Alasan penolakan wajib diisi.',
                ]);
            }

            $approval->update([
                'decision' => $decision,
                'comments' => $comments,
                'decided_at' => now(),
            ]);

            // Record approval or rejection.
            $approval->load('approvalStep');

            $auditLog?->record(
                $user,
                $decision === 'approved'
                    ? 'document_approved'
                    : 'document_rejected',
                $decision === 'approved'
                    ? 'Menyetujui dokumen pada tahap persetujuan.'
                    : 'Menolak dokumen pada tahap persetujuan.',
                $document,
                [
                    'document_title' => $document->title,
                    'version' => $version->version_number,
                    'approval_step' => $approval->approvalStep->name,
                    'decision' => $decision,
                    'comments' => $comments,
                ]
            );

            // Rejection stops the workflow immediately.
            if ($decision === 'rejected') {
                $document->update([
                    'application_status' => 'rejected',
                ]);

                return;
            }

            // Check whether every approver assigned to this step has approved.
            $currentStepApprovals = DocumentApproval::where(
                'document_version_id',
                $approval->document_version_id
            )->where(
                'approval_step_id',
                $approval->approval_step_id
            )->get();

            $allApproved = $currentStepApprovals->every(
                fn ($item) => $item->decision === 'approved'
            );

            // Wait if other approvers at the same step have not decided.
            if (!$allApproved) {
                return;
            }

            $policy = ApprovalPolicy::with('steps.assignments')
                ->findOrFail($document->approval_policy_id);

            $currentStep = $policy->steps->firstWhere(
                'id',
                $approval->approval_step_id
            );

            $nextStep = $policy->steps->first(
                fn ($step) => $step->step_order > $currentStep->step_order
            );

            // No next step means the document is fully approved.
            if (!$nextStep) {
                $document->update([
                    'application_status' => 'approved',
                ]);

                return;
            }

            // Activate the next approval step.
            foreach ($nextStep->assignments as $assignment) {
                DocumentApproval::firstOrCreate(
                    [
                        'document_version_id' => $approval->document_version_id,
                        'approval_step_id' => $nextStep->id,
                        'approver_id' => $assignment->user_id,
                    ],
                    [
                        'decision' => null,
                    ]
                );
            }

            $document->update([
                'application_status' => 'in_progress',
            ]);
        });
    }
}
