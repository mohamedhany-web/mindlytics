<?php

namespace App\Domains\Advising\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** حجز جلسة إرشاد مهني. */
class AdvisingBooking extends Model
{
    protected $table = 'advising_bookings';

    protected $fillable = [
        'name', 'phone', 'email', 'goal', 'level', 'interest', 'notes',
        'starts_at', 'duration_minutes', 'mode', 'availability_id', 'advisor_id', 'user_id',
        'status', 'outcome', 'recommended_program', 'source_url', 'ip',
    ];

    public const GOALS = [
        'first_job' => ['أول وظيفة في البرمجيات', 'My first software job'],
        'career_change' => ['تغيير مساري المهني', 'Changing my career'],
        'upskill' => ['تطوير مهاراتي في شغلي', 'Upskilling in my job'],
        'team' => ['تدريب فريق شركتي', 'Training my company team'],
        'unsure' => ['لسه مش متأكد، محتاج توجيه', 'Not sure yet, I need guidance'],
    ];

    public const LEVELS = [
        'beginner' => ['مبتدئ تماماً', 'Complete beginner'],
        'basics' => ['عندي أساسيات', 'I know the basics'],
        'working' => ['شغّال في المجال', 'Already working in tech'],
    ];

    public const MODES = [
        'online' => ['أونلاين (مكالمة فيديو)', 'Online (video call)'],
        'phone' => ['مكالمة تليفون', 'Phone call'],
        'campus' => ['في الفرع', 'At the branch'],
    ];

    public const STATUSES = [
        'pending' => 'جديد',
        'confirmed' => 'مؤكَّد',
        'done' => 'تمّت',
        'no_show' => 'لم يحضر',
        'cancelled' => 'ملغي',
    ];

    /** الحالات اللي بتحجز الميعاد (الملغي بيفضّي المكان). */
    public const ACTIVE_STATUSES = ['pending', 'confirmed', 'done', 'no_show'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(AdvisingAvailability::class, 'availability_id');
    }

    public function scopeHoldingSlot(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public static function label(array $map, ?string $key, bool $ar = true): string
    {
        $v = $map[$key] ?? null;

        return $v === null ? (string) $key : (is_array($v) ? $v[$ar ? 0 : 1] : $v);
    }
}
