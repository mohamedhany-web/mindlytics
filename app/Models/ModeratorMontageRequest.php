<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ModeratorMontageRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_MODERATOR_DELIVERY_PENDING = 'moderator_delivery_pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'moderator_id',
        'montage_employee_id',
        'title',
        'description',
        'requirements',
        'priority',
        'deadline_at',
        'status',
        'employee_task_id',
        'moderator_delivery_task_id',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'بانتظار محرر الفيديو',
            self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
            self::STATUS_SUBMITTED => 'تم تسليم الفيديو',
            self::STATUS_MODERATOR_DELIVERY_PENDING => 'بانتظار تسليم المشرف',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
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

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function montageEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'montage_employee_id');
    }

    public function employeeTask(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'employee_task_id');
    }

    public function moderatorDeliveryTask(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'moderator_delivery_task_id');
    }

    /**
     * @return Collection<int, array{source: string, source_label: string, task: EmployeeTask, deliverable: EmployeeTaskDeliverable}>
     */
    public function deliverablesTimelineForModerator(): Collection
    {
        $rows = collect();

        if ($this->employeeTask) {
            foreach ($this->employeeTask->deliverables as $d) {
                $rows->push([
                    'source' => 'editor',
                    'source_label' => 'تسليم محرر الفيديو',
                    'task' => $this->employeeTask,
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

    public static function syncAfterMontageDeliverable(EmployeeTask $task): void
    {
        if ($task->task_type !== 'video_editing' || ! $task->montage_request_id) {
            return;
        }

        $request = self::query()->find($task->montage_request_id);
        if (! $request || $request->isCancelled()) {
            return;
        }

        if (in_array($request->status, [self::STATUS_COMPLETED, self::STATUS_MODERATOR_DELIVERY_PENDING], true)) {
            return;
        }

        $request->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => $request->submitted_at ?? now(),
        ]);
    }

    public static function syncTaskInProgress(EmployeeTask $task): void
    {
        if ($task->task_type !== 'video_editing' || ! $task->montage_request_id) {
            return;
        }

        $request = self::query()->find($task->montage_request_id);
        if (! $request || $request->isCancelled() || $request->status !== self::STATUS_PENDING) {
            return;
        }

        $request->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    /**
     * عند إكمال محرر الفيديو المهمة — تبقى بانتظار مراجعة المشرف وليس «مكتملة».
     */
    public static function syncTaskCompleted(EmployeeTask $task): void
    {
        if ($task->task_type !== 'video_editing' || ! $task->montage_request_id) {
            return;
        }

        $request = self::query()->find($task->montage_request_id);
        if (! $request || $request->isCancelled()) {
            return;
        }

        if (in_array($request->status, [self::STATUS_COMPLETED, self::STATUS_MODERATOR_DELIVERY_PENDING], true)) {
            return;
        }

        $request->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => $request->submitted_at ?? now(),
        ]);
    }

    public static function syncAfterModeratorDeliveryCompleted(EmployeeTask $task): void
    {
        if ($task->task_type !== 'video_montage_moderator_delivery' || ! $task->montage_request_id) {
            return;
        }

        $request = self::query()->find($task->montage_request_id);
        if (! $request || (int) $request->moderator_delivery_task_id !== (int) $task->id) {
            return;
        }

        $request->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }
}
