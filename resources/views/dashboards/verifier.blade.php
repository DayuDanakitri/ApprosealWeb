
@extends('layouts.app')

@section('title', 'Verifier Dashboard')

@section('content')

<div class="page-heading">

    <div>
        <span class="page-eyebrow">Document Verification</span>

        <h1>Welcome back, {{ auth()->user()->name }}</h1>

        <p>Check published documents and access document verification services.</p>
    </div>

</div>


<div class="row g-3 mb-4">

    @foreach ($stats as $stat)
        <div class="col-md-6">
            @include('components.dashboard.stat-card', ['stat' => $stat])
        </div>
    @endforeach

</div>


<div class="content-card">

    <div class="content-card-header">

        <div>
            <h5>Recently Published Documents</h5>
            <p>Documents available for verification.</p>
        </div>

        <span class="record-count">
            {{ $publishedDocuments->count() }} records
        </span>

    </div>


    <div class="table-responsive">

        <table class="table app-table">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Document Type</th>
                    <th>Created By</th>
                    <th>Published Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($publishedDocuments as $document)

                    <tr>

                        <td>
                            <div class="document-name">

                                <div class="document-icon">
                                    <i class="bi bi-file-earmark-check"></i>
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
                            {{ $document->creator->name ?? '-' }}
                        </td>

                        <td>
                            {{ $document->published_at->format('d M Y') }}
                        </td>

                        <td>
                            <span class="status-badge status-approved">
                                Published
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-shield-check"></i>
                                <strong>No published documents</strong>
                                <span>Published documents will appear here.</span>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
