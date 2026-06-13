<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingDebt extends Model
{
    public const DIRECTION_PAYABLE = 'payable';

    public const DIRECTION_RECEIVABLE = 'receivable';

    protected $fillable = [
        'debt_number',
        'direction',
        'party_name',
        'party_phone',
        'party_relation',
        'title',
        'amount',
        'paid_amount',
        'remaining_amount',
        'wallet_id',
        'deposited_to_wallet',
        'debt_date',
        'due_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date',
        'deposited_to_wallet' => 'boolean',
    ];

    public static function directionLabels(): array
    {
        return [
            self::DIRECTION_PAYABLE => 'دين علينا (استلفنا)',
            self::DIRECTION_RECEIVABLE => 'دين لنا (مستحق لنا)',
        ];
    }

    public static function directionLabel(?string $direction): string
    {
        return self::directionLabels()[$direction] ?? '—';
    }

    public static function statusLabels(): array
    {
        return [
            'active' => 'نشط',
            'partial' => 'سداد جزئي',
            'settled' => 'مسدّد',
            'cancelled' => 'ملغي',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusLabels()[$status] ?? '—';
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(AccountingDebtRepayment::class);
    }

    public function recalculateStatus(): void
    {
        $remaining = max(0, (float) $this->amount - (float) $this->paid_amount);
        $this->remaining_amount = round($remaining, 2);

        if ($this->status === 'cancelled') {
            $this->save();

            return;
        }

        if ($remaining <= 0) {
            $this->status = 'settled';
        } elseif ((float) $this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'active';
        }

        $this->save();
    }

    public function isPayable(): bool
    {
        return $this->direction === self::DIRECTION_PAYABLE;
    }
}
