<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockchainEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockchain_transaction_id',
        'document_id',
        'document_version_id',
        'chain_id',
        'contract_address',
        'event_name',
        'transaction_hash',
        'log_index',
        'block_number',
        'block_hash',
        'event_data',
        'processing_status',
        'processed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
            'log_index' => 'integer',
            'block_number' => 'integer',
            'event_data' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function blockchainTransaction(): BelongsTo
    {
        return $this->belongsTo(BlockchainTransaction::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class);
    }
}
