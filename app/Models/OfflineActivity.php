<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineActivity extends Model
{
    protected $fillable = [
        'offline_course_id',
        'group_id',
        'instructor_id',
        'title',
        'description',
        'type',
        'due_date',
        'max_score',
        'instructions',
        'attachments',
        'status',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'date',
        'attachments' => 'array',
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
     * علاقة مع المجموعة
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع التقديمات
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(OfflineActivitySubmission::class, 'activity_id');
    }
}
