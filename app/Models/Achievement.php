<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'points',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['earned_at', 'points_earned'])
            ->withTimestamps();
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }
}
