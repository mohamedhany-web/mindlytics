<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'status',
        'is_active',
    ];

    protected $casts = [
        'class_time' => 'datetime',
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
