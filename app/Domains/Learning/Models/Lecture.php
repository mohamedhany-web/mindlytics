<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'min_watch_percent_to_unlock_next',
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

    public function materials()
    {
        return $this->hasMany(LectureMaterial::class)->orderBy('sort_order');
    }

    public function videoQuestions()
    {
        return $this->hasMany(LectureVideoQuestion::class)->orderBy('timestamp_seconds');
    }

    public function watchProgress()
    {
        return $this->hasMany(LectureWatchProgress::class);
    }
}
