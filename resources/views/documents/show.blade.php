@extends('layouts.app')

@section('title', 'Document Details')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Document Management</span>
        <h1>Document Details</h1>
        <p>View document information, file details, and revision history.</p>
    </div>

    <a href="{{ route('secretary.documents.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Documents
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->has('document'))
    <div class="alert alert-danger">
        {{ $errors->first('document') }}
    </div>
@endif

{{-- DRAFT STATUS --}}
@if ($document->application_status === 'draft')
    <div class="content-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="mb-1">Ready for Approval?</h5>
                <p class="text-muted small mb-0">
                    Submit this document to start its approval process.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('secretary.documents.submit', $document) }}"
                onsubmit="return confirm('Submit this document for approval?')"
            >
                @csrf

                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-send me-2"></i>
                    Submit for Approval
                </button>
            </form>
        </div>
    </div>
@endif

{{-- APPROVAL IN PROGRESS --}}
@if (in_array($document->application_status, ['pending', 'in_progress']))
    <div class="alert alert-info">
        <i class="bi bi-hourglass-split me-2"></i>
        This document is currently undergoing the approval process.
    </div>
@endif

{{-- APPROVED STATUS --}}
@if ($document->application_status === 'approved')
    <div class="content-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="mb-1 text-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Document Fully Approved
                </h5>

                <p class="text-muted small mb-0">
                    All approval stages have been completed.
                    You can now publish this document.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('secretary.documents.publish', $document) }}"
                onsubmit="return confirm('Are you sure you want to publish this document? This action cannot be undone.')"
            >
                @csrf

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-megaphone me-2"></i>
                    Publish Document
                </button>
            </form>
        </div>
    </div>
@endif

{{-- PUBLISHED STATUS --}}
@if ($document->application_status === 'published')
    <div class="alert alert-success">
        <h6 class="mb-1">
            <i class="bi bi-patch-check-fill me-2"></i>
            Document Published
        </h6>

        <p class="mb-0">
            This document has been successfully published.

            @if ($document->published_at)
                <br>
                <small>
                    Published on:
                    {{ $document->published_at->format('d M Y, H:i') }}
                </small>
            @endif
        </p>
    </div>
@endif

{{-- REJECTED STATUS --}}
@if ($document->application_status === 'rejected')
    <div class="alert alert-danger">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h6 class="mb-1">
                    <i class="bi bi-x-circle me-2"></i>
                    Document Rejected
                </h6>

                <p class="mb-0">
                    Please review the rejection reason and upload a revised version.
                </p>
            </div>

            <a
                href="{{ route('secretary.documents.revise', $document) }}"
                class="btn btn-danger"
            >
                <i class="bi bi-pencil-square me-2"></i>
                Revise Document
            </a>
        </div>
    </div>
@endif

