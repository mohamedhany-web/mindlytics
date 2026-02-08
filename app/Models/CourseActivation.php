<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'advanced_course_id',
        'activated_at',
        'expires_at',
        'is_active',
        'notes',
        'activated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function activatedBy()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid()
    {
        return $this->is_active && !$this->isExpired();
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->expires_at) {
            return null; // لا ينتهي
        }

        $days = now()->diffInDays($this->expires_at, false);
        return $days > 0 ? $days : 0;
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_active) {
            return ['text' => 'معطل', 'color' => 'gray'];
        }

        if ($this->isExpired()) {
            return ['text' => 'منتهي', 'color' => 'red'];
        }

        if ($this->expires_at && $this->getDaysRemainingAttribute() <= 7) {
            return ['text' => 'قارب على الانتهاء', 'color' => 'yellow'];
        }

        return ['text' => 'نشط', 'color' => 'green'];
    }
}