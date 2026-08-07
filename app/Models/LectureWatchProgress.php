<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureWatchProgress extends Model
{
    use HasFactory;

    protected $table = 'lecture_watch_progress';

    protected $fillable = [
        'lecture_id',
        'user_id',
        'watch_time_seconds',
        'video_duration_seconds',
        'progress_percent',
        'is_completed',
    ];

    protected $casts = [
        'watch_time_seconds' => 'integer',
        'video_duration_seconds' => 'integer',
        'progress_percent' => 'integer',
        'is_completed' => 'boolean',
    ];

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديث عيّنة مشاهدة.
     *
     * @param  int|null  $expectedDurationSec  مدة متوقعة من المنهج (duration_minutes) لتجنّب اكتمال زائف بمدة قصيرة خاطئة
     */
    public function updateFromSample(
        int $currentSec,
        int $durationSec,
        ?int $minPercentToComplete = null,
        ?int $expectedDurationSec = null,
    ): void {
        $reportedDuration = max(1, $durationSec);
        $expected = max(0, (int) ($expectedDurationSec ?? 0));
        $storedDuration = max((int) ($this->video_duration_seconds ?? 0), $reportedDuration, $expected);

        // احسب النسبة على أطول مدة معروفة حتى لا تُكمَّل المحاضرة بعيّنة duration خاطئة (مثلاً 5 ثوانٍ).
        $effectiveDuration = max($storedDuration, 1);
        $currentSec = max(0, min($currentSec, $effectiveDuration));
        $samplePercent = (int) min(100, round(($currentSec / $effectiveDuration) * 100));

        // لا ننقص النسبة أبداً (Seek للخلف / عيّنة خاطئة لا تلغي إنجاز الطالب)
        $percent = max((int) ($this->progress_percent ?? 0), $samplePercent);
        $watchTime = max((int) ($this->watch_time_seconds ?? 0), $currentSec);

        $threshold = $minPercentToComplete ?? 90;
        $completed = (bool) ($this->is_completed) || $percent >= $threshold;

        $this->fill([
            'watch_time_seconds' => $watchTime,
            'video_duration_seconds' => $storedDuration,
            'progress_percent' => $percent,
            'is_completed' => $completed,
        ])->save();
    }
}
