<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineGroupSession extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'session_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'location',
        'instructor_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseGroup::class, 'group_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(OfflineLecture::class, 'offline_group_session_id');
    }

    /**
     * جلسات المجموعات التابعة لكورس أوفلاين (حسب قناة التعلم للمجموعة).
     */
    public function scopeForOfflineCourse(Builder $query, OfflineCourse $course, string $channel = 'offline'): Builder
    {
        return $query->whereHas('group', function (Builder $gq) use ($course, $channel) {
            $gq->where('offline_course_id', $course->id);
            if ($channel === 'online') {
                $gq->where(function (Builder $g) {
                    $g->where('online_booking_enabled', true)
                        ->orWhere('current_students_online', '>', 0);
                });
            }
        });
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', now()->toDateString())
                     ->where('status', 'scheduled');
    }

    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('session_date')->orderBy('start_time');
    }
}
