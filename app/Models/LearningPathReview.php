<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPathReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'user_id',
        'rating',
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

    public function learningPath()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

