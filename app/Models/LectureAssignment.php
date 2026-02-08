<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
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
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function submissions()
    {
        return $this->hasMany(LectureAssignmentSubmission::class, 'assignment_id');
    }
}
