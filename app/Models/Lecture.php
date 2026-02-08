<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_lesson_id',
        'instructor_id',
        'title',
        'description',
        'teams_registration_link',
        'teams_meeting_link',
        'recording_url',
        'recording_file_path',
        'video_platform',
        'scheduled_at',
        'duration_minutes',
        'status',
        'notes',
        'has_attendance_tracking',
        'has_assignment',
        'has_evaluation',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'has_attendance_tracking' => 'boolean',
        'has_assignment' => 'boolean',
        'has_evaluation' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function assignments()
    {
        return $this->hasMany(LectureAssignment::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function evaluations()
    {
        return $this->hasMany(LectureEvaluation::class);
    }

    public function curriculumItems()
    {
        return $this->morphMany(CurriculumItem::class, 'item');
    }
}
