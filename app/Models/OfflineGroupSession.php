<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
