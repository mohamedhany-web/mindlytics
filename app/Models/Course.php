<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'thumbnail',
        'subject_id',
        'teacher_id',
        'classroom_id',
        'status',
        'duration_minutes',
        'is_free',
        'price',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
    ];

    // العلاقات
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'student_id')
            ->withPivot('enrolled_at', 'completed_at', 'progress_percentage', 'is_active')
            ->withTimestamps();
    }
}
