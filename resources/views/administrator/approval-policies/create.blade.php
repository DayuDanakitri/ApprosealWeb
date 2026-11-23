@extends('layouts.app')

@section('title', 'Create Approval Policy')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Create Approval Policy</h4>
            <p class="text-muted mb-0">
                Define approval stages and assign approvers for each stage.
            </p>
        </div>

        <a href="{{ route('administrator.approval-policies.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">
                Please check the following errors:
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('administrator.approval-policies.store') }}"
          method="POST"
          id="approval-policy-form">

        @csrf

        <div class="row g-4">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">Policy Information</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Policy Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                placeholder="Example: Financial Approval"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">
                                Policy Code <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="code"
                                name="code"
                                class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code') }}"
                                placeholder="Example: FINANCIAL_APPROVAL"
                                required
                            >

                            <small class="text-muted">
                                Use letters, numbers, hyphens, or underscores.
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
                                id="description"
                                name="description"
                                rows="3"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Describe the purpose of this approval policy..."
                            >{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="is_active" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select
                                id="is_active"
                                name="is_active"
                                class="form-select @error('is_active') is-invalid @enderror"
                                required
                            >
                                <option value="1" @selected(old('is_active', '1') == '1')>
                                    Active
                                </option>
                                <option value="0" @selected(old('is_active') == '0')>
                                    Inactive
                                </option>
                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Approval Stages</h5>
                                <p class="text-muted small mb-0">
                                    Arrange the approvers in the required approval order.
                                </p>
                            </div>

                            <button type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    id="add-step">
                                <i class="bi bi-plus-lg me-1"></i>
                                Add Stage
                            </button>
                        </div>

                        <div id="steps-container"></div>

                        <div id="empty-steps-message"
                             class="text-center text-muted py-4 d-none">
                            No approval stages added yet.
                        </div>

                        @error('steps')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('administrator.approval-policies.index') }}"
                       class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Save Approval Policy
                    </button>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            How Approval Stages Work
                        </h6>

                        <p class="text-muted small">
                            Approval stages are processed sequentially.
                            The next stage becomes available after all required
                            approvers in the current stage have approved.
                        </p>

                        <p class="text-muted small mb-0">
                            Assign at least one approver to every stage.
                            Only users with the Approver role can be assigned.
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Approval Flow Preview
                        </h6>

                        <div id="flow-preview" class="text-muted small">
                            Add approval stages to see the flow.
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<template id="step-template">
    <div class="approval-step border rounded-3 p-3 mb-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary step-number">1</span>
                <span class="fw-semibold step-heading">Approval Stage 1</span>
            </div>

            <button type="button"
                    class="btn btn-sm btn-outline-danger remove-step">
                <i class="bi bi-trash"></i>
                Remove
            </button>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Stage Name <span class="text-danger">*</span>
            </label>

            <input type="text"
                   class="form-control step-name"
                   name="steps[__INDEX__][name]"
                   placeholder="Example: Treasurer Approval"
                   maxlength="150"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Description
            </label>

            <textarea
                class="form-control step-description"
                name="steps[__INDEX__][description]"
                rows="2"
                placeholder="Optional stage description..."
            ></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Assigned Approvers <span class="text-danger">*</span>
            </label>

            <select
                class="form-select step-users"
                name="steps[__INDEX__][user_ids][]"
                multiple
                size="4"
                required
            >
                @foreach ($approvers as $approver)
                    <option value="{{ $approver->id }}">
                        {{ $approver->name }} ({{ $approver->email }})
                    </option>
                @endforeach
            </select>

            <small class="text-muted">
                Hold Ctrl (Windows) or Command (Mac) to select multiple approvers.
            </small>
        </div>

        <div>
            <label class="form-label fw-semibold">
                Stage Requirement
            </label>

            <input
                type="hidden"
                class="step-required"
                name="steps[__INDEX__][is_required]"
                value="1"
            >
        </div>

    </div>
</template>
@endsection

@push('scripts')
<script>
    const stepsContainer = document.getElementById('steps-container');
    const stepTemplate = document.getElementById('step-template');
    const addStepButton = document.getElementById('add-step');
    const emptyMessage = document.getElementById('empty-steps-message');
    const flowPreview = document.getElementById('flow-preview');

    const oldSteps = {{ Illuminate\Support\Js::from(
        old('steps', [
            [
                'name' => '',
                'description' => '',
                'user_ids' => [],
                'is_required' => '1',
            ],
        ])
    ) }};

    function updateStepIndexes() {
        const cards = stepsContainer.querySelectorAll('.approval-step');

        cards.forEach((card, index) => {
            card.querySelector('.step-number').textContent = index + 1;
            card.querySelector('.step-heading').textContent =
                `Approval Stage ${index + 1}`;

            card.querySelectorAll('[name]').forEach(field => {
                field.name = field.name.replace(
                    /steps\[[^\]]+\]/,
                    `steps[${index}]`
                );
            });
        });
    }

    function updateFlowPreview() {
        const cards = stepsContainer.querySelectorAll('.approval-step');

        flowPreview.innerHTML = '';

        if (cards.length === 0) {
            flowPreview.textContent =
                'Add approval stages to see the flow.';
            return;
        }

        cards.forEach((card, index) => {
            const name = card.querySelector('.step-name').value.trim()
                || `Approval Stage ${index + 1}`;

            const selectedUsers = Array.from(
                card.querySelector('.step-users').selectedOptions
            ).map(option => option.textContent.trim());

            const stage = document.createElement('div');
            stage.className = 'mb-2';

            const title = document.createElement('div');
            title.className = 'fw-semibold text-dark';
            title.textContent = `${index + 1}. ${name}`;

            const users = document.createElement('div');
            users.className = 'small text-muted';

            users.textContent = selectedUsers.length
                ? selectedUsers.join(', ')
                : 'No approver selected';

            stage.appendChild(title);
            stage.appendChild(users);
            flowPreview.appendChild(stage);

            if (index < cards.length - 1) {
                const arrow = document.createElement('div');
                arrow.className = 'text-muted ms-2 mb-2';
                arrow.innerHTML = '<i class="bi bi-arrow-down"></i>';

                flowPreview.appendChild(arrow);
            }
        });
    }

    function addStep(data = {}) {
        const fragment = stepTemplate.content.cloneNode(true);
        const card = fragment.querySelector('.approval-step');

        card.querySelector('.step-name').value = data.name || '';
        card.querySelector('.step-description').value =
            data.description || '';

        card.querySelector('.step-required').value =
            String(data.is_required ?? '1');

        const selectedUsers = (data.user_ids || []).map(String);

        card.querySelectorAll('.step-users option').forEach(option => {
            option.selected = selectedUsers.includes(option.value);
        });

        card.querySelector('.remove-step').addEventListener('click', () => {
            card.remove();
            updateStepIndexes();
        });

        card.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('input', updateFlowPreview);
            field.addEventListener('change', updateFlowPreview);
        });

        stepsContainer.appendChild(fragment);

        updateStepIndexes();
    }

    addStepButton.addEventListener('click', () => {
        addStep();
    });

    oldSteps.forEach(step => {
        addStep(step);
    });

    document.getElementById('approval-policy-form')
        .addEventListener('submit', function (event) {
            if (stepsContainer.querySelectorAll('.approval-step').length < 1) {
                event.preventDefault();
                alert('Please add at least one approval stage.');
            }
        });
</script>
@endpush
