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
        'title',
        'project_type',
        'description',
        'project_url',
        'github_url',
        'image_path',
        'status',
        'instructor_notes',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'rejected_reason',
        'admin_notes',
        'is_visible',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'is_visible' => 'boolean',
    ];

    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PUBLISHED = 'published';

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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioProjectImage::class)->orderBy('sort_order');
    }

    /** صورة المعاينة (الأولى من المعرض أو image_path القديم) */
    public function getPreviewImagePathAttribute(): ?string
    {
        $first = $this->images()->first();
        return $first ? $first->image_path : $this->image_path;
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)->where('is_visible', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
