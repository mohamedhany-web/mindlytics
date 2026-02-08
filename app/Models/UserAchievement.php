<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_id',
        'course_id',
        'earned_at',
        'progress',
        'points_earned',
        'metadata',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'progress' => 'integer',
        'points_earned' => 'integer',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class);
    }

    public function badge()
    {
        return $this->hasOne(UserBadge::class, 'user_id', 'user_id')
            ->whereHas('badge', function($q) {
                $q->where('code', 'LIKE', '%' . $this->achievement->code . '%');
            });
    }
}
