<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'total_lectures',
        'attended_lectures',
        'late_lectures',
        'absent_lectures',
        'attendance_rate',
        'total_hours',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'attendance_rate' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }
}
