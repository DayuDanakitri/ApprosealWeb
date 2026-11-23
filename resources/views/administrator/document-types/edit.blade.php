@extends('layouts.app')

@section('title', 'Edit Document Type')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Document Type</h4>
            <p class="text-muted mb-0">
                Perbarui informasi jenis dokumen dan approval policy.
            </p>
        </div>

        <a href="{{ route('administrator.document-types.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">
                Terdapat kesalahan pada input:
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <form
                        action="{{ route('administrator.document-types.update', $documentType->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Document Type Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name', $documentType->name) }}"
                                placeholder="Contoh: Proposal"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">
                                Document Code
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control @error('code') is-invalid @enderror"
                                id="code"
                                name="code"
                                value="{{ old('code', $documentType->code) }}"
                                placeholder="Contoh: PROP"
                                required
                            >

                            <small class="text-muted">
                                Kode tidak dapat diubah apabila jenis dokumen
                                sudah digunakan oleh dokumen yang tersimpan.
                            </small>

                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Masukkan deskripsi jenis dokumen..."
                            >{{ old('description', $documentType->description) }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="approval_policy_id" class="form-label fw-semibold">
                                Approval Policy
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select @error('approval_policy_id') is-invalid @enderror"
                                id="approval_policy_id"
                                name="approval_policy_id"
                                required
                            >
                                <option value="">Select Approval Policy</option>

                                @foreach ($approvalPolicies as $policy)
                                    <option
                                        value="{{ $policy->id }}"
                                        @selected(
                                            old(
                                                'approval_policy_id',
                                                $documentType->approval_policy_id
                                            ) == $policy->id
                                        )
                                    >
                                        {{ $policy->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('approval_policy_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Policy ini akan digunakan untuk dokumen baru
                                yang menggunakan jenis dokumen tersebut.
                            </small>
                        </div>

                        <div
                            id="policy-preview"
                            class="border rounded-3 p-3 mb-4 d-none"
                        >
                            <div class="fw-semibold mb-3">
                                <i class="bi bi-diagram-3 me-2"></i>
                                Approval Flow Preview
                            </div>

                            <div id="policy-steps"></div>
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="form-label fw-semibold">
                                Status
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select @error('is_active') is-invalid @enderror"
                                id="is_active"
                                name="is_active"
                                required
                            >
                                <option
                                    value="1"
                                    @selected(old('is_active', (int) $documentType->is_active) == 1)
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    @selected(old('is_active', (int) $documentType->is_active) == 0)
                                >
                                    Inactive
                                </option>
                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a
                                href="{{ route('administrator.document-types.index') }}"
                                class="btn btn-light"
                            >
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Document Type Information</h6>

                    <div class="mb-3">
                        <small class="text-muted d-block">Document Code</small>
                        <span class="fw-semibold">
                            {{ $documentType->code }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Documents Using This Type</small>
                        <span class="fw-semibold">
                            {{ $documentType->documents_count }}
                        </span>
                    </div>

                    <div>
                        <small class="text-muted d-block">Created At</small>
                        <span class="fw-semibold">
                            {{ $documentType->created_at?->format('d M Y, H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2">Important Information</h6>

                    <p class="text-muted small mb-2">
                        Perubahan approval policy hanya berlaku untuk dokumen
                        baru yang dibuat setelah perubahan disimpan.
                    </p>

                    <p class="text-muted small mb-0">
                        Dokumen yang sudah tersimpan tetap menggunakan
                        approval policy yang telah ditetapkan saat dokumen dibuat.
                    </p>
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
