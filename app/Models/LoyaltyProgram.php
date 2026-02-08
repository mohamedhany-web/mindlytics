<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'points_per_purchase',
        'points_per_referral',
        'redemption_rules',
        'is_active',
    ];

    protected $casts = [
        'points_per_purchase' => 'decimal:2',
        'points_per_referral' => 'decimal:2',
        'is_active' => 'boolean',
        'redemption_rules' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_points', 'loyalty_program_id', 'user_id')
            ->withPivot(['points', 'total_earned', 'total_redeemed'])
            ->withTimestamps();
    }
}
