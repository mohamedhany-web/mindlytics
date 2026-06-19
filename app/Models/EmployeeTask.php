<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeTask extends Model
{
    protected $fillable = [
        'employee_id',
        'assigned_by',
        'title',
        'description',
        'task_type',
        'priority',
        'status',
        'deadline',
        'started_at',
        'completed_at',
        'progress',
        'notes',
        'design_cycle_id',
        'marketing_event_id',
    ];

    protected $casts = [
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * علاقة مع المكلف
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * علاقة مع التسليمات
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(EmployeeTaskDeliverable::class, 'task_id');
    }

    public function designCycle(): BelongsTo
    {
        return $this->belongsTo(DesignTaskCycle::class, 'design_cycle_id');
    }

    public function marketingEvent(): BelongsTo
    {
        return $this->belongsTo(ModeratorMarketingCalendarEvent::class, 'marketing_event_id');
    }

    /**
     * Scope للمهام المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope للمهام قيد التنفيذ
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope للمهام المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * هل المهمة من نوع مونتاج فيديو
     */
    public function isVideoEditing(): bool
    {
        return $this->task_type === 'video_editing';
    }

    /**
     * هل المهمة مخصصة للمبيعات (تسليم كمهمة عامة: ملف/رابط مع تمييز بصري)
     */
    public function isSales(): bool
    {
        return $this->task_type === 'sales';
    }

    public function isDesign(): bool
    {
        return $this->task_type === 'design';
    }

    public function isDesignModeratorDelivery(): bool
    {
        return $this->task_type === 'design_moderator_delivery';
    }

    public static function taskTypeLabel(?string $taskType): string
    {
        return match ($taskType) {
            'video_editing' => 'مونتاج فيديو',
            'sales' => 'مبيعات',
            'design' => 'تصميم (دورة مشرف/مصمم)',
            'design_moderator_delivery' => 'تسليم نهائي (مشرف)',
            default => 'مهمة عامة',
        };
    }

    /**
     * Scope للمهام المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereIn('status', ['pending', 'in_progress']);
    }
}
