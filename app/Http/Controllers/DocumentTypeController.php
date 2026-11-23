<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApprovalPolicy;
use App\Models\DocumentType;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    /**
     * Display all document types.
     */
    public function index(Request $request)
    {
        $query = DocumentType::with('approvalPolicy')
            ->withCount('documents')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $documentTypes = $query->paginate(15)->withQueryString();

        $totalTypes = DocumentType::count();
        $activeTypes = DocumentType::where('is_active', true)->count();
        $inactiveTypes = DocumentType::where('is_active', false)->count();
        $typesInUse = DocumentType::has('documents')->count();

        return view('administrator.document-types.index', compact(
            'documentTypes',
            'totalTypes',
            'activeTypes',
            'inactiveTypes',
            'typesInUse'
        ));
    }

    /**
     * Show the form for creating a document type.
     */
    public function create()
    {
        $approvalPolicies = ApprovalPolicy::where('is_active', true)
            ->with('steps')
            ->orderBy('name')
            ->get();

        return view('administrator.document-types.create', compact(
            'approvalPolicies'
        ));
    }

    /**
     * Store a new document type.
     */
    public function store(
        Request $request,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash:ascii',
                'unique:document_types,code',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'approval_policy_id' => [
                'required',
                Rule::exists('approval_policies', 'id')
                    ->where('is_active', true),
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated, $auditLog) {
            $approvalPolicy = ApprovalPolicy::whereKey(
                $validated['approval_policy_id']
            )->where('is_active', true)->firstOrFail();

            $documentType = DocumentType::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'approval_policy_id' => $approvalPolicy->id,
                'is_active' => $validated['is_active'] ?? false,
            ]);

            $auditLog->record(
                Auth::user(),
                'document_type_created',
                'Menambahkan jenis dokumen baru.',
                $documentType,
                [
                    'name' => $documentType->name,
                    'code' => $documentType->code,
                    'approval_policy' => $approvalPolicy->name,
                    'is_active' => $documentType->is_active,
                ]
            );
        });

        return redirect()
            ->route('administrator.document-types.index')
            ->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a document type.
     */
    public function edit(DocumentType $documentType)
    {
        $documentType->loadCount('documents');

        $approvalPolicies = ApprovalPolicy::where('is_active', true)
            ->with('steps')
            ->orderBy('name')
            ->get();

        return view('administrator.document-types.edit', compact(
            'documentType',
            'approvalPolicies'
        ));
    }

    /**
     * Update an existing document type.
     */
    public function update(
        Request $request,
        DocumentType $documentType,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash:ascii',
                Rule::unique('document_types', 'code')
                    ->ignore($documentType->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'approval_policy_id' => [
                'required',
                Rule::exists('approval_policies', 'id')
                    ->where('is_active', true),
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $documentType,
            $auditLog
        ) {
            $lockedType = DocumentType::whereKey($documentType->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldCode = $lockedType->code;
            $oldName = $lockedType->name;
            $oldPolicyId = $lockedType->approval_policy_id;

            // Prevent changing the code if the type is already used.
            if (
                $oldCode !== strtoupper($validated['code']) &&
                $lockedType->documents()->exists()
            ) {
                abort(
                    422,
                    'Kode jenis dokumen tidak dapat diubah karena sudah digunakan oleh dokumen.'
                );
            }

            $approvalPolicy = ApprovalPolicy::whereKey(
                $validated['approval_policy_id']
            )->where('is_active', true)->firstOrFail();

            $lockedType->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'approval_policy_id' => $approvalPolicy->id,
                'is_active' => $validated['is_active'],
            ]);

            $auditLog->record(
                Auth::user(),
                'document_type_updated',
                'Memperbarui informasi jenis dokumen.',
                $lockedType,
                [
                    'previous_name' => $oldName,
                    'new_name' => $lockedType->name,
                    'previous_code' => $oldCode,
                    'new_code' => $lockedType->code,
                    'previous_approval_policy_id' => $oldPolicyId,
                    'new_approval_policy_id' => $approvalPolicy->id,
                    'new_approval_policy' => $approvalPolicy->name,
                    'is_active' => $lockedType->is_active,
                ]
            );
        });

        return redirect()
            ->route('administrator.document-types.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }
}
