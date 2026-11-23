
@extends('layouts.app')

@section('title', 'Secretary Dashboard')

@section('content')

<div class="page-heading">

    <div>
        <span class="page-eyebrow">Workspace Overview</span>

        <h1>Welcome back, {{ auth()->user()->name }}</h1>

        <p>Manage your documents and monitor their approval progress.</p>
    </div>

    <button class="btn btn-primary-custom" type="button" disabled>
        <i class="bi bi-plus-lg me-2"></i>
        New Document
    </button>

</div>


<div class="row g-3 mb-4">

    @foreach ($stats as $stat)
        <div class="col-md-4">
            @include('components.dashboard.stat-card', ['stat' => $stat])
        </div>
    @endforeach

</div>


<div class="content-card">

    <div class="content-card-header">

        <div>
            <h5>Recent Documents</h5>
            <p>Your latest uploaded documents.</p>
        </div>

        <span class="record-count">
            {{ $documents->count() }} records
        </span>

    </div>


    <div class="table-responsive">

        <table class="table app-table">

            <thead>
                <tr>
                    <th>Document</th>
                    <th>Document Type</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>Created</th>
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
                            <span class="status-badge">
                                {{ ucfirst(str_replace('_', ' ', $document->application_status)) }}
                            </span>
                        </td>

                        <td>
                            {{ $document->created_at->format('d M Y') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-file-earmark"></i>
                                <strong>No documents yet</strong>
                                <span>Your uploaded documents will appear here.</span>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
