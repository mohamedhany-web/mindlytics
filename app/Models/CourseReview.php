<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'review',
        'comment',
        'status',
        'is_verified_purchase',
        'is_approved',
        'is_featured',
        'helpful_count',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
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
}
