<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoWatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'watch_time',
        'video_duration',
        'progress_percentage',
        'completed',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'completed' => 'boolean',
    ];

    /**
     * العلاقة مع الدرس
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديث تقدم المشاهدة
     */
    public function updateProgress($watchTime, $videoDuration)
    {
        $percentage = ($watchTime / $videoDuration) * 100;
        $completed = $percentage >= 90; // يعتبر مكتمل عند 90%

        $this->update([
            'watch_time' => $watchTime,
            'video_duration' => $videoDuration,
            'progress_percentage' => min($percentage, 100),
            'completed' => $completed,
        ]);

        return $this;
    }
}
