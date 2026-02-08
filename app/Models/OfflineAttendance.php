<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineAttendance extends Model
{
    protected $fillable = [
        'offline_course_id',
        'group_id',
        'student_id',
        'attendance_date',
        'attendance_time',
        'status',
        'notes',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'attendance_time' => 'datetime',
    ];

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع المجموعة
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * علاقة مع من قام بتسجيل الحضور
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
