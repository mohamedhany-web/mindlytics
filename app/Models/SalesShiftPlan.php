<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesShiftPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'work_start_hour',
        'work_end_hour',
        'takeover_grace_minutes',
        'is_active',
        'effective_from',
        'rules',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'effective_from' => 'date',
            'rules' => 'array',
            'work_start_hour' => 'integer',
            'work_end_hour' => 'integer',
            'takeover_grace_minutes' => 'integer',
        ];
    }

    public function segments(): HasMany
    {
        return $this->hasMany(SalesShiftSegment::class)->orderBy('day_of_week')->orderBy('sort_order')->orderBy('start_hour');
    }

    public static function activePlan(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();
    }
}
