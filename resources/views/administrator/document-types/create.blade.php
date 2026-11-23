@extends('layouts.app')

@section('title', 'Add Document Type')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="mb-4">
        <a href="{{ route('administrator.document-types.index') }}"
           class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Document Types
        </a>

        <h4 class="fw-bold mt-3 mb-1">Add Document Type</h4>

        <p class="text-muted mb-0">
            Register a new document category in ApproSeal.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-1">Document Type Information</h5>
                    <p class="text-muted small mb-0">
                        Enter the details of the new document type.
                    </p>
                </div>

                <div class="card-body p-4">

                    <form method="POST"
                          action="{{ route('administrator.document-types.store') }}">

                        @csrf

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Document Type Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Example: Surat Keputusan"
                                value="{{ old('name') }}"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Code --}}
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">
                                Document Code
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="code"
                                name="code"
                                class="form-control text-uppercase @error('code') is-invalid @enderror"
                                placeholder="Example: SK"
                                value="{{ old('code') }}"
                                maxlength="50"
                                required
                            >

                            <div class="form-text">
                                Use a unique code containing letters, numbers,
                                hyphens, or underscores.
                            </div>

                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="4"
                                placeholder="Describe this document type..."
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Approval Policy --}}
                        <div class="mb-4">
                            <label for="approval_policy_id"
                                   class="form-label fw-semibold">
                                Approval Policy
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="approval_policy_id"
                                id="approval_policy_id"
                                class="form-select @error('approval_policy_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select Approval Policy</option>

                                @foreach ($approvalPolicies as $policy)
                                    <option
                                        value="{{ $policy->id }}"
                                        @selected(old('approval_policy_id') == $policy->id)
                                    >
                                        {{ $policy->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('approval_policy_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Select the approval workflow required
                                for this document type.
                            </div>

                            {{-- Policy Steps Preview --}}
                            <div id="policy-preview"
                                 class="border rounded-3 p-3 mt-3 d-none">

                                <div class="fw-semibold small mb-2">
                                    Approval Stages
                                </div>

                                <div id="policy-steps"
                                     class="small text-muted">
                                </div>

                            </div>
                        </div>

                        {{-- Initial Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Initial Status
                            </label>

                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    role="switch"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    @checked(old('is_active'))
                                >

                                <label class="form-check-label" for="is_active">
                                    Activate this document type
                                </label>
                            </div>

                            <div class="form-text">
                                Inactive types will not be available when
                                Secretary uploads a new document.
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('administrator.document-types.index') }}"
                               class="btn btn-light border">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>
                                Create Document Type
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>

        {{-- Information Panel --}}
        <div class="col-lg-4 mt-4 mt-lg-0">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle text-primary fs-5 me-2"></i>
                        <h6 class="fw-bold mb-0">Important Information</h6>
                    </div>

                    <ul class="text-muted small ps-3 mb-0">
                        <li class="mb-2">
                            Document codes must be unique.
                        </li>

                        <li class="mb-2">
                            Codes cannot be changed after the type has been used.
                        </li>

                        <li class="mb-2">
                            Every document type must have an approval policy.
                        </li>

                        <li>
                            The selected policy determines the approval
                            stages for newly submitted documents.
                        </li>
                    </ul>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
@php
    $policyPreviewData = $approvalPolicies->map(function ($policy) {
        return [
            'id' => $policy->id,
            'steps' => $policy->steps->map(function ($step) {
                return [
                    'order' => $step->step_order,
                    'name' => $step->name,
                ];
            })->values(),
        ];
    })->values();
@endphp

<script>
    const approvalPolicies = {{ Illuminate\Support\Js::from($policyPreviewData) }};

    const policySelect = document.getElementById('approval_policy_id');
    const policyPreview = document.getElementById('policy-preview');
    const policySteps = document.getElementById('policy-steps');

    function updatePolicyPreview() {
        const selectedId = Number(policySelect.value);

        const selectedPolicy = approvalPolicies.find(
            policy => Number(policy.id) === selectedId
        );

        policySteps.innerHTML = '';

        if (!selectedPolicy) {
            policyPreview.classList.add('d-none');
            return;
        }

        selectedPolicy.steps.forEach((step, index) => {
            const item = document.createElement('div');

            item.className = 'd-flex align-items-center gap-2 mb-2';

            const number = document.createElement('span');
            number.className = 'badge bg-primary rounded-pill';
            number.textContent = step.order;

            const name = document.createElement('span');
            name.textContent = step.name;

            item.appendChild(number);
            item.appendChild(name);
            policySteps.appendChild(item);

            if (index < selectedPolicy.steps.length - 1) {
                const arrow = document.createElement('div');
                arrow.className = 'text-muted ms-2 mb-2';
                arrow.innerHTML = '<i class="bi bi-arrow-down"></i>';

                policySteps.appendChild(arrow);
            }
        });

        policyPreview.classList.remove('d-none');
    }

    policySelect.addEventListener('change', updatePolicyPreview);

    updatePolicyPreview();
</script>
@endpush
