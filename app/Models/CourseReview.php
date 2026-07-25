<?php

namespace App\Models;

use App\Services\CourseReviewStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'reviewer_name',
        'rating',
        'review',
        'comment',
        'image_path',
        'image_disk',
        'status',
        'is_verified_purchase',
        'is_approved',
        'is_featured',
        'is_marketing',
        'helpful_count',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'is_marketing' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helpful()
    {
        return $this->hasMany(ReviewHelpful::class, 'review_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if (filled($this->reviewer_name)) {
            return (string) $this->reviewer_name;
        }

        return (string) ($this->user?->name ?: 'طالب');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! filled($this->image_path)) {
            return null;
        }

        return app(CourseReviewStorageService::class)->url(
            (string) $this->image_path,
            $this->image_disk
        );
    }

    public function getBodyTextAttribute(): string
    {
        return trim((string) ($this->comment ?: $this->review ?: ''));
    }

    protected static function booted(): void
    {
        static::deleting(function (CourseReview $review) {
            if ($review->image_path) {
                app(CourseReviewStorageService::class)->deleteIfExists(
                    $review->image_path,
                    $review->image_disk
                );
            }
        });
    }
}
