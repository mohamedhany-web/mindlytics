<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingDebtRepayment extends Model
{
    protected $fillable = [
        'accounting_debt_id',
        'amount',
        'paid_at',
        'wallet_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function debt(): BelongsTo
    {
        return $this->belongsTo(AccountingDebt::class, 'accounting_debt_id');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
