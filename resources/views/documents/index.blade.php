@extends('layouts.app')

@section('title', 'My Documents')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Document Management</span>
        <h1>My Documents</h1>
        <p>Manage and monitor your uploaded documents.</p>
    </div>

    <a href="{{ route('secretary.documents.create') }}"
       class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-2"></i>
        Upload Document
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="content-card">

    <div class="content-card-header">
        <div>
            <h5>Document List</h5>
            <p>All documents created by your account.</p>
        </div>

        <span class="record-count">
            {{ $documents->total() }} documents
        </span>
    </div>

    <form method="GET"
          action="{{ route('secretary.documents.index') }}"
          class="document-filter mb-4">

        <div class="row g-2">

            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search document title or reference..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="draft"
                        @selected(request('status') === 'draft')>
                        Draft
                    </option>
                    <option value="pending"
                        @selected(request('status') === 'pending')>
                        Pending
                    </option>
                    <option value="in_progress"
                        @selected(request('status') === 'in_progress')>
                        In Progress
                    </option>
                    <option value="approved"
                        @selected(request('status') === 'approved')>
                        Approved
                    </option>
                    <option value="rejected"
                        @selected(request('status') === 'rejected')>
                        Rejected
                    </option>
                    <option value="published"
                        @selected(request('status') === 'published')>
                        Published
                    </option>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-filter" type="submit">
                    Filter
                </button>
            </div>

        </div>

    </form>

    <div class="table-responsive">

        <table class="table app-table">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Type</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>Uploaded</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($documents as $document)

                    <tr>
                        <td>
                            <div class="document-name">
                                <div class="document-icon">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>

                                <div>
                                    <strong>{{ $document->title }}</strong>
                                    <small>
                                        {{ $document->reference_number ?? 'DOC-' . $document->id }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ $document->documentType->name ?? '-' }}
                        </td>

                        <td>
                            v{{ $document->current_version }}
                        </td>

                        <td>
                            <span class="status-badge
                                {{ $document->application_status === 'published' ? 'status-approved' : '' }}
                                {{ $document->application_status === 'rejected' ? 'status-rejected' : '' }}
                                {{ in_array($document->application_status, ['pending', 'in_progress']) ? 'status-pending' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $document->application_status)) }}
                            </span>
                        </td>

                        <td>
                            {{ $document->created_at->format('d M Y') }}
                        </td>

                        <td>
                            <a
                                href="{{ route('secretary.documents.show', $document) }}"
                                class="btn btn-sm btn-outline-primary"
                                title="View details"
                            >
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-folder2-open"></i>
                                <strong>No documents found</strong>
                                <span>
                                    Upload your first document to get started.
                                </span>

                                <a href="{{ route('secretary.documents.create') }}"
                                   class="btn btn-primary-custom mt-2">
                                    Upload Document
                                </a>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">
        {{ $documents->links() }}
    </div>

</div>

@endsection
