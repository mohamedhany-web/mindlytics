<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'report_month',
        'opening_balance',
        'closing_balance',
        'total_deposits',
        'total_withdrawals',
        'transactions_count',
        'expected_amounts',
        'actual_amounts',
        'difference',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'difference' => 'decimal:2',
        'expected_amounts' => 'array',
        'actual_amounts' => 'array',
    ];

    /**
     * العلاقة مع المحفظة
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
