<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureAssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'attachments',
        'github_link',
        'submitted_at',
        'score',
        'feedback',
        'voice_feedback_path',
        'feedback_attachments',
        'graded_at',
        'graded_by',
        'status',
        'version',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'attachments' => 'array',
        'feedback_attachments' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(LectureAssignment::class, 'assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
