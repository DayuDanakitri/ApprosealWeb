@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Edit User</h4>
            <p class="text-muted mb-0">
                Update the account information and access role.
            </p>
        </div>

        <a href="{{ route('administrator.users.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Users
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

    {{-- Edit User Form --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-1">User Information</h5>
            <p class="text-muted small mb-0">
                Update the details for this account.
            </p>
        </div>

        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('administrator.users.update', $user) }}">

                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Full Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Email Address <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">
                            User Role <span class="text-danger">*</span>
                        </label>

                        @if ((int) $user->id === (int) Auth::id())
                            <input type="hidden"
                                   name="role_id"
                                   value="{{ $user->role_id }}">

                            <select class="form-select" disabled>
                                <option selected>
                                    {{ $user->role->name ?? 'No Role' }}
                                </option>
                            </select>

                            <small class="text-muted">
                                You cannot change your own administrator role.
                            </small>
                        @else
                            <select
                                name="role_id"
                                id="role_id"
                                class="form-select @error('role_id') is-invalid @enderror"
                                required
                            >
                                @foreach ($roles as $role)
                                    <option
                                        value="{{ $role->id }}"
                                        @selected(old('role_id', $user->role_id) == $role->id)
                                    >
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label">
                            New Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Leave blank to keep current password"
                        >

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Minimum 8 characters if you want to change it.
                        </small>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            placeholder="Re-enter new password"
                        >
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('administrator.users.index') }}"
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

@endsection
