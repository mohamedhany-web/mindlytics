<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'type',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot(['earned_at'])
            ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }
}
