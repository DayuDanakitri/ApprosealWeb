@extends('layouts.app')

@section('title', 'Add Wallet')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Add New Wallet</h4>
            <p class="text-muted mb-0">
                Connect a blockchain wallet to an existing ApproSeal account.
            </p>
        </div>

        <a href="{{ route('administrator.wallets.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Wallets
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following errors:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Wallet Form --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">Wallet Information</h5>
                    <p class="text-muted small mb-0">
                        Enter the wallet details below.
                    </p>
                </div>

                <div class="card-body p-4">

                    <form method="POST"
                          action="{{ route('administrator.wallets.store') }}">

                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label">
                                Account Owner <span class="text-danger">*</span>
                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                class="form-select @error('user_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select User</option>

                                @foreach ($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        @selected(old('user_id') == $user->id)
                                    >
                                        {{ $user->name }}
                                        ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="wallet_address" class="form-label">
                                Wallet Address <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="wallet_address"
                                id="wallet_address"
                                class="form-control font-monospace @error('wallet_address') is-invalid @enderror"
                                value="{{ old('wallet_address') }}"
                                placeholder="0x..."
                                maxlength="42"
                                required
                            >

                            @error('wallet_address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted">
                                Enter a valid Ethereum-compatible address consisting of 42 characters.
                            </small>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="network_name" class="form-label">
                                    Network Name
                                </label>

                                <input
                                    type="text"
                                    name="network_name"
                                    id="network_name"
                                    class="form-control @error('network_name') is-invalid @enderror"
                                    value="{{ old('network_name', 'Sepolia') }}"
                                    placeholder="e.g. Sepolia"
                                >

                                @error('network_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="chain_id" class="form-label">
                                    Chain ID
                                </label>

                                <input
                                    type="number"
                                    name="chain_id"
                                    id="chain_id"
                                    class="form-control @error('chain_id') is-invalid @enderror"
                                    value="{{ old('chain_id', 11155111) }}"
                                    min="1"
                                    placeholder="e.g. 11155111"
                                >

                                @error('chain_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                        <div class="form-check mt-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_primary"
                                value="1"
                                id="is_primary"
                                @checked(old('is_primary'))
                            >

                            <label class="form-check-label" for="is_primary">
                                Set as primary wallet
                            </label>

                            <div class="text-muted small">
                                This wallet will become the user's primary wallet.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('administrator.wallets.index') }}"
                               class="btn btn-light border">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-wallet2 me-2"></i>
                                Add Wallet
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        {{-- Information Panel --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-info-circle fs-4"></i>
                        </div>

                        <h6 class="fw-bold mb-0">
                            Important Information
                        </h6>
                    </div>

                    <div class="mb-3">
                        <p class="fw-semibold small mb-1">
                            Wallet Ownership
                        </p>

                        <p class="text-muted small mb-0">
                            Adding a wallet address does not prove ownership.
                            The user must complete wallet verification later.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <p class="fw-semibold small mb-1">
                            Primary Wallet
                        </p>

                        <p class="text-muted small mb-0">
                            Each account can have only one primary wallet.
                            Selecting this option replaces the previous primary wallet.
                        </p>
                    </div>

                    <hr>

                    <div>
                        <p class="fw-semibold small mb-1">
                            Verification Status
                        </p>

                        <span class="badge bg-warning bg-opacity-10 text-warning mb-2">
                            <i class="bi bi-shield-exclamation me-1"></i>
                            Unverified
                        </span>

                        <p class="text-muted small mb-0">
                            Newly registered wallets are automatically marked as unverified.
                            Verification will be handled through a digital signature process.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
