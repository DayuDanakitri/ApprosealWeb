@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">User Management</h4>
            <p class="text-muted mb-0">
                Manage and monitor all registered users within ApproSeal.
            </p>
        </div>

        <a href="{{ route('administrator.users.create') }}"
        class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>
            Add User
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Users</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($totalUsers) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Registered Today</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($usersToday) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Available Roles</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format($totalRoles) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Search and Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('administrator.users.index') }}">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Search User</label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name or email..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Role</label>

                        <select name="role_id" class="form-select">
                            <option value="">All Roles</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected((string) request('role_id') === (string) $role->id)
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit"
                                class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>
                            Filter
                        </button>
                    </div>

                    <div class="col-12">
                        <a
                            href="{{ route('administrator.users.index') }}"
                            class="btn btn-light border"
                        >
                            Reset Filters
                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- User Table --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-1">Registered Users</h5>

            <p class="text-muted small mb-0">
                All users registered in the ApproSeal system.
            </p>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="px-4">User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered Date</th>
                            <th class="text-end px-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                            <tr>

                                <td class="px-4">
                                    <div class="fw-semibold">
                                        {{ $user->name }}
                                    </div>

                                    <small class="text-muted">
                                        User ID: #{{ $user->id }}
                                    </small>
                                </td>

                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>
                                    @php
                                        $roleColors = [
                                            'secretary' => 'primary',
                                            'approver' => 'success',
                                            'administrator' => 'danger',
                                            'verifier' => 'secondary',
                                        ];

                                        $roleSlug = $user->role?->slug;
                                        $roleColor = $roleColors[$roleSlug] ?? 'secondary';
                                    @endphp

                                    <span class="badge bg-{{ $roleColor }}">
                                        {{ $user->role?->name ?? 'No Role' }}
                                    </span>
                                </td>

                                <td>
                                    <div>
                                        {{ $user->created_at?->format('d M Y') }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $user->created_at?->format('H:i') }}
                                    </small>
                                </td>

                                <td class="text-end px-4">
                                    <a href="{{ route('administrator.users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="text-center py-5 text-muted">
                                    No users found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white border-0 px-4 py-3">
            {{ $users->links() }}
        </div>

    </div>

</div>

@endsection
