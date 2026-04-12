<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'order_id',
        'user_id',
        'payment_method',
        'payment_gateway',
        'wallet_id',
        'installment_payment_id',
        'amount',
        'gateway_fee_amount',
        'gateway_fee_detail',
        'currency',
        'status',
        'transaction_id',
        'reference_number',
        'gateway_response',
        'notes',
        'proof_path',
        'paid_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
        'gateway_fee_detail' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function installmentPayment()
    {
        return $this->belongsTo(InstallmentPayment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * مدفوعات أونلاين عبر بوابة (للمحاسبة وتقارير العمولة).
     */
    public function scopeGatewayOnline($query)
    {
        return $query->where('status', 'completed')
            ->where('payment_method', 'online')
            ->whereNotNull('payment_gateway')
            ->where('payment_gateway', '!=', 'manual');
    }

    public static function gatewayLabel(?string $gateway): string
    {
        return match (strtolower((string) $gateway)) {
            'kashier' => 'كاشير',
            'fawaterak' => 'فواتيرك',
            'moyasar' => 'مويصر',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'other' => 'أخرى',
            'manual' => 'يدوي',
            default => $gateway ? (string) $gateway : '—',
        };
    }
}