<div class="row g-3">

    {{-- MAIN DOCUMENT CONTENT --}}
    <div class="col-lg-8">

        {{-- DOCUMENT DETAILS --}}
        <div class="content-card mb-3">

            <div class="content-card-header">
                <div>
                    <h5>{{ $document->title }}</h5>
                    <p>
                        {{ $document->reference_number ?? 'DOC-' . $document->id }}
                    </p>
                </div>

                <span class="status-badge">
                    {{ ucfirst(str_replace('_', ' ', $document->application_status)) }}
                </span>
            </div>

            <div class="detail-grid">

                <div class="detail-item">
                    <span>Document Type</span>
                    <strong>{{ $document->documentType->name ?? '-' }}</strong>
                </div>

                <div class="detail-item">
                    <span>Current Version</span>
                    <strong>v{{ $document->current_version }}</strong>
                </div>

                <div class="detail-item">
                    <span>Approval Policy</span>
                    <strong>{{ $document->approvalPolicy->name ?? 'Not selected' }}</strong>
                </div>

                <div class="detail-item">
                    <span>Uploaded Date</span>
                    <strong>{{ $document->created_at->format('d M Y, H:i') }}</strong>
                </div>

                @if ($document->published_at)
                    <div class="detail-item">
                        <span>Published Date</span>
                        <strong>
                            {{ $document->published_at->format('d M Y, H:i') }}
                        </strong>
                    </div>
                @endif

            </div>

            <div class="detail-description">
                <span>Description</span>
                <p>{{ $document->description ?: 'No description provided.' }}</p>
            </div>

        </div>

        {{-- DOCUMENT INTEGRITY --}}
        <div class="content-card mb-3">

            <div class="content-card-header">
                <div>
                    <h5>Document Integrity</h5>
                    <p>SHA-256 hash generated from the current PDF.</p>
                </div>

                <span class="integrity-icon">
                    <i class="bi bi-shield-check"></i>
                </span>
            </div>

            @if ($currentVersion)

                <div class="hash-container">

                    <div class="hash-label">
                        SHA-256
                    </div>

                    <code class="hash-value">
                        {{ $currentVersion->document_hash }}
                    </code>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary mt-3"
                        id="copyHash"
                    >
                        <i class="bi bi-copy me-1"></i>
                        Copy Hash
                    </button>

                </div>

            @else

                <p class="text-muted">
                    Document version information is unavailable.
                </p>

            @endif

            <div class="blockchain-note mt-3">
                <i class="bi bi-info-circle me-2"></i>
                Blockchain registration has not been performed yet.
            </div>

        </div>

        {{-- VERSION HISTORY AND APPROVAL TIMELINE --}}
        <div class="content-card">

            <div class="content-card-header">
                <div>
                    <h5>Version History</h5>
                    <p>All uploaded versions and their approval progress.</p>
                </div>
            </div>

            @forelse ($document->versions->sortByDesc('version_number') as $version)

                <div class="border rounded-3 p-3 mb-3">

                    {{-- VERSION HEADER --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Version {{ $version->version_number }}</strong>

                            @if ($version->version_number == $document->current_version)
                                <span class="badge bg-primary ms-2">Current</span>
                            @endif
                        </div>

                        <small class="text-muted">
                            {{ $version->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>

                    <div class="small text-muted mb-2">
                        {{ $version->original_filename }}
                    </div>

                    <div class="small mb-2">
                        <strong>SHA-256:</strong>
                        <code class="text-break">{{ $version->document_hash }}</code>
                    </div>

                    <div class="small text-muted">
                        Uploaded by:
                        {{ $version->uploader->name ?? '-' }}
                    </div>

                    <hr>

                    {{-- APPROVAL TIMELINE --}}
                    <div class="mt-3">

                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Approval Timeline
                        </h6>

                        @include('components.approval.timeline', [
                            'document' => $document,
                            'version' => $version,
                        ])

                    </div>

                </div>

            @empty

                <p class="text-muted mb-0">
                    No document versions found.
                </p>

            @endforelse

        </div>

    </div>

    {{-- SIDEBAR --}}
    <div class="col-lg-4">

        {{-- FILE INFORMATION --}}
        <div class="content-card mb-3">

            <div class="content-card-header">
                <div>
                    <h5>File Information</h5>
                    <p>Current uploaded document metadata.</p>
                </div>
            </div>

            @if ($currentVersion)

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
                    <span>File Size</span>
                    <strong>
                        {{ number_format($currentVersion->file_size / 1024, 2) }} KB
                    </strong>
                </div>

                <div class="info-row">
                    <span>Version</span>
                    <strong>v{{ $currentVersion->version_number }}</strong>
                </div>

                <div class="info-row">
                    <span>MIME Type</span>
                    <strong>{{ $currentVersion->mime_type }}</strong>
                </div>

                <div class="info-row">
                    <span>Uploaded By</span>
                    <strong>{{ $currentVersion->uploader->name ?? '-' }}</strong>
                </div>

            @else

                <p class="text-muted">
                    No file information available.
                </p>

            @endif

        </div>

        {{-- QR CODE VERIFICATION --}}
        @if ($document->application_status === 'published' && $qrCode)

            <div class="content-card">

                <div class="content-card-header">
                    <div>
                        <h5>QR Code Verification</h5>
                        <p>Scan this QR Code to verify the published document.</p>
                    </div>
                </div>

                <div class="text-center">

                    <div
                        class="qr-code-container p-3 bg-white border rounded-3 d-inline-block mb-3"
                        id="verificationQr"
                    >
                        {!! $qrCode !!}
                    </div>

                    <p class="text-muted small mb-3">
                        Scan this QR Code to access the public verification page.
                    </p>

                    <div class="d-grid gap-2">

                        <button
                            type="button"
                            class="btn btn-primary-custom"
                            id="downloadQr"
                        >
                            <i class="bi bi-download me-2"></i>
                            Download QR Code
                        </button>

                        <a
                            href="{{ $verificationUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-box-arrow-up-right me-2"></i>
                            Open Verification Page
                        </a>

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // COPY DOCUMENT HASH
    const copyButton = document.getElementById('copyHash');

    if (copyButton) {

        copyButton.addEventListener('click', async function () {

            const hashElement = document.querySelector('.hash-value');

            if (!hashElement) {
                return;
            }

            const hash = hashElement.textContent.trim();

            try {
                await navigator.clipboard.writeText(hash);

                copyButton.innerHTML =
                    '<i class="bi bi-check2 me-1"></i> Copied';

            } catch (error) {
                alert('Unable to copy hash. Please copy it manually.');
            }

        });

    }

    // DOWNLOAD QR CODE
    const downloadQrButton = document.getElementById('downloadQr');

    if (downloadQrButton) {

        downloadQrButton.addEventListener('click', function () {

            const qrContainer = document.getElementById('verificationQr');
            const svg = qrContainer?.querySelector('svg');

            if (!svg) {
                alert('QR Code is unavailable.');
                return;
            }

            const svgData = new XMLSerializer().serializeToString(svg);

            const blob = new Blob(
                [svgData],
                { type: 'image/svg+xml;charset=utf-8' }
            );

            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = 'approseal-verification-qr.svg';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            URL.revokeObjectURL(url);

        });

    }

});
</script>
@endpush
