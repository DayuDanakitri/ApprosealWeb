@extends('layouts.app')

@section('title', 'Audit Log Detail')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Audit Log Detail</h4>
            <p class="text-muted mb-0">
                Detailed information about a recorded system activity.
            </p>
        </div>

        <a href="{{ route('administrator.audit-logs.index') }}"
           class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Audit Logs
        </a>
    </div>

    {{-- Activity Information --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 48px; height: 48px; background: #f0eaff;">
                    <i class="bi bi-journal-text fs-4"
                       style="color: #7952b3;"></i>
                </div>

                <div>
                    <h5 class="fw-bold mb-1">
                        {{ ucwords(str_replace('_', ' ', $auditLog->action)) }}
                    </h5>
                    <span class="text-muted small">
                        Audit Log #{{ $auditLog->id }}
                    </span>
                </div>
            </div>

            <hr>

            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small mb-1">Activity</label>
                    <div class="fw-semibold">
                        {{ $auditLog->action }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small mb-1">Date & Time</label>
                    <div class="fw-semibold">
                        {{ $auditLog->created_at?->format('d F Y, H:i:s') ?? '-' }}
                    </div>
                </div>

                <div class="col-12">
                    <label class="text-muted small mb-1">Description</label>
                    <div>
                        {{ $auditLog->description ?? '-' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- User Information --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-person me-2"></i>
                User Information
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small mb-1">Name</label>
                    <div class="fw-semibold">
                        {{ $auditLog->user?->name ?? 'System' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small mb-1">Email</label>
                    <div class="fw-semibold">
                        {{ $auditLog->user?->email ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small mb-1">IP Address</label>
                    <div class="fw-semibold">
                        {{ $auditLog->ip_address ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small mb-1">User Agent</label>
                    <div class="text-break">
                        {{ $auditLog->user_agent ?? '-' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Related Subject --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                Related Subject
            </h5>
        </div>

        <div class="card-body p-4">

            @if ($auditLog->subject)
                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="text-muted small mb-1">
                            Subject Type
                        </label>
                        <div class="fw-semibold">
                            {{ class_basename($auditLog->subject_type) }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small mb-1">
                            Subject ID
                        </label>
                        <div class="fw-semibold">
                            {{ $auditLog->subject_id }}
                        </div>
                    </div>

                    @if ($auditLog->subject instanceof \App\Models\Document)
                        <div class="col-12">
                            <label class="text-muted small mb-1">
                                Document Title
                            </label>
                            <div class="fw-semibold">
                                {{ $auditLog->subject->title }}
                            </div>
                        </div>
                    @endif

                </div>
            @else
                <p class="text-muted mb-0">
                    No related subject information available.
                </p>
            @endif

        </div>
    </div>

    {{-- Activity Properties --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-braces me-2"></i>
                Activity Properties
            </h5>
        </div>

        <div class="card-body p-4">

            @if (!empty($auditLog->properties))
                <pre class="bg-light rounded-3 p-3 mb-0 text-wrap"
                     style="font-size: 13px;">{{ json_encode(
                         $auditLog->properties,
                         JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                     ) }}</pre>
            @else
                <p class="text-muted mb-0">
                    No additional properties recorded.
                </p>
            @endif

        </div>
    </div>

</div>
@endsection
