
@extends('layouts.app')

@section('title', 'Assigned Approvals')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Approval Management</span>
        <h1>Assigned Approvals</h1>
        <p>Review documents assigned to your account.</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="content-card">

    <div class="content-card-header">
        <div>
            <h5>Pending Approvals</h5>
            <p>Documents waiting for your decision.</p>
        </div>

        <span class="record-count">
            {{ $approvals->total() }} pending
        </span>
    </div>

    <div class="table-responsive">
        <table class="table app-table">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Document Type</th>
                    <th>Approval Step</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($approvals as $approval)
                    @php
                        $document = $approval->documentVersion->document;
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $document->title }}</strong>
                            <small class="d-block text-muted">
                                DOC-{{ $document->id }}
                            </small>
                        </td>

                        <td>
                            {{ $document->documentType->name ?? '-' }}
                        </td>

                        <td>
                            {{ $approval->approvalStep->name }}
                        </td>

                        <td>
                            {{ $approval->created_at->format('d M Y') }}
                        </td>

                        <td>
                            <a
                                href="{{ route('approver.approvals.show', $approval) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-check2-circle"></i>
                                <strong>No pending approvals</strong>
                                <span>
                                    There are currently no documents waiting for your decision.
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $approvals->links() }}
    </div>

</div>

@endsection
