<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OfflineCourse extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructor_id',
        'location_id',
        'location',
        'start_date',
        'end_date',
        'duration_hours',
        'sessions_count',
        'price',
        'max_students',
        'current_students',
        'status',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع المكان
     */
    public function locationModel(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'location_id');
    }

    /**
     * علاقة مع المجموعات
     */
    public function groups(): HasMany
    {
        return $this->hasMany(OfflineCourseGroup::class, 'offline_course_id');
    }

    /**
     * علاقة مع التسجيلات
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(OfflineCourseEnrollment::class, 'offline_course_id');
    }

    /**
     * علاقة مع الطلاب (من خلال التسجيلات)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'offline_course_enrollments', 'offline_course_id', 'user_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'group_id'])
                    ->withTimestamps();
    }

    /**
     * علاقة مع الأنشطة
     */
    public function activities(): HasMany
    {
        return $this->hasMany(OfflineActivity::class, 'offline_course_id');
    }

    /**
     * علاقة مع اتفاقيات المدربين
     */
    public function instructorAgreements(): HasMany
    {
        return $this->hasMany(InstructorAgreement::class, 'offline_course_id');
    }

    /**
     * علاقة مع الحضور
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(OfflineAttendance::class, 'offline_course_id');
    }

    /**
     * علاقة مع موارد الكورس (أوفلاين)
     */
    public function resources(): HasMany
    {
        return $this->hasMany(OfflineCourseResource::class, 'offline_course_id');
    }

    /**
     * علاقة مع محاضرات الكورس (أوفلاين)
     */
    public function offlineLectures(): HasMany
    {
        return $this->hasMany(OfflineLecture::class, 'offline_course_id');
    }

    /**
     * علاقة مع امتحانات الأكاديمية المرتبطة بالكورس الأوفلاين
     */
    public function exams(): HasMany
    {
        return $this->hasMany(AdvancedExam::class, 'offline_course_id');
    }

    /**
     * Scope للكورسات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope للكورسات حسب المدرب
     */
    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
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

    /**
     * زيادة عدد الطلاب
     */
    public function incrementStudents(): void
    {
        $this->increment('current_students');
    }

    /**
     * تقليل عدد الطلاب
     */
    public function decrementStudents(): void
    {
        $this->decrement('current_students');
    }
}
