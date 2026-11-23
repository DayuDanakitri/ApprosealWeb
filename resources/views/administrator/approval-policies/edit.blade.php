@extends('layouts.app')

@section('title', 'Edit Approval Policy')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Approval Policy</h4>
            <p class="text-muted mb-0">
                Update approval policy and its approval stages.
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
            <div class="fw-semibold mb-2">
                Please check the following errors:
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('administrator.approval-policies.update', $approvalPolicy) }}"
          id="approvalPolicyForm">

        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-lg-8">

                {{-- POLICY INFORMATION --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">Policy Information</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Policy Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $approvalPolicy->name) }}"
                                   maxlength="150"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Policy Code
                            </label>

                            <input type="text"
                                   name="code"
                                   class="form-control text-uppercase"
                                   value="{{ old('code', $approvalPolicy->code) }}"
                                   maxlength="100"
                                   required>

                            <small class="text-muted">
                                Use letters, numbers, underscores, or hyphens.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea name="description"
                                      class="form-control"
                                      rows="3">{{ old('description', $approvalPolicy->description) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <select name="is_active"
                                    class="form-select"
                                    required>
                                <option value="1"
                                    {{ (string) old('is_active', (int) $approvalPolicy->is_active) === '1' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0"
                                    {{ (string) old('is_active', (int) $approvalPolicy->is_active) === '0' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- APPROVAL STAGES --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Approval Stages</h5>
                                <p class="text-muted small mb-0">
                                    Configure the sequential approval stages.
                                </p>
                            </div>

                            @if (! $isUsed)
                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        id="addStepButton">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Add Stage
                                </button>
                            @endif
                        </div>

                        @if ($isUsed)

                            <div class="alert alert-warning">
                                <i class="bi bi-lock-fill me-2"></i>
                                This policy has already been used by
                                {{ $approvalPolicy->documents_count }}
                                document(s). Its approval stages and assignments
                                are locked to preserve existing document workflows.
                            </div>

                            @foreach ($approvalPolicy->steps as $step)
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary">
                                            Stage {{ $step->step_order }}
                                        </span>

                                        <strong>{{ $step->name }}</strong>
                                    </div>

                                    @if ($step->description)
                                        <p class="text-muted small mb-2">
                                            {{ $step->description }}
                                        </p>
                                    @endif

                                    <div class="small text-muted mb-1">
                                        Assigned Approvers
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($step->assignments as $assignment)
                                            <span class="badge bg-light text-dark border">
                                                {{ $assignment->user->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        @else

                            <div id="stepsContainer"></div>

                            <div id="emptyStepsMessage"
                                 class="text-center text-muted py-4 border rounded-3">
                                No approval stages added yet.
                            </div>

                        @endif

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('administrator.approval-policies.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Save Changes
                    </button>
                </div>

            </div>

            {{-- INFORMATION PANEL --}}
            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Policy Information
                        </h6>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Created By
                            </small>
                            <span class="fw-semibold">
                                {{ $approvalPolicy->creator?->name ?? 'System' }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Total Stages
                            </small>
                            <span class="fw-semibold">
                                {{ $approvalPolicy->steps->count() }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Documents Using This Policy
                            </small>
                            <span class="fw-semibold">
                                {{ $approvalPolicy->documents_count }}
                            </span>
                        </div>

                        <hr>

                        <p class="small text-muted mb-0">
                            Approval stages are processed sequentially.
                            The next stage becomes available only after
                            all assigned approvers in the current stage
                            have approved the document.
                        </p>

                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

@if (! $isUsed)

    <template id="step-template">
        <div class="approval-step border rounded-3 p-3 mb-3">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                    Approval Stage <span class="step-number"></span>
                </h6>

                <button type="button"
                        class="btn btn-sm btn-outline-danger remove-step">
                    <i class="bi bi-trash"></i>
                    Remove
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Stage Name
                </label>

                <input type="text"
                       class="form-control step-name"
                       name="steps[__INDEX__][name]"
                       maxlength="150"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Description
                </label>

                <textarea class="form-control step-description"
                          name="steps[__INDEX__][description]"
                          rows="2"></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold">
                    Assigned Approvers
                </label>

                <select class="form-select step-users"
                        name="steps[__INDEX__][user_ids][]"
                        multiple
                        required
                        size="4">
                    @foreach ($approvers as $approver)
                        <option value="{{ $approver->id }}">
                            {{ $approver->name }}
                        </option>
                    @endforeach
                </select>

                <small class="text-muted">
                    Hold Ctrl to select multiple approvers.
                </small>
            </div>

            <input type="hidden"
                   class="step-required"
                   name="steps[__INDEX__][is_required]"
                   value="1">

        </div>
    </template>

    @php
        $initialSteps = old('steps', $approvalPolicy->steps->map(function ($step) {
            return [
                'name' => $step->name,
                'description' => $step->description,
                'user_ids' => $step->assignments->pluck('user_id')->map(fn ($id) => (string) $id)->toArray(),
                'is_required' => 1,
            ];
        })->toArray());
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stepsContainer = document.getElementById('stepsContainer');
            const emptyMessage = document.getElementById('emptyStepsMessage');
            const addButton = document.getElementById('addStepButton');
            const template = document.getElementById('step-template');

            const initialSteps = {{ Illuminate\Support\Js::from($initialSteps) }};

            function updateStepIndexes() {
                const cards = stepsContainer.querySelectorAll('.approval-step');

                cards.forEach((card, index) => {
                    card.querySelector('.step-number').textContent = index + 1;

                    card.querySelectorAll('[name]').forEach(field => {
                        field.name = field.name.replace(
                            /steps\[[^\]]+\]/,
                            `steps[${index}]`
                        );
                    });
                });

                emptyMessage.style.display = cards.length === 0 ? 'block' : 'none';
            }

            function addStep(data = {}) {
                const fragment = template.content.cloneNode(true);
                const card = fragment.querySelector('.approval-step');

                card.querySelector('.step-name').value = data.name || '';
                card.querySelector('.step-description').value = data.description || '';

                const userSelect = card.querySelector('.step-users');
                const selectedUsers = (data.user_ids || []).map(String);

                Array.from(userSelect.options).forEach(option => {
                    option.selected = selectedUsers.includes(option.value);
                });

                card.querySelector('.remove-step').addEventListener('click', function () {
                    card.remove();
                    updateStepIndexes();
                });

                stepsContainer.appendChild(fragment);
                updateStepIndexes();
            }

            addButton.addEventListener('click', function () {
                addStep();
            });

            initialSteps.forEach(step => addStep(step));

            updateStepIndexes();

            document.getElementById('approvalPolicyForm')
                .addEventListener('submit', function (event) {
                    if (stepsContainer.querySelectorAll('.approval-step').length < 1) {
                        event.preventDefault();
                        alert('At least one approval stage is required.');
                    }
                });
        });
    </script>

@endif

@endsection
