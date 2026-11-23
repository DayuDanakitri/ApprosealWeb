@php
    $approvalRecords = $version->approvals;

    $hasRejection = $approvalRecords->contains(
        fn ($approval) => $approval->decision === 'rejected'
    );

    $hasBeenSubmitted = $approvalRecords->isNotEmpty();
@endphp

<div class="approval-timeline">
    @forelse ($document->approvalPolicy?->steps ?? [] as $step)
        <div class="d-flex gap-3 mb-4">
            <div class="d-flex flex-column align-items-center">
                @php
                    $stepHasRejection = $approvalRecords->contains(
                        fn ($approval) =>
                            $approval->approval_step_id === $step->id &&
                            $approval->decision === 'rejected'
                    );

                    $stepHasPending = $approvalRecords->contains(
                        fn ($approval) =>
                            $approval->approval_step_id === $step->id &&
                            $approval->decision === null
                    );

                    $stepHasApproval = $approvalRecords->contains(
                        fn ($approval) =>
                            $approval->approval_step_id === $step->id &&
                            $approval->decision === 'approved'
                    );
                @endphp

                @if ($stepHasRejection)
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-x-lg"></i>
                    </div>
                @elseif ($stepHasPending)
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-clock"></i>
                    </div>
                @elseif ($stepHasApproval)
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                @else
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-circle"></i>
                    </div>
                @endif

                @if (!$loop->last)
                    <div class="bg-light flex-grow-1" style="width: 2px; min-height: 30px;"></div>
                @endif
            </div>

            <div class="flex-grow-1">
                <div class="fw-semibold">
                    Tahap {{ $step->step_order }} — {{ $step->name }}
                </div>

                @forelse ($step->assignments as $assignment)
                    @php
                        $record = $approvalRecords->first(
                            fn ($approval) =>
                                $approval->approval_step_id === $step->id &&
                                $approval->approver_id === $assignment->user_id
                        );

                        if ($record?->decision === 'approved') {
                            $statusLabel = 'Disetujui';
                            $statusClass = 'success';
                        } elseif ($record?->decision === 'rejected') {
                            $statusLabel = 'Ditolak';
                            $statusClass = 'danger';
                        } elseif ($record) {
                            $statusLabel = 'Menunggu keputusan';
                            $statusClass = 'primary';
                        } elseif ($hasRejection) {
                            $statusLabel = 'Tidak dilanjutkan';
                            $statusClass = 'secondary';
                        } elseif ($hasBeenSubmitted) {
                            $statusLabel = 'Menunggu giliran';
                            $statusClass = 'secondary';
                        } else {
                            $statusLabel = 'Belum diajukan';
                            $statusClass = 'secondary';
                        }
                    @endphp

                    <div class="mt-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span>{{ $assignment->user->name }}</span>
                            <span class="badge bg-{{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        @if ($record?->decided_at)
                            <small class="text-muted">
                                {{ $record->decided_at->format('d M Y, H:i') }}
                            </small>
                        @endif

                        @if ($record?->comments)
                            <div class="small text-muted mt-1">
                                Catatan: {{ $record->comments }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted small mt-2">
                        Belum ada approver yang ditugaskan.
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <p class="text-muted mb-0">
            Belum ada tahapan persetujuan untuk dokumen ini.
        </p>
    @endforelse
</div>
