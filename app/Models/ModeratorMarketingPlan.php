<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeratorMarketingPlan extends Model
{
    protected $table = 'moderator_mkt_plans';

    protected $fillable = [
        'moderator_id',
        'title',
        'summary',
        'goals',
        'start_date',
        'end_date',
        'status',
        'design_task_cycle_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function designTaskCycle(): BelongsTo
    {
        return $this->belongsTo(DesignTaskCycle::class, 'design_task_cycle_id');
    }

    public function platforms(): HasMany
    {
        return $this->hasMany(ModeratorMarketingPlatform::class, 'plan_id')->orderBy('sort_order');
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(ModeratorMarketingCalendarEvent::class, 'plan_id');
    }
}
