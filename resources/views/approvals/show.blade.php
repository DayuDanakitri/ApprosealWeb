@extends('layouts.app')

@section('title', 'Review Document')

@section('content')

@php
    $document = $documentApproval->documentVersion->document;
    $version = $documentApproval->documentVersion;
@endphp

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Approval Management</span>
        <h1>Review Document</h1>
        <p>Review the document before making your approval decision.</p>
    </div>

    <a
        href="{{ route('approver.approvals.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-2"></i>
        Back
    </a>
</div>

<div class="row g-3">

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
                    <strong>
                        {{ $document->documentType->name ?? '-' }}
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Version</span>
                    <strong>v{{ $version->version_number }}</strong>
                </div>

                <div class="detail-item">
                    <span>Approval Step</span>
                    <strong>
                        {{ $documentApproval->approvalStep->name }}
                    </strong>
                </div>

                <div class="detail-item">
                    <span>Uploaded By</span>
                    <strong>
                        {{ $version->uploader->name ?? '-' }}
                    </strong>
                </div>

            </div>

            <div class="detail-description">
                <span>Description</span>
                <p>
                    {{ $document->description ?: 'No description provided.' }}
                </p>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('approver.approvals.download', $documentApproval) }}"
                    class="btn btn-outline-primary"
                >
                    <i class="bi bi-download me-2"></i>
                    Download PDF
                </a>
            </div>

        </div>

        {{-- DOCUMENT INTEGRITY --}}
        <div class="content-card mb-3">

            <div class="content-card-header">
                <div>
                    <h5>Document Integrity</h5>
                    <p>SHA-256 hash of the uploaded document.</p>
                </div>

                <span class="integrity-icon">
                    <i class="bi bi-shield-check"></i>
                </span>
            </div>

            <div class="hash-container">
                <div class="hash-label">SHA-256</div>

                <code class="hash-value">
                    {{ $version->document_hash }}
                </code>
            </div>

            <div class="blockchain-note mt-3">
                <i class="bi bi-info-circle me-2"></i>
                This hash is currently recorded in the application database.
                Blockchain registration has not been performed yet.
            </div>

        </div>

        {{-- APPROVAL TIMELINE --}}
        <div class="content-card">

            <div class="content-card-header">
                <div>
                    <h5>Approval Timeline</h5>
                    <p>View the complete approval process for this document version.</p>
                </div>
            </div>

            @include('components.approval.timeline', [
                'document' => $document,
                'version' => $version,
            ])

        </div>

    </div>

    <div class="col-lg-4">

        {{-- APPROVAL DECISION --}}
        <div class="content-card">

            <div class="content-card-header">
                <div>
                    <h5>Approval Decision</h5>
                    <p>Submit your decision for this document.</p>
                </div>
            </div>

            @if (
                $documentApproval->decision === null &&
                in_array($document->application_status, ['pending', 'in_progress'], true)
            )

                <form
                    method="POST"
                    action="{{ route('approver.approvals.decide', $documentApproval) }}"
                >
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Comments / Rejection Reason
                        </label>

                        <textarea
                            name="comments"
                            class="form-control"
                            rows="5"
                            placeholder="Enter comments or reason if rejecting..."
                        >{{ old('comments') }}</textarea>

                        @error('comments')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @error('decision')
                        <div class="text-danger small mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="d-grid gap-2">

                        <button
                            type="submit"
                            name="decision"
                            value="approved"
                            class="btn btn-success"
                            onclick="return confirm('Are you sure you want to approve this document?')"
                        >
                            <i class="bi bi-check-circle me-2"></i>
                            Approve Document
                        </button>

                        <button
                            type="submit"
                            name="decision"
                            value="rejected"
                            class="btn btn-outline-danger"
                            onclick="return confirm('Are you sure you want to reject this document?')"
                        >
                            <i class="bi bi-x-circle me-2"></i>
                            Reject Document
                        </button>

                    </div>

                </form>

            @else

                <div class="alert alert-secondary mb-0">
                    This approval is no longer awaiting a decision.
                </div>

            @endif

        </div>

    </div>

</div>

@endsection
