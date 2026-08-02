<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesCommissionSettlement extends Model
{
    protected $fillable = [
        'user_id',
        'settled_by',
        'leads_count',
        'amount_total',
        'notes',
        'settled_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount_total' => 'decimal:2',
            'settled_at' => 'datetime',
            'meta' => 'array',
            'leads_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'commission_settlement_id');
    }
}
