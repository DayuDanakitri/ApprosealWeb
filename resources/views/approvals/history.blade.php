@extends('layouts.app')

@section('title', 'Approval History')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Approval Management</span>
        <h1>Approval History</h1>
        <p>View all approval decisions you have made.</p>
    </div>

    <a
        href="{{ route('approver.approvals.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Assigned Documents
    </a>
</div>

{{-- STATISTICS --}}
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Total Decisions</span>
                    <h3 class="mb-0 mt-2">{{ $totalDecisions }}</h3>
                </div>

                <div class="integrity-icon">
                    <i class="bi bi-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Approved</span>
                    <h3 class="text-success mb-0 mt-2">
                        {{ $approvedCount }}
                    </h3>
                </div>

                <div class="integrity-icon">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="content-card h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Rejected</span>
                    <h3 class="text-danger mb-0 mt-2">
                        {{ $rejectedCount }}
                    </h3>
                </div>

                <div class="integrity-icon">
                    <i class="bi bi-x-circle text-danger"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- FILTERS --}}
<div class="content-card mb-3">

    <div class="content-card-header">
        <div>
            <h5>Filter History</h5>
            <p>Search and filter your previous decisions.</p>
        </div>
    </div>

    <form
        method="GET"
        action="{{ route('approver.approvals.history') }}"
    >
        <div class="row g-3 align-items-end">

            <div class="col-lg-4">
                <label class="form-label">Search Document</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Enter document title or reference..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-lg-2">
                <label class="form-label">Decision</label>
                <select name="decision" class="form-select">
                    <option value="">All Decisions</option>
                    <option
                        value="approved"
                        @selected(request('decision') === 'approved')
                    >
                        Approved
                    </option>
                    <option
                        value="rejected"
                        @selected(request('decision') === 'rejected')
                    >
                        Rejected
                    </option>
                </select>
            </div>

            <div class="col-lg-2">
                <label class="form-label">From Date</label>
                <input
                    type="date"
                    name="date_from"
                    class="form-control"
                    value="{{ request('date_from') }}"
                >
            </div>

            <div class="col-lg-2">
                <label class="form-label">To Date</label>
                <input
                    type="date"
                    name="date_to"
                    class="form-control"
                    value="{{ request('date_to') }}"
                >
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom flex-grow-1">
                    <i class="bi bi-search me-1"></i>
                    Filter
                </button>

                <a
                    href="{{ route('approver.approvals.history') }}"
                    class="btn btn-outline-secondary"
                    title="Reset Filters"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>

        </div>
    </form>

</div>

{{-- HISTORY TABLE --}}
<div class="content-card">

    <div class="content-card-header">
        <div>
            <h5>Decision Records</h5>
            <p>
                Showing {{ $history->count() }} of
                {{ $history->total() }} records.
            </p>
        </div>
    </div>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Version</th>
                    <th>Approval Step</th>
                    <th>Decision</th>
                    <th>Decision Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($history as $record)

                    @php
                        $document = $record->documentVersion->document;
                    @endphp

                    <tr>

                        <td>
                            <div class="fw-semibold">
                                {{ $document->title }}
                            </div>

                            <small class="text-muted">
                                {{ $document->documentType->name ?? '-' }}
                            </small>

                            <small class="d-block text-muted">
                                {{ $document->reference_number ?? 'DOC-' . $document->id }}
                            </small>
                        </td>

                        <td>
                            v{{ $record->documentVersion->version_number }}
                        </td>

                        <td>
                            {{ $record->approvalStep->name ?? '-' }}
                        </td>

                        <td>
                            @if ($record->decision === 'approved')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Approved
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Rejected
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($record->decided_at)
                                <div>
                                    {{ $record->decided_at->format('d M Y') }}
                                </div>

                                <small class="text-muted">
                                    {{ $record->decided_at->format('H:i') }}
                                </small>
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-end">
                            <a
                                href="{{ route('approver.approvals.show', $record) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Details
                            </a>
                        </td>

                    </tr>

                    @if ($record->decision === 'rejected' && $record->comments)
                        <tr>
                            <td colspan="6" class="pt-0">
                                <div class="small text-danger">
                                    <i class="bi bi-chat-left-text me-1"></i>
                                    Rejection reason: {{ $record->comments }}
                                </div>
                            </td>
                        </tr>
                    @endif

                @empty

                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-clock-history fs-2 text-muted"></i>

                            <h6 class="mt-3">
                                No Approval History Found
                            </h6>

                            <p class="text-muted small mb-0">
                                Your completed approval decisions will appear here.
                            </p>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if ($history->hasPages())
        <div class="mt-3">
            {{ $history->links() }}
        </div>
    @endif

</div>

@endsection
