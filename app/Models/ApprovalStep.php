<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_policy_id',
        'step_order',
        'name',
        'description',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    public function approvalPolicy(): BelongsTo
    {
        return $this->belongsTo(ApprovalPolicy::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ApprovalStepAssignment::class);
    }

    public function documentApprovals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class);
    }
}
