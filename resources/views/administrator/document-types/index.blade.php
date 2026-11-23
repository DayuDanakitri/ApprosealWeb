@extends('layouts.app')

@section('title', 'Document Types')

@section('content')

<div class="container-fluid px-4 py-4">

    {{-- Page Heading --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Document Types</h4>
            <p class="text-muted mb-0">
                Manage the types of documents supported by ApproSeal.
            </p>
        </div>

        <a href="{{ route('administrator.document-types.create') }}"
           class="btn btn-primary">
            <i class="bi bi-file-earmark-plus me-2"></i>
            Add Document Type
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
                            <p class="text-muted small mb-1">Total Types</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalTypes) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-file-earmark-text fs-4"></i>
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
                            <p class="text-muted small mb-1">Active Types</p>
                            <h3 class="fw-bold text-success mb-0">
                                {{ number_format($activeTypes) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle fs-4"></i>
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
                            <p class="text-muted small mb-1">Inactive Types</p>
                            <h3 class="fw-bold text-warning mb-0">
                                {{ number_format($inactiveTypes) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-pause-circle fs-4"></i>
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
                            <p class="text-muted small mb-1">Types in Use</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($typesInUse) }}
                            </h3>
                        </div>

                        <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                            <i class="bi bi-folder-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Document Types Table --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-1">Registered Document Types</h5>
            <p class="text-muted small mb-0">
                View and manage the document categories available in the system.
            </p>
        </div>

        <div class="card-body px-4 pb-4">

            {{-- Search and Filter --}}
            <form method="GET"
                  action="{{ route('administrator.document-types.index') }}"
                  class="row g-2 mb-4">

                <div class="col-lg-6">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search name, code, or description..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-lg-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>

                        <option value="active"
                            @selected(request('status') === 'active')>
                            Active
                        </option>

                        <option value="inactive"
                            @selected(request('status') === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search"></i>
                    </button>

                    <a href="{{ route('administrator.document-types.index') }}"
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
                            <th>Document Type</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($documentTypes as $documentType)

                            <tr>
                                {{-- Name --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $documentType->name }}
                                    </div>

                                    <div class="text-muted small">
                                        ID: {{ $documentType->id }}
                                    </div>
                                </td>

                                {{-- Code --}}
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary font-monospace">
                                        {{ $documentType->code }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td>
                                    <span class="text-muted small">
                                        {{ $documentType->description ?: 'No description' }}
                                    </span>
                                </td>

                                {{-- Document Count --}}
                                <td>
                                    <span class="fw-semibold">
                                        {{ $documentType->documents_count }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if ($documentType->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-pause-circle me-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="text-end">
                                    <a href="{{ route('administrator.document-types.edit', $documentType) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-file-earmark-text fs-1 text-muted"></i>

                                    <p class="fw-semibold mt-3 mb-1">
                                        No document types found
                                    </p>

                                    <p class="text-muted small mb-0">
                                        No document types match your search or filter.
                                    </p>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $documentTypes->links() }}
            </div>

        </div>
    </div>

</div>

@endsection
