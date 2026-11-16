@extends('layouts.guest')

@section('title', 'Login')

@section('content')

<div class="login-page">

    <div class="login-container">

        <!-- Brand Section -->
        <div class="login-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>

            <h1>ApproSeal</h1>

            <p>
                Secure Document Approval & Governance System
            </p>
        </div>

        <!-- Login Card -->
        <div class="login-card">

            <div class="login-heading">
                <h2>Welcome Back</h2>
                <p>Sign in to continue to your workspace.</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- General Error Message -->
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.process') }}">

                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email Address
                    </label>

                    <div class="input-group login-input">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>

                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    @error('email')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="input-group login-input">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            class="input-group-text password-toggle"
                            type="button"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn login-button w-100">
                    Sign In
                    <i class="bi bi-arrow-right ms-2"></i>
                </button>

            </form>

        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p>
                &copy; {{ date('Y') }} ApproSeal.
                All rights reserved.
            </p>

            <span>
                Blockchain-Based Document Governance
            </span>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';

        passwordIcon.classList.toggle('bi-eye', !isPassword);
        passwordIcon.classList.toggle('bi-eye-slash', isPassword);

        togglePassword.setAttribute(
            'aria-label',
            isPassword ? 'Hide password' : 'Show password'
        );
    });
</script>
@endpush
