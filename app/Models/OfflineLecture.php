<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineLecture extends Model
{
    protected $fillable = [
        'offline_course_id',
        'group_id',
        'instructor_id',
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'recording_url',
        'download_links',
        'attachments',
        'notes',
        'order',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'download_links' => 'array',
        'attachments' => 'array',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('scheduled_at')->orderBy('id');
    }
}
