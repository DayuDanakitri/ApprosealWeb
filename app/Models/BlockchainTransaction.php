<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockchainTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_version_id',
        'initiated_by',
        'transaction_hash',
        'transaction_type',
        'wallet_address',
        'chain_id',
        'status',
        'block_number',
        'block_hash',
        'error_message',
        'submitted_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
            'block_number' => 'integer',
            'submitted_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(BlockchainEvent::class);
    }
}
