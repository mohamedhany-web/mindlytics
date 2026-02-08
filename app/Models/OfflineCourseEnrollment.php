<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'offline_course_id',
        'group_id',
        'enrolled_at',
        'status',
        'progress',
        'attendance_count',
        'absence_count',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'progress' => 'decimal:2',
    ];

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
     * تحديد ما إذا كان التسجيل نشط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
