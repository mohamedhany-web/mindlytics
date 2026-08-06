<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesShiftSegment extends Model
{
    public const MODE_NORMAL = 'normal';

    public const MODE_HOME = 'home';

    protected $fillable = [
        'sales_shift_plan_id',
        'day_of_week',
        'user_id',
        'start_hour',
        'end_hour',
        'mode',
        'channels',
        'location_badge',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'day_of_week' => 'integer',
            'start_hour' => 'integer',
            'end_hour' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SalesShiftPlan::class, 'sales_shift_plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channelsLabel(): string
    {
        $channels = $this->channels ?? [];
        if (in_array('all', $channels, true)) {
            return config('sales_shifts.channels.all.label', 'كل القنوات');
        }

        $labels = [];
        foreach ($channels as $code) {
            $labels[] = config("sales_shifts.channels.{$code}.label", $code);
        }

        return implode(' · ', $labels);
    }

    public function coversHour(int $hour): bool
    {
        if ($this->end_hour > $this->start_hour) {
            return $hour >= $this->start_hour && $hour < $this->end_hour;
        }

        // midnight wrap (rare with our 10-26 model)
        return $hour >= $this->start_hour || $hour < $this->end_hour;
    }

    public function coversChannel(string $channel): bool
    {
        $channels = $this->channels ?? [];

        return in_array('all', $channels, true) || in_array($channel, $channels, true);
    }
}
