<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'created_by',
        'approval_policy_id',
        'onchain_document_id',
        'title',
        'description',
        'reference_number',
        'verification_token',
        'current_version',
        'application_status',
        'blockchain_status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'current_version' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvalPolicy(): BelongsTo
    {
        return $this->belongsTo(ApprovalPolicy::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function blockchainTransactions(): HasMany
    {
        return $this->hasMany(BlockchainTransaction::class);
    }

    public function blockchainEvents(): HasMany
    {
        return $this->hasMany(BlockchainEvent::class);
    }
}
