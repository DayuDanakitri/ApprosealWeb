@extends('layouts.app')

@section('title', 'Edit Wallet')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Edit Wallet</h4>
            <p class="text-muted mb-0">
                Update the wallet information connected to an account.
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

        {{-- Edit Form --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-1">Wallet Information</h5>
                    <p class="text-muted small mb-0">
                        Update the registered wallet details.
                    </p>
                </div>

                <div class="card-body p-4">

                    <form method="POST"
                          action="{{ route('administrator.wallets.update', $wallet) }}">

                        @csrf
                        @method('PUT')

                        {{-- Account Owner --}}
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
                                @foreach ($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        @selected(old('user_id', $wallet->user_id) == $user->id)
                                    >
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Wallet Address --}}
                        <div class="mb-3">
                            <label for="wallet_address" class="form-label">
                                Wallet Address <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="wallet_address"
                                id="wallet_address"
                                class="form-control font-monospace @error('wallet_address') is-invalid @enderror"
                                value="{{ old('wallet_address', $wallet->wallet_address) }}"
                                maxlength="42"
                                required
                            >

                            @error('wallet_address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted">
                                Enter a valid Ethereum-compatible wallet address.
                            </small>
                        </div>

                        <div class="row g-3">

                            {{-- Network --}}
                            <div class="col-md-6">
                                <label for="network_name" class="form-label">
                                    Network Name
                                </label>

                                <input
                                    type="text"
                                    name="network_name"
                                    id="network_name"
                                    class="form-control @error('network_name') is-invalid @enderror"
                                    value="{{ old('network_name', $wallet->network_name) }}"
                                    placeholder="e.g. Sepolia"
                                >

                                @error('network_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Chain ID --}}
                            <div class="col-md-6">
                                <label for="chain_id" class="form-label">
                                    Chain ID
                                </label>

                                <input
                                    type="number"
                                    name="chain_id"
                                    id="chain_id"
                                    class="form-control @error('chain_id') is-invalid @enderror"
                                    value="{{ old('chain_id', $wallet->chain_id) }}"
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

                        {{-- Primary Wallet --}}
                        <div class="form-check mt-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_primary"
                                value="1"
                                id="is_primary"
                                @checked(old('is_primary', $wallet->is_primary))
                            >

                            <label class="form-check-label" for="is_primary">
                                Set as primary wallet
                            </label>

                            <div class="text-muted small">
                                This wallet will become the primary wallet for its selected owner.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('administrator.wallets.index') }}"
                               class="btn btn-light border">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        {{-- Current Wallet Information --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <h6 class="fw-bold mb-3">
                        Current Wallet Status
                    </h6>

                    <p class="text-muted small mb-1">
                        Current Owner
                    </p>

                    <p class="fw-semibold mb-3">
                        {{ $wallet->user->name }}
                    </p>

                    <p class="text-muted small mb-1">
                        Verification
                    </p>

                    <div class="mb-3">
                        @if ($wallet->is_verified)
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-shield-check me-1"></i>
                                Verified
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-shield-exclamation me-1"></i>
                                Unverified
                            </span>
                        @endif
                    </div>

                    <p class="text-muted small mb-1">
                        Primary Status
                    </p>

                    <div class="mb-3">
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
                    </div>

                    <p class="text-muted small mb-1">
                        Registered At
                    </p>

                    <p class="small mb-0">
                        {{ $wallet->created_at?->format('d M Y, H:i') ?? '-' }}
                    </p>

                </div>

            </div>

            <div class="alert alert-info mt-3 small">
                <i class="bi bi-info-circle me-2"></i>

                Changing the wallet address will automatically reset its verification status.
                Ownership verification must be performed again.
            </div>

        </div>

    </div>

</div>

@endsection
