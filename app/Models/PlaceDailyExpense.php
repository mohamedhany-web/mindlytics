<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceDailyExpense extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'offline_location_id',
        'logged_by',
        'expense_date',
        'title',
        'category',
        'amount',
        'quantity',
        'status',
        'place_monthly_settlement_id',
        'place_usage_log_id',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public static function categoryLabels(): array
    {
        return [
            'food' => 'أكل / وجبات',
            'drinks' => 'مشروبات',
            'supplies' => 'مستلزمات',
            'transport' => 'مواصلات',
            'other' => 'أخرى',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(PlaceMonthlySettlement::class, 'place_monthly_settlement_id');
    }

    public function usageLog(): BelongsTo
    {
        return $this->belongsTo(PlaceUsageLog::class, 'place_usage_log_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            default => 'في الانتظار',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }

    public function lineTotal(): float
    {
        return round((float) $this->amount * max(1, (int) $this->quantity), 2);
    }
}
