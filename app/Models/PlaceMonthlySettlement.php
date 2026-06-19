<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlaceMonthlySettlement extends Model
{
    protected $fillable = [
        'settlement_number',
        'offline_location_id',
        'period_month',
        'total_hours',
        'hourly_rate',
        'total_amount',
        'total_expenses',
        'currency',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'closed_by',
        'closed_at',
        'wallet_id',
        'expense_id',
        'place_invoice_id',
        'notes',
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PAID = 'paid';

    public function location(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(PlaceUsageLog::class, 'place_monthly_settlement_id');
    }

    public function dailyExpenses(): HasMany
    {
        return $this->hasMany(PlaceDailyExpense::class, 'place_monthly_settlement_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(PlaceInvoice::class, 'place_monthly_settlement_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_PAID], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SUBMITTED => 'مُرسل للمراجعة',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_CLOSED => 'مُقفل',
            self::STATUS_PAID => 'مدفوع',
            default => 'مفتوح',
        };
    }

    public static function generateNumber(): string
    {
        $count = static::count() + 1;

        return 'PMS-' . now()->format('Ym') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
