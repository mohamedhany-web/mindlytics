<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesDailyReport extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'user_id',
        'report_date',
        'status',
        'submitted_at',
        'messages_replied',
        'leads_qualified',
        'bookings_from_leads',
        'activity_notes',
        'numbers_worked',
        'followups_done',
        'calls_made',
        'meetings_held',
        'calls_answered',
        'productivity_notes',
        'missing_fields',
        'auto_deduction_id',
        'penalty_waived_at',
        'manager_reviewed_at',
        'manager_reviewed_by',
        'sales_team_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'penalty_waived_at' => 'datetime',
        'manager_reviewed_at' => 'datetime',
        'missing_fields' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SalesDailyReportContact::class);
    }

    public function autoDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'auto_deduction_id');
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
