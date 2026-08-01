<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDayBlock extends Model
{
    public const ACTIVITY_TYPES = [
        'brief' => 'Brief / مراجعة',
        'calls' => 'مكالمات',
        'followup' => 'متابعات',
        'whatsapp_closing' => 'واتساب وإغلاق',
        'break' => 'استراحة',
        'lunch' => 'غداء',
        'report' => 'تقرير يومي',
    ];

    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'activity_type',
        'goal_text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function activityTypeLabel(): string
    {
        return self::ACTIVITY_TYPES[$this->activity_type] ?? $this->activity_type;
    }

    public function startTimeHm(): string
    {
        return substr((string) $this->start_time, 0, 5);
    }

    public function endTimeHm(): string
    {
        return substr((string) $this->end_time, 0, 5);
    }
}
