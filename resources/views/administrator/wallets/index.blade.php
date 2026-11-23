@extends('layouts.app')

@section('title', 'Wallet Management')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Wallet Management</h4>
            <p class="text-muted mb-0">
                Manage wallet addresses connected to ApproSeal accounts.
            </p>
        </div>

        <a href="{{ route('administrator.wallets.create') }}"
           class="btn btn-primary">
            <i class="bi bi-wallet2 me-2"></i>
            Add Wallet
        </a>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Wallets</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalWallets) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Verified Wallets</p>
                            <h3 class="fw-bold text-success mb-0">
                                {{ number_format($verifiedWallets) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Unverified Wallets</p>
                            <h3 class="fw-bold text-warning mb-0">
                                {{ number_format($unverifiedWallets) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-shield-exclamation fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Primary Wallets</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($primaryWallets) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                            <i class="bi bi-star-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Wallet Table --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">Registered Wallets</h5>
            <p class="text-muted small mb-0">
                View and manage all wallet addresses registered in the system.
            </p>
        </div>

        <div class="card-body px-4 pb-4">

            {{-- Search and Filters --}}
            <form method="GET"
                  action="{{ route('administrator.wallets.index') }}"
                  class="row g-2 mb-4">

                <div class="col-lg-4">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search user or wallet address..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-lg-3">
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>

                        @foreach ($users as $user)
                            <option
                                value="{{ $user->id }}"
                                @selected(request('user_id') == $user->id)
                            >
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <select name="verification" class="form-select">
                        <option value="">All Verification Status</option>

                        <option value="verified"
                            @selected(request('verification') === 'verified')>
                            Verified
                        </option>

                        <option value="unverified"
                            @selected(request('verification') === 'unverified')>
                            Unverified
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search"></i>
                    </button>

                    <a href="{{ route('administrator.wallets.index') }}"
                       class="btn btn-light border"
                       title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>

            </form>

            {{-- Table --}}
            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Wallet Address</th>
                            <th>Owner</th>
                            <th>Network</th>
                            <th>Primary</th>
                            <th>Verification</th>
                            <th>Registered</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($wallets as $wallet)

                            <tr>
                                {{-- Wallet Address --}}
                                <td>
                                    <div class="fw-semibold font-monospace small">
                                        {{ $wallet->wallet_address }}
                                    </div>

                                    <div class="text-muted small">
                                        Chain ID: {{ $wallet->chain_id ?? 'Not specified' }}
                                    </div>
                                </td>

                                {{-- Owner --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $wallet->user->name ?? 'Unknown User' }}
                                    </div>

                                    <div class="text-muted small">
                                        {{ $wallet->user->email ?? '-' }}
                                    </div>
                                </td>

                                {{-- Network --}}
                                <td>
                                    {{ $wallet->network_name ?? 'Not specified' }}
                                </td>

                                {{-- Primary --}}
                                <td>
                                    @if ($wallet->is_primary)
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-star-fill me-1"></i>
                                            Primary
                                        </span>
                                    @else
                                        <span class="text-muted small">
                                            Secondary
                                        </span>
                                    @endif
                                </td>

                                {{-- Verification --}}
                                <td>
                                    @if ($wallet->is_verified)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-shield-check me-1"></i>
                                            Verified
                                        </span>

                                        @if ($wallet->verified_at)
                                            <div class="text-muted small mt-1">
                                                {{ $wallet->verified_at->format('d M Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-shield-exclamation me-1"></i>
                                            Unverified
                                        </span>
                                    @endif
                                </td>

                                {{-- Registration Date --}}
                                <td>
                                    <span class="text-muted small">
                                        {{ $wallet->created_at?->format('d M Y') ?? '-' }}
                                    </span>
                                </td>

                                {{-- Action --}}
                                <td class="text-end">
                                    <a href="{{ route('administrator.wallets.edit', $wallet) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-wallet2 fs-1 text-muted"></i>

                                    <p class="fw-semibold mt-3 mb-1">
                                        No wallets found
                                    </p>

                                    <p class="text-muted small mb-0">
                                        There are no wallet addresses matching your search.
                                    </p>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $wallets->links() }}
            </div>

        </div>
    </div>

</div>

@endsection
