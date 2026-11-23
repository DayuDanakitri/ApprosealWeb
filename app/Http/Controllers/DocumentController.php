<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class DocumentController extends Controller
{
    /**
     * Display documents belonging to the authenticated secretary.
     */
    public function index(Request $request)
    {
        $query = Document::with('documentType')
            ->where('created_by', Auth::id());

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'application_status',
                $request->input('status')
            );
        }

        $documents = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the document upload form.
     */
    public function create()
    {
        $documentTypes = DocumentType::where('is_active', true)
            ->whereHas('approvalPolicy', function ($query) {
                $query->where('is_active', true);
            })
            ->with('approvalPolicy')
            ->orderBy('name')
            ->get();

        return view('documents.create', compact('documentTypes'));
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(
        Request $request,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')
                    ->where('is_active', true),
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ]);

        // Retrieve document type and its approval policy.
        $documentType = DocumentType::with('approvalPolicy')
            ->whereKey($validated['document_type_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $approvalPolicy = $documentType->approvalPolicy;

        // Ensure the document type has an active approval policy.
        if (!$approvalPolicy || !$approvalPolicy->is_active) {
            return back()
                ->withInput()
                ->withErrors([
                    'document_type_id' =>
                        'Jenis dokumen ini belum memiliki approval policy aktif.',
                ]);
        }

        $file = $request->file('file');

        // Calculate SHA-256 hash from the actual uploaded PDF.
        $documentHash = hash_file(
            'sha256',
            $file->getRealPath()
        );

        if (!$documentHash) {
            return back()
                ->withInput()
                ->withErrors([
                    'file' => 'Gagal menghitung hash dokumen.',
                ]);
        }

        // Store the uploaded file in private storage.
        $storedFilename = Str::uuid() . '.pdf';

        $filePath = $file->storeAs(
            'documents',
            $storedFilename,
            'local'
        );

        if (!$filePath) {
            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Dokumen gagal disimpan. Silakan coba kembali.',
                ]);
        }

        try {
            $document = DB::transaction(function () use (
                $validated,
                $file,
                $documentHash,
                $filePath,
                $approvalPolicy,
                $auditLog
            ) {
                // Create document record.
                $document = Document::create([
                    'document_type_id' => $validated['document_type_id'],
                    'created_by' => Auth::id(),
                    'approval_policy_id' => $approvalPolicy->id,
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'current_version' => 1,
                    'application_status' => 'draft',
                ]);

                // Create initial document version.
                DocumentVersion::create([
                    'document_id' => $document->id,
                    'version_number' => 1,
                    'file_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'document_hash' => $documentHash,
                    'uploaded_by' => Auth::id(),
                    'blockchain_status' => null,
                ]);

                // Record document upload activity.
                $auditLog->record(
                    Auth::user(),
                    'document_uploaded',
                    'Mengunggah dokumen baru.',
                    $document,
                    [
                        'document_title' => $document->title,
                        'document_type' => $document->documentType->name,
                        'approval_policy' => $approvalPolicy->name,
                        'version' => 1,
                        'document_hash' => $documentHash,
                    ]
                );

                return $document;
            });

            return redirect()
                ->route('secretary.documents.show', $document)
                ->with(
                    'success',
                    'Dokumen berhasil diunggah dan approval policy ditentukan otomatis.'
                );

        } catch (Throwable $exception) {
            Storage::disk('local')->delete($filePath);

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Terjadi kesalahan saat menyimpan dokumen.',
                ]);
        }
    }

    /**
     * Display document details and approval timeline.
     */
    
    public function show(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        $document->load([
            'documentType',
            'approvalPolicy.steps.assignments.user',
            'versions.uploader',
            'versions.approvals.approver',
            'versions.approvals.approvalStep',
        ]);

        $currentVersion = $document->versions->firstWhere(
            'version_number',
            $document->current_version
        );

        $verificationUrl = null;
        $qrCode = null;

        if (
            $document->application_status === 'published' &&
            !empty($document->verification_token)
        ) {
            $verificationUrl = route(
                'verification.show',
                ['token' => $document->verification_token]
            );

            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel' => QRCode::ECC_L,
                'outputBase64' => false,
            ]);

            $qrCode = (new QRCode($options))->render($verificationUrl);
        }

        return view('documents.show', compact(
            'document',
            'currentVersion',
            'verificationUrl',
            'qrCode'
        ));
    }

    public function publish(
        Document $document,
        AuditLogService $auditLog
    ) {
        $this->authorizeDocumentOwner($document);

        DB::transaction(function () use ($document, $auditLog) {
            $lockedDocument = Document::whereKey($document->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                (int) $lockedDocument->created_by !==
                (int) Auth::id()
            ) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }

            if ($lockedDocument->application_status !== 'approved') {
                throw ValidationException::withMessages([
                    'document' =>
                        'Hanya dokumen yang telah disetujui sepenuhnya yang dapat diterbitkan.',
                ]);
            }

            $lockedDocument->update([
                'application_status' => 'published',
                'published_at' => now(),
                'verification_token' => $lockedDocument->verification_token
                    ?? (string) Str::uuid(),
            ]);

            $auditLog->record(
                Auth::user(),
                'document_published',
                'Menerbitkan dokumen yang telah disetujui.',
                $lockedDocument,
                [
                    'document_title' => $lockedDocument->title,
                    'document_type' => $lockedDocument->documentType->name,
                    'version' => $lockedDocument->current_version,
                    'published_at' => $lockedDocument->published_at
                        ->toDateTimeString(),
                ]
            );
        });

        return redirect()
            ->route('secretary.documents.show', $document)
            ->with('success', 'Dokumen berhasil diterbitkan.');
    }

    /**
     * Display the revision form for a rejected document.
     */
    public function revise(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        abort_unless(
            $document->application_status === 'rejected',
            403,
            'Hanya dokumen yang ditolak yang dapat direvisi.'
        );

        $document->load([
            'documentType',
            'versions.approvals.approver',
            'versions.approvals.approvalStep',
        ]);

        $currentVersion = $document->versions()
            ->where('version_number', $document->current_version)
            ->firstOrFail();

        $rejectionReasons = $currentVersion->approvals
            ->where('decision', 'rejected');

        return view('documents.revise', compact(
            'document',
            'currentVersion',
            'rejectionReasons'
        ));
    }

    /**
     * Store a new document version after revision.
     */
    public function storeRevision(
        Request $request,
        Document $document,
        AuditLogService $auditLog
    ) {
        $this->authorizeDocumentOwner($document);

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ]);

        $file = $request->file('file');

        // Calculate SHA-256 hash of the revised PDF.
        $documentHash = hash_file(
            'sha256',
            $file->getRealPath()
        );

        if (!$documentHash) {
            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Gagal menghitung hash dokumen revisi.',
                ]);
        }

        // Store the revised file.
        $filePath = $file->storeAs(
            'documents',
            Str::uuid() . '.pdf',
            'local'
        );

        if (!$filePath) {
            return back()
                ->withInput()
                ->withErrors([
                    'file' => 'Dokumen revisi gagal disimpan.',
                ]);
        }

        try {
            DB::transaction(function () use (
                $document,
                $file,
                $filePath,
                $documentHash,
                $auditLog
            ) {
                $lockedDocument = Document::whereKey($document->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    (int) $lockedDocument->created_by !==
                    (int) Auth::id()
                ) {
                    abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
                }

                if ($lockedDocument->application_status !== 'rejected') {
                    throw ValidationException::withMessages([
                        'document' =>
                            'Dokumen hanya dapat direvisi jika berstatus Rejected.',
                    ]);
                }

                $newVersionNumber =
                    (int) $lockedDocument->current_version + 1;

                // Create new document version.
                DocumentVersion::create([
                    'document_id' => $lockedDocument->id,
                    'version_number' => $newVersionNumber,
                    'file_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'document_hash' => $documentHash,
                    'uploaded_by' => Auth::id(),
                    'blockchain_status' => null,
                ]);

                // Reset document status for resubmission.
                $lockedDocument->update([
                    'current_version' => $newVersionNumber,
                    'application_status' => 'draft',
                ]);

                // Record revision activity.
                $auditLog->record(
                    Auth::user(),
                    'document_revised',
                    'Mengunggah versi revisi dokumen.',
                    $lockedDocument,
                    [
                        'document_title' => $lockedDocument->title,
                        'previous_version' => $newVersionNumber - 1,
                        'new_version' => $newVersionNumber,
                        'document_hash' => $documentHash,
                    ]
                );
            });

            return redirect()
                ->route('secretary.documents.show', $document)
                ->with(
                    'success',
                    'Dokumen revisi berhasil diunggah sebagai versi baru. Silakan ajukan kembali untuk persetujuan.'
                );

        } catch (Throwable $exception) {
            Storage::disk('local')->delete($filePath);

            report($exception);

            if (
                $exception instanceof ValidationException ||
                $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException
            ) {
                throw $exception;
            }

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Terjadi kesalahan saat menyimpan dokumen revisi.',
                ]);
        }
    }

    /**
     * Ensure that the authenticated secretary owns the document.
     */
    private function authorizeDocumentOwner(Document $document): void
    {
        abort_unless(
            (int) $document->created_by === (int) Auth::id(),
            403,
            'Anda tidak memiliki akses ke dokumen ini.'
        );
    }
}
