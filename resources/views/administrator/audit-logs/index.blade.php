@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Audit Logs</h4>
        <p class="text-muted mb-0">
            Monitor and review all activities performed within ApproSeal.
        </p>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Activities</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($auditLogs->total()) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Activities Today</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format(
                            \App\Models\AuditLog::whereDate('created_at', today())->count()
                        ) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Activity Types</p>
                    <h3 class="fw-bold mb-0">
                        {{ $actions->count() }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('administrator.audit-logs.index') }}">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search activity or user..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Activity</label>
                        <select name="action" class="form-select">
                            <option value="">All Activities</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}"
                                    @selected(request('action') === $action)>
                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected((string) request('user_id') === (string) $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input type="date"
                               name="date_from"
                               class="form-control"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input type="date"
                               name="date_to"
                               class="form-control"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>
                            Apply Filters
                        </button>

                        <a href="{{ route('administrator.audit-logs.index') }}"
                           class="btn btn-light border">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-1">Activity History</h5>
            <p class="text-muted small mb-0">
                Recorded system activities.
            </p>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">User</th>
                            <th>Activity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Date & Time</th>
                            <th class="text-end px-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-semibold">
                                        {{ $log->user?->name ?? 'System' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $log->user?->email ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $log->description ?? '-' }}
                                </td>

                                <td>
                                    {{ $log->ip_address ?? '-' }}
                                </td>

                                <td>
                                    <div>
                                        {{ $log->created_at?->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $log->created_at?->format('H:i:s') }}
                                    </small>
                                </td>

                                <td class="text-end px-4">
                                    <a href="{{ route('administrator.audit-logs.show', $log) }}"
                                       class="btn btn-sm btn-light border">
                                        <i class="bi bi-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="text-center py-5 text-muted">
                                    No audit activities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 px-4 py-3">
            {{ $auditLogs->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection
