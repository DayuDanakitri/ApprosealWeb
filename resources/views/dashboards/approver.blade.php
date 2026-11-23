
@extends('layouts.app')

@section('title', 'Approver Dashboard')

@section('content')

<div class="page-heading">

    <div>
        <span class="page-eyebrow">Approval Workspace</span>

        <h1>Welcome back, {{ auth()->user()->name }}</h1>

        <p>Review documents assigned to you and manage your approval decisions.</p>
    </div>

</div>


<div class="row g-3 mb-4">

    @foreach ($stats as $stat)
        <div class="col-md-4">
            @include('components.dashboard.stat-card', ['stat' => $stat])
        </div>
    @endforeach

</div>


<div class="content-card">

    <div class="content-card-header">

        <div>
            <h5>Pending Approvals</h5>
            <p>Documents waiting for your decision.</p>
        </div>

        <span class="record-count">
            {{ $pendingApprovals->count() }} records
        </span>

    </div>


    <div class="table-responsive">

        <table class="table app-table">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Approval Step</th>
                    <th>Version</th>
                    <th>Assigned Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($pendingApprovals as $approval)

                    <tr>

                        <td>
                            <div class="document-name">

                                <div class="document-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>

                                <div>
                                    <strong>
                                        {{ $approval->documentVersion?->document?->title ?? 'Document unavailable' }}
                                    </strong>

                                    <small>
                                        {{ $approval->documentVersion?->document?->reference_number ?? '-' }}
                                    </small>
                                </div>

                            </div>
                        </td>

                        <td>
                            {{ $approval->approvalStep?->name ?? '-' }}
                        </td>

                        <td>
                            v{{ $approval->documentVersion?->version_number ?? '-' }}
                        </td>

                        <td>
                            {{ $approval->created_at->format('d M Y') }}
                        </td>

                        <td>
                            <span class="status-badge status-pending">
                                Pending
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-check-circle"></i>
                                <strong>All caught up!</strong>
                                <span>You currently have no pending approvals.</span>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
