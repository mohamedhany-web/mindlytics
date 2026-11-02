<?php

namespace App\Domains\Advising\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** فترة توافر أسبوعية للإرشاد (مثلاً: الأحد 6–9 م، جلسات 30 دقيقة). */
class AdvisingAvailability extends Model
{
    protected $table = 'advising_availability';

    protected $fillable = ['weekday', 'start_time', 'end_time', 'slot_minutes', 'capacity', 'mode', 'advisor_id', 'is_active'];

    /** 0 = الأحد … 6 = السبت، زي Carbon::dayOfWeek. */
    public const WEEKDAYS = [6 => 'السبت', 0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة'];

    public const WEEKDAYS_EN = [6 => 'Saturday', 0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday'];

    protected function casts(): array
    {
        return [
            'weekday' => 'integer',
            'slot_minutes' => 'integer',
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
