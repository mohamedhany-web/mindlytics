<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_by',
        'title',
        'description',
        'priority',
        'status',
        'progress',
        'due_date',
        'completed_at',
        'related_course_id',
        'related_lecture_id',
        'related_assignment_id',
        'related_type',
        'related_id',
        'is_reminder',
        'reminder_at',
        'tags',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_at' => 'datetime',
        'is_reminder' => 'boolean',
        'tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function relatedCourse()
    {
        return $this->belongsTo(AdvancedCourse::class, 'related_course_id');
    }

    public function relatedLecture()
    {
        return $this->belongsTo(Lecture::class, 'related_lecture_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function deliverables()
    {
        return $this->hasMany(TaskDeliverable::class)->orderBy('submitted_at', 'desc');
    }

    /** مهمة مسندة من الإدارة (لا يعدلها المدرب) */
    public function isAssignedByAdmin(): bool
    {
        return $this->assigned_by !== null;
    }

    public function notifications()
    {
        return $this->hasMany(TaskNotification::class);
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }
}
