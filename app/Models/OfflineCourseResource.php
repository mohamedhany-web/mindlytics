<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OfflineCourseResource extends Model
{
    protected $fillable = [
        'offline_course_id',
        'group_id',
        'instructor_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'attachments',
        'url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * جميع الملفات (القديم الواحد + المرفقات المتعددة)
     */
    public function getAllFiles(): array
    {
        $fromAttachments = [];
        if ($this->attachments && is_array($this->attachments)) {
            foreach ($this->attachments as $att) {
                if (! empty($att['path'])) {
                    $fromAttachments[] = [
                        'path' => $att['path'],
                        'name' => $att['name'] ?? basename($att['path']),
                        'disk' => $att['disk'] ?? 'public',
                    ];
                }
            }
        }
        if (! empty($fromAttachments)) {
            return $fromAttachments;
        }
        if ($this->file_path && $this->file_name) {
            return [['path' => $this->file_path, 'name' => $this->file_name, 'disk' => 'public']];
        }

        return [];
    }

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

    public function offlineCurriculumItems(): MorphMany
    {
        return $this->morphMany(OfflineCurriculumItem::class, 'item');
    }

    public function lectures(): BelongsToMany
    {
        return $this->belongsToMany(
            OfflineLecture::class,
            'offline_lecture_resource',
            'offline_course_resource_id',
            'offline_lecture_id'
        )->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }
}
