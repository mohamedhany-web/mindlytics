<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class DesignTaskCycle extends Model
{
    public const STATUS_PENDING_DESIGN = 'pending_design';

    public const STATUS_DESIGN_IN_PROGRESS = 'design_in_progress';

    public const STATUS_DESIGN_SUBMITTED = 'design_submitted';

    public const STATUS_MODERATOR_DELIVERY_PENDING = 'moderator_delivery_pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'moderator_id',
        'designer_employee_id',
        'title',
        'description',
        'specifications',
        'priority',
        'deadline_at',
        'status',
        'designer_task_id',
        'moderator_delivery_task_id',
        'designer_submitted_at',
        'completed_at',
        'admin_notes',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'designer_submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_employee_id');
    }

    public function designerTask(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'designer_task_id');
    }

    public function moderatorDeliveryTask(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'moderator_delivery_task_id');
    }

    public function employeeTasks(): HasMany
    {
        return $this->hasMany(EmployeeTask::class, 'design_cycle_id');
    }

    public function moderatorPlannerItems(): HasMany
    {
        return $this->hasMany(DesignCycleModeratorPlannerItem::class, 'design_task_cycle_id')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * تسليمات المصمم والمشرف مجمّعة للعرض على صفحة المشرف.
     *
     * @return Collection<int, array{source: string, source_label: string, task: EmployeeTask, deliverable: EmployeeTaskDeliverable}>
     */
    public function deliverablesTimelineForModerator(): Collection
    {
        $rows = collect();

        if ($this->designerTask) {
            foreach ($this->designerTask->deliverables as $d) {
                $rows->push([
                    'source' => 'designer',
                    'source_label' => 'تسليم المصمم',
                    'task' => $this->designerTask,
                    'deliverable' => $d,
                ]);
            }
        }

        if ($this->moderatorDeliveryTask) {
            foreach ($this->moderatorDeliveryTask->deliverables as $d) {
                $rows->push([
                    'source' => 'moderator',
                    'source_label' => 'تسليمك (مشرف)',
                    'task' => $this->moderatorDeliveryTask,
                    'deliverable' => $d,
                ]);
            }
        }

        return $rows->sortByDesc(fn (array $r) => $r['deliverable']->created_at?->timestamp ?? 0)->values();
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING_DESIGN => 'بانتظار المصمم',
            self::STATUS_DESIGN_IN_PROGRESS => 'قيد التنفيذ (تصميم)',
            self::STATUS_DESIGN_SUBMITTED => 'تم تسليم التصميم',
            self::STATUS_MODERATOR_DELIVERY_PENDING => 'بانتظار تسليم المشرف',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
            default => $status ?? '—',
        };
    }

    public static function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING_DESIGN => 'bg-amber-100 text-amber-800 border-amber-200',
            self::STATUS_DESIGN_IN_PROGRESS => 'bg-sky-100 text-sky-800 border-sky-200',
            self::STATUS_DESIGN_SUBMITTED => 'bg-violet-100 text-violet-800 border-violet-200',
            self::STATUS_MODERATOR_DELIVERY_PENDING => 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_CANCELLED => 'bg-slate-200 text-slate-700 border-slate-300',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public static function priorityLabel(?string $priority): string
    {
        return match ($priority) {
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
            default => $priority ?? '—',
        };
    }

    public static function priorityBadgeClass(?string $priority): string
    {
        return match ($priority) {
            'low' => 'bg-slate-100 text-slate-700',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-rose-100 text-rose-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * بعد أول تسليم من المصمم على مهمة task_type=design.
     */
    public static function syncAfterDesignerDeliverable(EmployeeTask $task): void
    {
        if ($task->task_type !== 'design' || ! $task->design_cycle_id) {
            return;
        }

        $cycle = self::query()->find($task->design_cycle_id);
        if (! $cycle || $cycle->isCancelled()) {
            return;
        }

        if (in_array($cycle->status, [self::STATUS_COMPLETED, self::STATUS_MODERATOR_DELIVERY_PENDING], true)) {
            return;
        }

        $cycle->update([
            'status' => self::STATUS_DESIGN_SUBMITTED,
            'designer_submitted_at' => $cycle->designer_submitted_at ?? now(),
        ]);
    }

    /**
     * عند إكمال مهمة تسليم المشرف.
     */
    public static function syncAfterModeratorDeliveryCompleted(EmployeeTask $task): void
    {
        if ($task->task_type !== 'design_moderator_delivery' || ! $task->design_cycle_id) {
            return;
        }

        $cycle = self::query()->find($task->design_cycle_id);
        if (! $cycle || (int) $cycle->moderator_delivery_task_id !== (int) $task->id) {
            return;
        }

        $cycle->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * عند بدء المصمم العمل (اختياري — يحسّن التتبع).
     */
    public static function syncDesignerTaskInProgress(EmployeeTask $task): void
    {
        if ($task->task_type !== 'design' || ! $task->design_cycle_id) {
            return;
        }

        $cycle = self::query()->find($task->design_cycle_id);
        if (! $cycle || $cycle->isCancelled() || $cycle->status !== self::STATUS_PENDING_DESIGN) {
            return;
        }

        $cycle->update(['status' => self::STATUS_DESIGN_IN_PROGRESS]);
    }
}
