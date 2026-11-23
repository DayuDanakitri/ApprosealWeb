@extends('layouts.app')

@section('title', 'Document Verification')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-shield-check text-success"
                               style="font-size: 3rem;"></i>
                        </div>

                        <h3 class="fw-bold mb-2">
                            Document Verification
                        </h3>

                        <p class="text-muted mb-0">
                            ApproSeal Public Verification System
                        </p>
                    </div>

                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>
                            <strong>Document Found</strong>
                            <div class="small">
                                This document has been officially published.
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-uppercase text-muted mb-3">
                        Document Information
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Document Title</th>
                                <td>{{ $document->title }}</td>
                            </tr>
                            <tr>
                                <th>Reference Number</th>
                                <td>{{ $document->reference_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Document Type</th>
                                <td>{{ $document->documentType?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Published Date</th>
                                <td>
                                    {{ $document->published_at?->format('d F Y, H:i') ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Current Version</th>
                                <td>Version {{ $document->current_version }}</td>
                            </tr>
                        </table>
                    </div>

                    <hr>

                    <h6 class="fw-bold text-uppercase text-muted mb-3">
                        Document Integrity
                    </h6>

                    @if ($currentVersion)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                SHA-256 Document Hash
                            </label>

                            <div class="p-3 bg-light rounded border"
                                 style="word-break: break-all;">
                                <code>{{ $currentVersion->document_hash ?? 'Hash unavailable' }}</code>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Current document version information is unavailable.
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <span class="badge bg-success px-3 py-2">
                            Published
                        </span>

                        <p class="text-muted small mt-3 mb-0">
                            Verified through ApproSeal document records.
                            Blockchain verification will be available
                            after smart contract integration.
                        </p>
                    </div>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                ApproSeal &copy; {{ date('Y') }}
            </p>

        </div>
    </div>
</div>
@endsection
