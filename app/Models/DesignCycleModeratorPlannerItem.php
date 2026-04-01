<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignCycleModeratorPlannerItem extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_DONE = 'done';

    public const STATUS_SKIPPED = 'skipped';

    protected $table = 'design_cycle_moderator_planner_items';

    protected $fillable = [
        'design_task_cycle_id',
        'title',
        'department',
        'time_slot',
        'due_date',
        'status',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sort_order' => 'integer',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(DesignTaskCycle::class, 'design_task_cycle_id');
    }

    public static function statusLabel(?string $s): string
    {
        return match ($s) {
            self::STATUS_PENDING => 'معلّق',
            self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
            self::STATUS_DONE => 'تم',
            self::STATUS_SKIPPED => 'تخطّي',
            default => $s ?? '—',
        };
    }
}
