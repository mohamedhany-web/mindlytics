<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorMarketingCalendarEvent extends Model
{
    protected $table = 'moderator_mkt_calendar_events';

    public const CONTENT_POST = 'post';

    public const CONTENT_STORY = 'story';

    public const CONTENT_REEL = 'reel';

    public const CONTENT_GRAPHIC_DESIGN = 'graphic_design';

    public const CONTENT_VIDEO_MONTAGE = 'video_montage';

    public const CONTENT_OTHER = 'other';

    protected $fillable = [
        'plan_id',
        'platform_id',
        'title',
        'body',
        'content_type',
        'assigned_employee_id',
        'employee_task_id',
        'requires_confirmation',
        'execution_confirmed_at',
        'execution_confirmed_by',
        'reminder_sent_at',
        'execution_penalty_deduction_id',
        'starts_at',
        'ends_at',
        'status',
        'design_task_cycle_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'requires_confirmation' => 'boolean',
        'execution_confirmed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(ModeratorMarketingPlan::class, 'plan_id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(ModeratorMarketingPlatform::class, 'platform_id');
    }

    public function designTaskCycle(): BelongsTo
    {
        return $this->belongsTo(DesignTaskCycle::class, 'design_task_cycle_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_employee_id');
    }

    public function employeeTask(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'employee_task_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'execution_confirmed_by');
    }

    public function penaltyDeduction(): BelongsTo
    {
        return $this->belongsTo(EmployeeSalaryDeduction::class, 'execution_penalty_deduction_id');
    }

    public function isConfirmed(): bool
    {
        return $this->execution_confirmed_at !== null;
    }

    public function needsConfirmationToday(): bool
    {
        return $this->requires_confirmation
            && ! $this->isConfirmed()
            && $this->starts_at
            && $this->starts_at->isToday();
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'idea' => 'فكرة',
            'draft' => 'مسودة',
            'scheduled' => 'مجدول',
            'published' => 'منشور / مُنفَّذ',
            'skipped' => 'تم التخطي',
            default => $status ?? '—',
        };
    }
}
