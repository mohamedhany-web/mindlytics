<?php

namespace App\Models;

use App\Services\SalesCommissionTierService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCourseCommissionAgreement extends Model
{
    public const COURSE_TYPES = [
        'advanced' => 'أونلاين',
        'offline' => 'أوفلاين',
        'legacy' => 'مسجّل / قديم',
    ];

    public const CALC_MODES = [
        'tier_course' => 'Tier للكورس (عدّ wins على الكورس)',
        'tier_course_global_count' => 'Tier للكورس (عدّ wins عام)',
        'tier_global' => 'Tier العام للموظف',
        'fixed' => 'مبلغ ثابت لكل win',
        'percent' => 'نسبة % من قيمة الصفقة',
    ];

    protected $fillable = [
        'user_id',
        'course_type',
        'course_key',
        'advanced_course_id',
        'offline_course_id',
        'course_id',
        'calc_mode',
        'commission_value',
        'tiers',
        'tier_period',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'commission_value' => 'decimal:2',
            'tiers' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advancedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function offlineCourse(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function legacyCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseId(): ?int
    {
        return match ($this->course_type) {
            'advanced' => $this->advanced_course_id ? (int) $this->advanced_course_id : null,
            'offline' => $this->offline_course_id ? (int) $this->offline_course_id : null,
            'legacy' => $this->course_id ? (int) $this->course_id : null,
            default => null,
        };
    }

    public function courseTitle(): string
    {
        return match ($this->course_type) {
            'advanced' => (string) ($this->advancedCourse?->title ?? '—'),
            'offline' => (string) ($this->offlineCourse?->title ?? '—'),
            'legacy' => (string) ($this->legacyCourse?->title ?? '—'),
            default => '—',
        };
    }

    public function coursePrice(): ?float
    {
        $price = match ($this->course_type) {
            'advanced' => $this->advancedCourse?->price,
            'offline' => $this->offlineCourse?->price,
            'legacy' => $this->legacyCourse?->price,
            default => null,
        };

        return $price !== null ? (float) $price : null;
    }

    public function calcModeLabel(): string
    {
        return self::CALC_MODES[$this->calc_mode] ?? (string) $this->calc_mode;
    }

    public function courseTypeLabel(): string
    {
        return self::COURSE_TYPES[$this->course_type] ?? (string) $this->course_type;
    }

    /**
     * @return list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>
     */
    public function normalizedTiers(): array
    {
        return SalesCommissionTierService::normalizeTiers($this->tiers);
    }

    public static function makeCourseKey(string $type, int $courseId): string
    {
        return $type.':'.$courseId;
    }

    public static function applyCourseSelection(self $agreement, string $type, int $courseId): void
    {
        $agreement->course_type = $type;
        $agreement->course_key = self::makeCourseKey($type, $courseId);
        $agreement->advanced_course_id = $type === 'advanced' ? $courseId : null;
        $agreement->offline_course_id = $type === 'offline' ? $courseId : null;
        $agreement->course_id = $type === 'legacy' ? $courseId : null;
    }
}
