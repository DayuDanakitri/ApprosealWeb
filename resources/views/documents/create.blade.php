@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')

<div class="page-heading">
    <div>
        <span class="page-eyebrow">Document Management</span>
        <h1>Upload New Document</h1>
        <p>Upload a PDF and provide the required document information.</p>
    </div>

    <a href="{{ route('secretary.documents.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Documents
    </a>
</div>

<div class="content-card document-form-card">

    <form method="POST"
          action="{{ route('secretary.documents.store') }}"
          enctype="multipart/form-data">

        @csrf

        <div class="form-section-heading">
            <h5>Document Information</h5>
            <p>Enter the details of the document you want to upload.</p>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Document Title <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="title"
                class="form-control"
                placeholder="Enter document title"
                value="{{ old('title') }}"
                required
            >

            @error('title')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>

            <textarea
                name="description"
                class="form-control"
                rows="3"
                placeholder="Enter a short description (optional)"
            >{{ old('description') }}</textarea>

            @error('description')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">
                Document Type <span class="text-danger">*</span>
            </label>

            <select
                name="document_type_id"
                id="documentType"
                class="form-select"
                required
            >
                <option value="">Select document type</option>

                @foreach ($documentTypes as $type)
                    <option
                        value="{{ $type->id }}"
                        data-code="{{ $type->code }}"
                        @selected(old('document_type_id') == $type->id)
                    >
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>

            @if ($documentTypes->isEmpty())
                <div class="text-warning small mt-1">
                    No active document types available.
                    Please contact the administrator.
                </div>
            @endif

            @error('document_type_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="approval-preview mt-3" id="approvalPreview">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-diagram-3"></i>

                <div>
                    <strong id="approvalTitle">
                        Approval Workflow
                    </strong>

                    <p id="approvalDescription" class="mb-0">
                        Select a document type to see its approval workflow.
                    </p>
                </div>
            </div>
        </div>

        <div class="form-section-heading mt-4">
            <h5>Document File</h5>
            <p>Upload the PDF file that will be stored securely.</p>
        </div>

        <div class="upload-area" id="uploadArea">
            <input
                type="file"
                name="file"
                id="documentFile"
                accept=".pdf,application/pdf"
                hidden
                required
            >

            <div class="upload-content">
                <div class="upload-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>

                <strong id="fileName">
                    Choose a PDF file to upload
                </strong>

                <span id="fileInfo">
                    PDF format only · Maximum size 10 MB
                </span>

                <button
                    type="button"
                    class="btn btn-outline-primary mt-3"
                    id="chooseFile"
                >
                    Browse Files
                </button>
            </div>
        </div>

        @error('file')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        <div class="upload-security-note mt-3">
            <i class="bi bi-shield-check"></i>
            Your document will be stored in private storage.
            A SHA-256 hash will be generated automatically.
        </div>

        <div class="form-actions mt-4">
            <a
                href="{{ route('secretary.documents.index') }}"
                class="btn btn-light"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary-custom"
                @disabled($documentTypes->isEmpty())
            >
                <i class="bi bi-upload me-2"></i>
                Upload Document
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('documentFile');
    const chooseButton = document.getElementById('chooseFile');
    const fileName = document.getElementById('fileName');
    const fileInfo = document.getElementById('fileInfo');

    const documentType = document.getElementById('documentType');
    const approvalTitle = document.getElementById('approvalTitle');
    const approvalDescription = document.getElementById('approvalDescription');

    chooseButton.addEventListener('click', function () {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            fileName.textContent = 'Choose a PDF file to upload';
            fileInfo.textContent = 'PDF format only · Maximum size 10 MB';
            return;
        }

        fileName.textContent = file.name;
        fileInfo.textContent =
            (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    });

    function updateApprovalPreview() {
        const selectedOption =
            documentType.options[documentType.selectedIndex];

        const code = selectedOption?.dataset.code;

        if (!code) {
            approvalTitle.textContent = 'Approval Workflow';
            approvalDescription.textContent =
                'Select a document type to see its approval workflow.';
            return;
        }

        if (['PROP', 'LPJ'].includes(code)) {
            approvalTitle.textContent = 'Financial Document Approval';
            approvalDescription.textContent =
                'Bendahara → Ketua → Pembina';
        } else {
            approvalTitle.textContent = 'Standard Document Approval';
            approvalDescription.textContent =
                'Ketua → Pembina';
        }
    }

    documentType.addEventListener('change', updateApprovalPreview);

    updateApprovalPreview();
});
</script>
@endpush
