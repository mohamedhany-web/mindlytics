<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'advanced_course_id',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'is_active',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function advancedCourse()
    {
        return $this->belongsTo(AdvancedCourse::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('student_id', $userId);
    }
}