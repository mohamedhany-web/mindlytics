<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'type',
        'requirements',
        'points_reward',
        'is_active',
        'sort_order',
        // legacy aliases kept for older admin forms if present
        'category',
        'points',
    ];

    protected $casts = [
        'requirements' => 'array',
        'points_reward' => 'integer',
        'points' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['earned_at', 'points_earned', 'progress', 'metadata', 'course_id'])
            ->withTimestamps();
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }
}
