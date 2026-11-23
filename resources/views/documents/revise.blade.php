@extends('layouts.app')

@section('title', 'Revise Document')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Document Management</span>
        <h1>Revise Document</h1>
        <p>Upload a revised PDF document to address the rejection.</p>
    </div>

    <a href="{{ route('secretary.documents.show', $document) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Details
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please check the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">

    <div class="col-lg-7">

        <div class="content-card mb-3">
            <div class="content-card-header">
                <div>
                    <h5>Document Information</h5>
                    <p>Review the document before uploading a revision.</p>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span>Document Title</span>
                    <strong>{{ $document->title }}</strong>
                </div>

                <div class="detail-item">
                    <span>Document Type</span>
                    <strong>{{ $document->documentType->name ?? '-' }}</strong>
                </div>

                <div class="detail-item">
                    <span>Current Version</span>
                    <strong>v{{ $currentVersion->version_number }}</strong>
                </div>

                <div class="detail-item">
                    <span>New Version</span>
                    <strong>v{{ $currentVersion->version_number + 1 }}</strong>
                </div>
            </div>
        </div>

        <div class="content-card mb-3">
            <div class="content-card-header">
                <div>
                    <h5>Rejection Reason</h5>
                    <p>Review the feedback from the approver.</p>
                </div>
            </div>

            @forelse ($rejectionReasons as $rejection)
                <div class="alert alert-danger mb-2">
                    <div class="fw-semibold">
                        {{ $rejection->approver->name ?? 'Approver' }}
                    </div>

                    <div class="small text-muted mb-2">
                        {{ $rejection->approvalStep->name ?? 'Approval Step' }}
                        @if ($rejection->decided_at)
                            · {{ $rejection->decided_at->format('d M Y, H:i') }}
                        @endif
                    </div>

                    <p class="mb-0">
                        {{ $rejection->comments ?: 'No rejection comment provided.' }}
                    </p>
                </div>
            @empty
                <p class="text-muted mb-0">
                    No rejection comments were found for this version.
                </p>
            @endforelse
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <div>
                    <h5>Upload Revised Document</h5>
                    <p>
                        The previous PDF will be preserved as an older version.
                    </p>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('secretary.documents.revise.store', $document) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">
                        Revised PDF File <span class="text-danger">*</span>
                    </label>

                    <input
                        type="file"
                        name="file"
                        id="file"
                        class="form-control @error('file') is-invalid @enderror"
                        accept=".pdf,application/pdf"
                        required
                    >

                    <div class="form-text">
                        PDF only. Maximum file size: 10 MB.
                    </div>

                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    After uploading, the document will return to Draft.
                    You must submit it again to restart the approval process.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a
                        href="{{ route('secretary.documents.show', $document) }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary-custom"
                        onclick="return confirm('Upload this revised document as a new version?')"
                    >
                        <i class="bi bi-upload me-2"></i>
                        Upload Revision
                    </button>
                </div>
            </form>
        </div>

    </div>

    <div class="col-lg-5">

        <div class="content-card">
            <div class="content-card-header">
                <div>
                    <h5>Previous File</h5>
                    <p>Current rejected document version.</p>
                </div>
            </div>

            <div class="file-preview-icon">
                <i class="bi bi-file-earmark-pdf"></i>
            </div>

            <div class="text-center mb-4">
                <strong class="file-original-name">
                    {{ $currentVersion->original_filename }}
                </strong>

                <small class="d-block text-muted mt-1">
                    PDF Document
                </small>
            </div>

            <div class="info-row">
                <span>Version</span>
                <strong>v{{ $currentVersion->version_number }}</strong>
            </div>

            <div class="info-row">
                <span>File Size</span>
                <strong>
                    {{ number_format($currentVersion->file_size / 1024, 2) }} KB
                </strong>
            </div>

            <div class="info-row">
                <span>Uploaded By</span>
                <strong>
                    {{ $currentVersion->uploader->name ?? '-' }}
                </strong>
            </div>

            <div class="blockchain-note mt-3">
                <i class="bi bi-shield-check me-2"></i>
                The new PDF will receive a new SHA-256 hash.
            </div>
        </div>

    </div>

</div>

@endsection
