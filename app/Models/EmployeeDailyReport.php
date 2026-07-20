<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDailyReport extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'user_id',
        'report_date',
        'summary',
        'tasks_done',
        'tomorrow_plan',
        'blockers',
        'hours_worked',
        'status',
        'submitted_at',
        'auto_deduction_id',
        'penalty_waived_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'submitted_at' => 'datetime',
        'penalty_waived_at' => 'datetime',
        'hours_worked' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function autoDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'auto_deduction_id');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }
}
