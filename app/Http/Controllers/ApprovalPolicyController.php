<?php

namespace App\Http\Controllers;

use App\Models\ApprovalPolicy;
use App\Models\ApprovalStep;
use App\Models\ApprovalStepAssignment;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApprovalPolicyController extends Controller
{
    /**
     * Display all approval policies.
     */
    public function index(Request $request)
    {
        $query = ApprovalPolicy::withCount([
            'steps',
            'documents',
        ])->with([
            'steps.assignments',
        ])->orderBy('name');

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

        $approvalPolicies = $query->paginate(15)->withQueryString();

        $totalPolicies = ApprovalPolicy::count();
        $activePolicies = ApprovalPolicy::where('is_active', true)->count();
        $inactivePolicies = ApprovalPolicy::where('is_active', false)->count();

        $policiesInUse = ApprovalPolicy::whereHas('documents')
            ->count();

        return view('administrator.approval-policies.index', compact(
            'approvalPolicies',
            'totalPolicies',
            'activePolicies',
            'inactivePolicies',
            'policiesInUse'
        ));
    }

    /**
     * Show the form for creating a policy.
     */
    public function create()
    {
        $approvers = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('slug', 'approver');
            })
            ->orderBy('name')
            ->get();

        return view('administrator.approval-policies.create', compact(
            'approvers'
        ));
    }

    /**
     * Store a new approval policy.
     */
    public function store(Request $request, AuditLogService $auditLog)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash:ascii',
                'unique:approval_policies,code',
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],

            'steps' => ['required', 'array', 'min:1'],
            'steps.*.name' => ['required', 'string', 'max:150'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.is_required' => ['required', 'boolean'],

            'steps.*.user_ids' => ['required', 'array', 'min:1'],
            'steps.*.user_ids.*' => [
                'required',
                'distinct',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('roles')
                            ->where('slug', 'approver');
                    });
                }),
            ],
        ]);

        DB::transaction(function () use ($validated, $auditLog) {
            $policy = ApprovalPolicy::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
                'created_by' => Auth::id(),
            ]);

            foreach (array_values($validated['steps']) as $index => $stepData) {
                $step = $policy->steps()->create([
                    'step_order' => $index + 1,
                    'name' => $stepData['name'],
                    'description' => $stepData['description'] ?? null,
                    'is_required' => $stepData['is_required'],
                ]);

                foreach ($stepData['user_ids'] as $userId) {
                    $step->assignments()->create([
                        'user_id' => $userId,
                    ]);
                }
            }

            $auditLog->record(
                Auth::user(),
                'approval_policy_created',
                'Membuat approval policy baru.',
                $policy,
                [
                    'name' => $policy->name,
                    'code' => $policy->code,
                    'steps_count' => count($validated['steps']),
                ]
            );
        });

        return redirect()
            ->route('administrator.approval-policies.index')
            ->with('success', 'Approval policy berhasil dibuat.');
    }

    /**
     * Show the form for editing a policy.
     */
    public function edit(ApprovalPolicy $approvalPolicy)
    {
        $approvalPolicy->load([
            'steps.assignments.user',
        ])->loadCount('documents');

        $approvers = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('slug', 'approver');
            })
            ->orderBy('name')
            ->get();

        $isUsed = $approvalPolicy->documents_count > 0;

        return view('administrator.approval-policies.edit', compact(
            'approvalPolicy',
            'approvers',
            'isUsed'
        ));
    }

    /**
     * Update an existing policy.
     */
    public function update(
        Request $request,
        ApprovalPolicy $approvalPolicy,
        AuditLogService $auditLog
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:100',
                'alpha_dash:ascii',
                Rule::unique('approval_policies', 'code')
                    ->ignore($approvalPolicy->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],

            'steps' => ['sometimes', 'required', 'array', 'min:1'],
            'steps.*.name' => ['required', 'string', 'max:150'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.is_required' => ['required', 'boolean'],
            'steps.*.user_ids' => ['required', 'array', 'min:1'],
            'steps.*.user_ids.*' => [
                'required',
                'distinct',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('roles')
                            ->where('slug', 'approver');
                    });
                }),
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $approvalPolicy,
            $auditLog
        ) {
            $policy = ApprovalPolicy::whereKey($approvalPolicy->id)
                ->lockForUpdate()
                ->firstOrFail();

            $isUsed = $policy->documents()->exists();

            if ($isUsed && isset($validated['steps'])) {
                throw ValidationException::withMessages([
                    'steps' => 'Tahapan approval tidak dapat diubah karena policy sudah digunakan oleh dokumen.',
                ]);
            }

            if (
                ! $validated['is_active']
                && DocumentType::where('approval_policy_id', $policy->id)
                    ->where('is_active', true)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'is_active' => 'Policy tidak dapat dinonaktifkan karena masih digunakan oleh jenis dokumen aktif.',
                ]);
            }

            $oldName = $policy->name;
            $oldCode = $policy->code;
            $oldStatus = $policy->is_active;

            $policy->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            if (! $isUsed && isset($validated['steps'])) {
                $policy->steps()->delete();

                foreach (array_values($validated['steps']) as $index => $stepData) {
                    $step = $policy->steps()->create([
                        'step_order' => $index + 1,
                        'name' => $stepData['name'],
                        'description' => $stepData['description'] ?? null,
                        'is_required' => $stepData['is_required'],
                    ]);

                    foreach ($stepData['user_ids'] as $userId) {
                        $step->assignments()->create([
                            'user_id' => $userId,
                        ]);
                    }
                }
            }

            $auditLog->record(
                Auth::user(),
                'approval_policy_updated',
                'Memperbarui approval policy.',
                $policy,
                [
                    'previous_name' => $oldName,
                    'new_name' => $policy->name,
                    'previous_code' => $oldCode,
                    'new_code' => $policy->code,
                    'previous_status' => $oldStatus,
                    'new_status' => $policy->is_active,
                    'steps_modified' => ! $isUsed && isset($validated['steps']),
                ]
            );
        });

        return redirect()
            ->route('administrator.approval-policies.index')
            ->with('success', 'Approval policy berhasil diperbarui.');
    }
}
