<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPatternAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_pattern_id',
        'user_id',
        'status',
        'attempt_data',
        'score',
        'points_earned',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'feedback',
    ];

    protected $casts = [
        'attempt_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'points_earned' => 'integer',
        'time_spent_seconds' => 'integer',
    ];

    public function pattern()
    {
        return $this->belongsTo(LearningPattern::class, 'learning_pattern_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['started', 'in_progress']);
    }

    public function calculateTimeSpent()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return null;
    }
}
