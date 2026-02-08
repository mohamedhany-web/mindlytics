<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'advanced_course_id',
        'group_id',
        'lesson_id',
        'teacher_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'max_score',
        'allow_late_submission',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
        'max_score' => 'integer',
    ];

    public function course()
    {
        // استخدام advanced_course_id إذا كان موجوداً، وإلا استخدم course_id
        if ($this->advanced_course_id) {
            return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
        }
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function curriculumItems()
    {
        return $this->morphMany(CurriculumItem::class, 'item');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}

