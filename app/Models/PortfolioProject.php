<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioProject extends Model
{
    protected $fillable = [
        'user_id',
        'academic_year_id',
        'advanced_course_id',
        'program_type',
        'offline_course_id',
        'title',
        'project_type',
        'is_capstone',
        'description',
        'technologies',
        'what_i_learned',
        'challenges',
        'project_url',
        'github_url',
        'image_path',
        'status',
        'instructor_notes',
        'rubric_code_quality',
        'rubric_ui_ux',
        'rubric_functionality',
        'rubric_problem_solving',
        'rubric_documentation',
        'rubric_average',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'rejected_reason',
        'revision_count',
        'admin_notes',
        'is_visible',
        'is_featured',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'is_capstone' => 'boolean',
        'technologies' => 'array',
        'rubric_average' => 'float',
        'revision_count' => 'integer',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_CHANGES_REQUESTED = 'changes_requested';

    public const STATUS_RESUBMITTED = 'resubmitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PUBLISHED = 'published';

    public const PROGRAM_RECORDED = 'recorded';

    public const PROGRAM_DIPLOMA = 'diploma';

    public const REVIEWABLE_STATUSES = [
        self::STATUS_PENDING_REVIEW,
        self::STATUS_RESUBMITTED,
    ];

    public const EDITABLE_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_CHANGES_REQUESTED,
        self::STATUS_REJECTED,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function advancedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function offlineCourse(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioProjectImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PortfolioProjectReview::class)->latest();
    }

    public function getPreviewImagePathAttribute(): ?string
    {
        if ($this->relationLoaded('images')) {
            $first = $this->images->first();

            return $first ? $first->image_path : $this->image_path;
        }

        if ($this->image_path) {
            return $this->image_path;
        }

        $first = $this->images()->first();

        return $first ? $first->image_path : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)->where('is_visible', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeProgramType($query, ?string $type)
    {
        if ($type && in_array($type, [self::PROGRAM_RECORDED, self::PROGRAM_DIPLOMA], true)) {
            $query->where('program_type', $type);
        }

        return $query;
    }

    public function isReviewable(): bool
    {
        return in_array($this->status, self::REVIEWABLE_STATUSES, true);
    }

    public function isEditableByStudent(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES, true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PENDING_REVIEW => 'قيد المراجعة',
            self::STATUS_CHANGES_REQUESTED => 'يحتاج تعديلات',
            self::STATUS_RESUBMITTED => 'أُعيد إرساله',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_PUBLISHED => 'منشور',
            default => $this->status,
        };
    }

    public function programTypeLabel(): string
    {
        return match ($this->program_type) {
            self::PROGRAM_RECORDED => 'كورس مسجّل',
            self::PROGRAM_DIPLOMA => 'دبلوم / حضوري',
            default => 'غير محدد',
        };
    }

    public function programContextLabel(): ?string
    {
        if ($this->academicYear) {
            return $this->academicYear->name;
        }
        if ($this->offlineCourse) {
            return $this->offlineCourse->title ?? $this->offlineCourse->name ?? null;
        }
        if ($this->advancedCourse) {
            return $this->advancedCourse->title;
        }

        return null;
    }

    public static function resolveProgramType(?int $academicYearId, ?int $advancedCourseId, ?int $offlineCourseId): ?string
    {
        if ($academicYearId || $offlineCourseId) {
            return self::PROGRAM_DIPLOMA;
        }
        if ($advancedCourseId) {
            return self::PROGRAM_RECORDED;
        }

        return null;
    }

    public function applyRubricScores(array $scores): void
    {
        $keys = [
            'rubric_code_quality',
            'rubric_ui_ux',
            'rubric_functionality',
            'rubric_problem_solving',
            'rubric_documentation',
        ];
        $values = [];
        foreach ($keys as $key) {
            $inputKey = str_replace('rubric_', 'score_', $key);
            if (array_key_exists($key, $scores)) {
                $this->{$key} = $scores[$key];
            } elseif (array_key_exists($inputKey, $scores)) {
                $this->{$key} = $scores[$inputKey];
            }
            if ($this->{$key} !== null) {
                $values[] = (int) $this->{$key};
            }
        }
        $this->rubric_average = count($values) ? round(array_sum($values) / count($values), 2) : null;
    }
}
