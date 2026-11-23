@extends('layouts.app')

@section('title', 'Approval Policies')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Approval Policies</h4>
            <p class="text-muted mb-0">
                Manage document approval workflows and approver assignments.
            </p>
        </div>

        <a href="{{ route('administrator.approval-policies.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add Approval Policy
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Total Policies</div>
                    <h3 class="fw-bold mb-0">{{ $totalPolicies }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Active Policies</div>
                    <h3 class="fw-bold text-success mb-0">{{ $activePolicies }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Inactive Policies</div>
                    <h3 class="fw-bold text-secondary mb-0">{{ $inactivePolicies }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2">Policies In Use</div>
                    <h3 class="fw-bold mb-0">{{ $policiesInUse }}</h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form method="GET"
                  action="{{ route('administrator.approval-policies.index') }}"
                  class="row g-2 mb-4">

                <div class="col-md-6">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by name, code, or description..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" @selected(request('status') === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>

                    <a href="{{ route('administrator.approval-policies.index') }}"
                       class="btn btn-light">
                        Reset
                    </a>
                </div>

            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Policy</th>
                            <th>Approval Flow</th>
                            <th class="text-center">Steps</th>
                            <th class="text-center">Documents</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($approvalPolicies as $policy)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $policy->name }}</div>
                                    <small class="text-muted">
                                        {{ $policy->code }}
                                    </small>

                                    @if ($policy->description)
                                        <div class="small text-muted mt-1">
                                            {{ $policy->description }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @forelse ($policy->steps as $step)
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-light text-dark border">
                                                {{ $step->step_order }}
                                            </span>

                                            <span class="small">
                                                {{ $step->name }}
                                            </span>

                                            <span class="text-muted small">
                                                ({{ $step->assignments->count() }} approver)
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-muted small">
                                            No steps configured
                                        </span>
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    {{ $policy->steps_count }}
                                </td>

                                <td class="text-center">
                                    {{ $policy->documents_count }}
                                </td>

                                <td>
                                    @if ($policy->is_active)
                                        <span class="badge bg-success-subtle text-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a
                                        href="{{ route('administrator.approval-policies.edit', $policy->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-diagram-3 fs-2 text-muted"></i>
                                    <p class="fw-semibold mt-2 mb-1">
                                        No Approval Policies Found
                                    </p>
                                    <p class="text-muted small mb-0">
                                        There are no policies matching your search.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $approvalPolicies->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
