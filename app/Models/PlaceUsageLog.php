<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceUsageLog extends Model
{
    protected $fillable = [
        'offline_location_id',
        'logged_by',
        'usage_date',
        'usage_type',
        'offline_course_id',
        'hours',
        'description',
        'status',
        'place_monthly_settlement_id',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'hours' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const TYPE_COURSE = 'course';

    public const TYPE_OTHER = 'other_activity';

    public static function usageTypeLabels(): array
    {
        return [
            self::TYPE_COURSE => 'كورس / محاضرة',
            self::TYPE_OTHER => 'نشاط آخر (بدون كورس محدد)',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    public function offlineCourse(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function dailyExpenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PlaceDailyExpense::class, 'place_usage_log_id');
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(PlaceMonthlySettlement::class, 'place_monthly_settlement_id');
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

    public function getUsageTypeLabelAttribute(): string
    {
        return self::usageTypeLabels()[$this->usage_type] ?? $this->usage_type ?? '—';
    }
}
