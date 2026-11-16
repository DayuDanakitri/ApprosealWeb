
@extends('layouts.app')

@section('title', $roleTitle . ' Dashboard')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                ApproSeal
            </h2>

            <p class="text-muted mb-0">
                Secure Document Approval System
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i>
                Logout
            </button>
        </form>

    </div>

    <hr>

    <div class="card border-0 shadow-sm mt-4">

        <div class="card-body p-4">

            <span class="badge bg-primary mb-3">
                {{ $roleTitle }}
            </span>

            <h3 class="fw-bold">
                Welcome, {{ auth()->user()->name }}!
            </h3>

            <p class="text-muted">
                You have successfully logged in to ApproSeal.
            </p>

            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle me-2"></i>

                Authentication successful.

                Your current role is
                <strong>{{ auth()->user()->role->name }}</strong>.
            </div>

        </div>

    </div>

    <div class="mt-4 text-muted small">
        &copy; {{ date('Y') }} ApproSeal.
        Blockchain-Based Document Governance.
    </div>

</div>

@endsection
