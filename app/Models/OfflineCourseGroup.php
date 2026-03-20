<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OfflineGroupSession;

class OfflineCourseGroup extends Model
{
    protected $fillable = [
        'offline_course_id',
        'instructor_id',
        'name',
        'description',
        'max_students',
        'current_students',
        'location',
        'class_time',
        'start_date',
        'end_date',
        'session_duration_hours',
        'location_id',
        'status',
        'is_active',
    ];

    protected $casts = [
        'class_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'session_duration_hours' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع الكورس الأوفلاين
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع التسجيلات
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(OfflineCourseEnrollment::class, 'group_id');
    }

    /**
     * علاقة مع الأنشطة
     */
    public function activities(): HasMany
    {
        return $this->hasMany(OfflineActivity::class, 'group_id');
    }

    /**
     * علاقة مع الجلسات
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(OfflineGroupSession::class, 'group_id');
    }

    /**
     * علاقة مع المكان
     */
    public function locationModel(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'location_id');
    }

    public function upcomingSessions()
    {
        return $this->sessions()->upcoming()->ordered();
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }

    /**
     * علاقة مع الحضور
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(OfflineAttendance::class, 'group_id');
    }

    /**
     * التحقق من إمكانية التسجيل
     */
    public function canEnroll(): bool
    {
        return $this->is_active 
            && $this->status === 'active' 
            && $this->current_students < $this->max_students;
    }
}
