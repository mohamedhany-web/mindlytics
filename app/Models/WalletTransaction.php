<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'payment_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_number',
        'notes',
        'description',
        'status',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * العلاقة مع المحفظة
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * العلاقة مع الدفع
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * العلاقة مع المعاملة
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ المعاملة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
