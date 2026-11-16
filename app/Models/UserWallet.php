<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_address',
        'network_name',
        'chain_id',
        'is_primary',
        'is_verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
