<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorMarketingCalendarEvent extends Model
{
    protected $table = 'moderator_mkt_calendar_events';

    protected $fillable = [
        'plan_id',
        'platform_id',
        'title',
        'body',
        'starts_at',
        'ends_at',
        'status',
        'design_task_cycle_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
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
}
