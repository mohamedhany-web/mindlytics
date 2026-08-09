<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesAttendancePermission extends Model
{
    public const TYPE_DAY_ABSENCE = 'day_absence';

    public const TYPE_EARLY_DEPARTURE = 'early_departure';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'employee_id',
        'granted_by',
        'type',
        'work_date',
        'early_departure_time',
        'reason',
        'status',
        'revoked_at',
        'revoked_by',
        'revoke_reason',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'revoked_at' => 'datetime',
        ];
    }

    /** @return array<string, string> */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_DAY_ABSENCE => 'إذن غياب يوم',
            self::TYPE_EARLY_DEPARTURE => 'إذن انصراف مبكر',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'ساري',
            self::STATUS_REVOKED => 'ملغى',
            default => $this->status,
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function granter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED)->whereNull('revoked_at');
    }

    public function scopeForEmployeeDate(Builder $query, int $employeeId, Carbon|string $date): Builder
    {
        $day = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return $query->where('employee_id', $employeeId)->whereDate('work_date', $day);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->revoked_at === null;
    }

    public static function hasDayAbsence(int $employeeId, Carbon|string $date): bool
    {
        return static::query()
            ->approved()
            ->forEmployeeDate($employeeId, $date)
            ->where('type', self::TYPE_DAY_ABSENCE)
            ->exists();
    }

    public static function hasEarlyDeparture(int $employeeId, Carbon|string $date): bool
    {
        return static::query()
            ->approved()
            ->forEmployeeDate($employeeId, $date)
            ->where('type', self::TYPE_EARLY_DEPARTURE)
            ->exists();
    }

    public static function earlyDepartureFor(int $employeeId, Carbon|string $date): ?self
    {
        return static::query()
            ->approved()
            ->forEmployeeDate($employeeId, $date)
            ->where('type', self::TYPE_EARLY_DEPARTURE)
            ->latest('id')
            ->first();
    }
}
