<?php

namespace App\Models;

use App\Models\Concerns\QueriesByBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory, QueriesByBranch;

    protected static function booted(): void
    {
        static::creating(function (WalletTransaction $walletTransaction): void {
            if ($walletTransaction->branch_id !== null && $walletTransaction->branch_id !== '') {
                return;
            }
            if ($walletTransaction->wallet_id) {
                $bid = Wallet::query()->whereKey($walletTransaction->wallet_id)->value('branch_id');
                if ($bid !== null) {
                    $walletTransaction->branch_id = $bid;

                    return;
                }
            }
            if ($walletTransaction->payment_id) {
                $bid = Payment::query()->whereKey($walletTransaction->payment_id)->value('branch_id');
                if ($bid !== null) {
                    $walletTransaction->branch_id = $bid;

                    return;
                }
            }
            $default = Branch::defaultAssignableId();
            if ($default !== null) {
                $walletTransaction->branch_id = $default;
            }
        });
    }

    protected $fillable = [
        'branch_id',
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
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    public static function typeLabels(): array
    {
        return [
            'deposit' => 'إيداع',
            'withdrawal' => 'سحب',
            'refund' => 'استرداد',
            'commission' => 'عمولة',
            'bonus' => 'مكافأة',
            'deduction' => 'خصم',
        ];
    }

    public static function typeLabel(?string $type): string
    {
        if ($type === null || $type === '') {
            return 'غير محدد';
        }

        return static::typeLabels()[$type] ?? $type;
    }

    public function isIncoming(): bool
    {
        return in_array($this->type, ['deposit', 'bonus'], true);
    }

    public function noteText(): string
    {
        return trim((string) ($this->description ?? $this->notes ?? ''));
    }

    /**
     * العلاقة مع المحفظة
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
