
@extends('layouts.app')

@section('title', 'Administrator Dashboard')

@section('content')

<div class="page-heading">

    <div>
        <span class="page-eyebrow">System Administration</span>

        <h1>Welcome back, {{ auth()->user()->name }}</h1>

        <p>Manage users, document settings, and approval policies.</p>
    </div>

</div>


<div class="row g-3 mb-4">

    @foreach ($stats as $stat)
        <div class="col-md-6 col-xl-3">
            @include('components.dashboard.stat-card', ['stat' => $stat])
        </div>
    @endforeach

</div>


<div class="row g-3">

    <div class="col-lg-8">

        <div class="content-card">

            <div class="content-card-header">

                <div>
                    <h5>Recent Users</h5>
                    <p>Recently registered application users.</p>
                </div>

                <span class="record-count">
                    {{ $users->count() }} records
                </span>

            </div>


            <div class="table-responsive">

                <table class="table app-table">

                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                            <tr>

                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>

                                <td>{{ $user->email }}</td>

                                <td>
                                    <span class="status-badge">
                                        {{ $user->role->name ?? 'Unassigned' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $user->created_at->format('d M Y') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    No users found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="content-card quick-info-card">

            <div class="content-card-header">
                <div>
                    <h5>System Overview</h5>
                    <p>Configuration summary.</p>
                </div>
            </div>

            <div class="info-row">
                <span>Document Types</span>
                <strong>{{ $documentTypesCount }}</strong>
            </div>

            <div class="info-row">
                <span>Approval Policies</span>
                <strong>{{ \App\Models\ApprovalPolicy::count() }}</strong>
            </div>

            <div class="info-row">
                <span>Application Roles</span>
                <strong>{{ \App\Models\Role::count() }}</strong>
            </div>

        </div>

    </div>

</div>

@endsection
