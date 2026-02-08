<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_lesson_id',
        'is_completed',
        'completed_at',
        'watch_time',
        'notes',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'watch_time' => 'integer',
    ];

    /**
     * علاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع الدرس
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    /**
     * تحديد ما إذا كان الدرس مكتمل
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * الحصول على نسبة المشاهدة
     */
    public function getWatchPercentageAttribute(): float
    {
        if (!$this->lesson || !$this->lesson->duration_minutes) {
            return 0;
        }

        $totalSeconds = $this->lesson->duration_minutes * 60;
        return $totalSeconds > 0 ? min(100, ($this->watch_time / $totalSeconds) * 100) : 0;
    }

    /**
     * تحديد ما إذا كان الدرس تم مشاهدته بالكامل
     */
    public function isFullyWatched(): bool
    {
        return $this->getWatchPercentageAttribute() >= 80; // 80% أو أكثر يعتبر مشاهدة كاملة
    }
}