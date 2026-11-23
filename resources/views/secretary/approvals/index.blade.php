@extends('layouts.app')

@section('title', 'Approval Monitoring')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <span class="text-uppercase small text-muted fw-semibold">
            Document Management
        </span>

        <h4 class="fw-bold mb-1">Approval Monitoring</h4>

        <p class="text-muted mb-0">
            Monitor the approval progress of your submitted documents.
        </p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTICS --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        Total Documents
                    </div>
                    <h3 class="fw-bold mb-0">
                        {{ $totalDocuments }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        Pending
                    </div>
                    <h3 class="fw-bold text-warning mb-0">
                        {{ $pendingCount }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        In Progress
                    </div>
                    <h3 class="fw-bold text-primary mb-0">
                        {{ $inProgressCount }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        Approved
                    </div>
                    <h3 class="fw-bold text-success mb-0">
                        {{ $approvedCount }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        Rejected
                    </div>
                    <h3 class="fw-bold text-danger mb-0">
                        {{ $rejectedCount }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- SEARCH AND FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">

            <form method="GET"
                  action="{{ route('secretary.approvals.index') }}">

                <div class="row g-2">

                    <div class="col-md-7">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search document title or reference number..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>

                            <option value="draft"
                                @selected(request('status') === 'draft')>
                                Draft
                            </option>

                            <option value="pending"
                                @selected(request('status') === 'pending')>
                                Pending
                            </option>

                            <option value="in_progress"
                                @selected(request('status') === 'in_progress')>
                                In Progress
                            </option>

                            <option value="approved"
                                @selected(request('status') === 'approved')>
                                Approved
                            </option>

                            <option value="rejected"
                                @selected(request('status') === 'rejected')>
                                Rejected
                            </option>

                            <option value="published"
                                @selected(request('status') === 'published')>
                                Published
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit"
                                class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i>
                            Filter
                        </button>

                        <a href="{{ route('secretary.approvals.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- DOCUMENT LIST --}}
    @forelse ($documents as $document)

        @php
            $status = $document->application_status;

            $statusLabels = [
                'draft' => 'Draft',
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'published' => 'Published',
            ];

            $statusColors = [
                'draft' => 'secondary',
                'pending' => 'warning',
                'in_progress' => 'primary',
                'approved' => 'success',
                'rejected' => 'danger',
                'published' => 'success',
            ];

            $version = $document->monitoringVersion;

            $versionApprovals = $version
                ? $version->approvals
                : collect();

            $rejection = $versionApprovals->firstWhere(
                'decision',
                'rejected'
            );
        @endphp

        <div class="card border-0 shadow-sm mb-4">

            {{-- DOCUMENT HEADER --}}
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between gap-3">

                    <div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

                            <span class="badge bg-light text-dark border">
                                {{ $document->documentType->name ?? 'Document' }}
                            </span>

                            <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                            </span>

                        </div>

                        <h5 class="fw-bold mb-1">
                            {{ $document->title }}
                        </h5>

                        <div class="small text-muted">
                            Reference:
                            {{ $document->reference_number ?? 'Not assigned' }}
                        </div>

                        <div class="small text-muted mt-1">
                            Current Version:
                            {{ $document->current_version }}
                        </div>
                    </div>

                    <div class="text-md-end">

                        <div class="small text-muted mb-2">
                            Last Updated
                        </div>

                        <div class="fw-semibold">
                            {{ $document->updated_at?->format('d M Y, H:i') ?? '-' }}
                        </div>

                        <a href="{{ route('secretary.documents.show', $document) }}"
                           class="btn btn-sm btn-outline-primary mt-3">
                            <i class="bi bi-eye me-1"></i>
                            View Document
                        </a>

                    </div>

                </div>

                <hr class="my-4">

                {{-- APPROVAL WORKFLOW --}}
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-diagram-3 me-2"></i>
                    Approval Progress
                </h6>

                @if ($document->approvalPolicy && $document->approvalPolicy->steps->isNotEmpty())

                    <div class="row g-3">

                        @foreach ($document->approvalPolicy->steps as $step)

                            @php
                                $stepApprovals = $versionApprovals->where(
                                    'approval_step_id',
                                    $step->id
                                );

                                $hasApprovals = $stepApprovals->isNotEmpty();

                                $stepRejected = $stepApprovals->contains(
                                    'decision',
                                    'rejected'
                                );

                                $allApproved = $hasApprovals
                                    && $stepApprovals->every(
                                        fn ($approval) => $approval->decision === 'approved'
                                    );

                                $stepInProgress = $hasApprovals
                                    && ! $stepRejected
                                    && ! $allApproved;

                                if ($stepRejected) {
                                    $stepStatus = 'Rejected';
                                    $stepColor = 'danger';
                                } elseif ($allApproved) {
                                    $stepStatus = 'Approved';
                                    $stepColor = 'success';
                                } elseif ($stepInProgress) {
                                    $stepStatus = 'In Progress';
                                    $stepColor = 'primary';
                                } else {
                                    $stepStatus = 'Waiting';
                                    $stepColor = 'secondary';
                                }
                            @endphp

                            <div class="col-md-6 col-xl-4">

                                <div class="border rounded-3 p-3 h-100">

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <span class="small fw-semibold text-muted">
                                            STAGE {{ $step->step_order }}
                                        </span>

                                        <span class="badge bg-{{ $stepColor }}">
                                            {{ $stepStatus }}
                                        </span>

                                    </div>

                                    <h6 class="fw-bold mb-3">
                                        {{ $step->name }}
                                    </h6>

                                    @if ($step->assignments->isNotEmpty())

                                        @foreach ($step->assignments as $assignment)

                                            @php
                                                $approval = $stepApprovals->firstWhere(
                                                    'approver_id',
                                                    $assignment->user_id
                                                );

                                                $decision = $approval?->decision;

                                                if ($decision === 'approved') {
                                                    $approverStatus = 'Approved';
                                                    $approverColor = 'success';
                                                    $approverIcon = 'bi-check-circle-fill';
                                                } elseif ($decision === 'rejected') {
                                                    $approverStatus = 'Rejected';
                                                    $approverColor = 'danger';
                                                    $approverIcon = 'bi-x-circle-fill';
                                                } elseif ($approval) {
                                                    $approverStatus = 'Awaiting Decision';
                                                    $approverColor = 'warning';
                                                    $approverIcon = 'bi-clock-fill';
                                                } else {
                                                    $approverStatus = 'Not Started';
                                                    $approverColor = 'secondary';
                                                    $approverIcon = 'bi-hourglass';
                                                }
                                            @endphp

                                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">

                                                <div class="small">
                                                    <i class="bi bi-person me-1"></i>
                                                    {{ $assignment->user->name ?? 'Unknown Approver' }}
                                                </div>

                                                <span class="small text-{{ $approverColor }} text-nowrap">
                                                    <i class="bi {{ $approverIcon }} me-1"></i>
                                                    {{ $approverStatus }}
                                                </span>

                                            </div>

                                            @if ($approval && $approval->decision === 'rejected' && $approval->comments)
                                                <div class="alert alert-danger py-2 px-3 small mt-2 mb-2">
                                                    <strong>Rejection Reason:</strong>
                                                    {{ $approval->comments }}
                                                </div>
                                            @endif

                                        @endforeach

                                    @else

                                        <p class="small text-muted mb-0">
                                            No approver assigned to this stage.
                                        </p>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="alert alert-light border mb-0">
                        No approval policy or stages are available for this document.
                    </div>

                @endif

                {{-- REJECTION SUMMARY --}}
                @if ($rejection && $rejection->comments)

                    <div class="alert alert-danger mt-4 mb-0">
                        <div class="fw-semibold mb-1">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Document Rejected
                        </div>

                        {{ $rejection->comments }}
                    </div>

                @endif

            </div>
        </div>

    @empty

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">

                <i class="bi bi-inbox display-4 text-muted"></i>

                <h5 class="fw-bold mt-3">
                    No Documents Found
                </h5>

                <p class="text-muted mb-0">
                    There are no documents matching your search or filter.
                </p>

            </div>
        </div>

    @endforelse

    {{-- PAGINATION --}}
    @if ($documents->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $documents->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
