<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearnDiscussion extends Model
{
    public const KIND_DISCUSSION = 'discussion';

    public const KIND_QA = 'qa';

    public const CONTEXT_TYPES = ['lecture', 'assignment', 'exam', 'pattern'];

    protected $fillable = [
        'course_id',
        'context_type',
        'context_id',
        'kind',
        'user_id',
        'parent_id',
        'body',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function isInstructorAuthor(): bool
    {
        $user = $this->user;

        return $user && ($user->isInstructor() || $user->isTeacher() || $user->isAdmin());
    }
}
