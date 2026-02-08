<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineActivitySubmission extends Model
{
    protected $fillable = [
        'activity_id',
        'student_id',
        'submission_content',
        'attachments',
        'submitted_at',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status',
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    /**
     * علاقة مع النشاط
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(OfflineActivity::class, 'activity_id');
    }

    /**
     * علاقة مع الطالب
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * علاقة مع المصحح
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
