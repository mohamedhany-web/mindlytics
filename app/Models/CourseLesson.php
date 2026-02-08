<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'advanced_course_id',
        'title',
        'description',
        'type',
        'content',
        'video_url',
        'attachments',
        'duration_minutes',
        'order',
        'is_free',
        'is_active',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع المحاضرات
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class, 'course_lesson_id');
    }

    public function curriculumItems()
    {
        return $this->morphMany(CurriculumItem::class, 'item');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function getProgressForUser($userId)
    {
        return $this->progress()->where('user_id', $userId)->first();
    }

    public function isCompletedByUser($userId)
    {
        return $this->progress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'video' => ['text' => 'فيديو', 'icon' => 'fas fa-play', 'color' => 'blue'],
            'quiz' => ['text' => 'كويز', 'icon' => 'fas fa-question-circle', 'color' => 'yellow'],
            'assignment' => ['text' => 'واجب', 'icon' => 'fas fa-tasks', 'color' => 'red'],
            'document' => ['text' => 'مستند', 'icon' => 'fas fa-file-alt', 'color' => 'green'],
        ];

        return $badges[$this->type] ?? $badges['video'];
    }

    public function getDurationFormattedAttribute()
    {
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' دقيقة';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return $hours . ' ساعة' . ($minutes > 0 ? ' و ' . $minutes . ' دقيقة' : '');
    }

    public function hasAttachments()
    {
        if (empty($this->attachments)) {
            return false;
        }
        
        $attachments = json_decode($this->attachments, true);
        return is_array($attachments) && count($attachments) > 0;
    }

    public function getAttachmentsArray()
    {
        if (empty($this->attachments)) {
            return [];
        }
        
        $attachments = json_decode($this->attachments, true);
        return is_array($attachments) ? $attachments : [];
    }
}