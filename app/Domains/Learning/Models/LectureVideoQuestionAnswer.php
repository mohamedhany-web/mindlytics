<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class LectureVideoQuestionAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'lecture_video_question_id',
        'answer',
        'is_correct',
        'score_earned',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'score_earned' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lectureVideoQuestion(): BelongsTo
    {
        return $this->belongsTo(LectureVideoQuestion::class);
    }

    /**
     * مجموع درجات أسئلة الفيديو لطالب في كورس معيّن.
     */
    public static function totalScoreForUserInCourse(int $userId, int $courseId): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->whereHas('lectureVideoQuestion.lecture', fn ($q) => $q->where('course_id', $courseId))
            ->sum('score_earned');
    }
}
