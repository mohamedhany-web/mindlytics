<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReferralProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'maximum_discount',
        'minimum_order_amount',
        'referrer_reward_type',
        'referrer_reward_value',
        'discount_valid_days',
        'referral_code_valid_days',
        'max_referrals_per_user',
        'max_discount_uses_per_referred',
        'allow_self_referral',
        'starts_at',
        'expires_at',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'referrer_reward_value' => 'decimal:2',
        'discount_valid_days' => 'integer',
        'referral_code_valid_days' => 'integer',
        'max_referrals_per_user' => 'integer',
        'max_discount_uses_per_referred' => 'integer',
        'allow_self_referral' => 'boolean',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // العلاقات
    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    // Methods
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_order_amount && $amount < $this->minimum_order_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        // تطبيق الحد الأقصى للخصم
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        // التأكد من عدم تجاوز المبلغ
        if ($discount > $amount) {
            $discount = $amount;
        }

        return round($discount, 2);
    }

    public function canUserRefer($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        // التحقق من الحد الأقصى للإحالات
        if ($this->max_referrals_per_user) {
            $userReferralsCount = Referral::where('referrer_id', $userId)
                ->where('referral_program_id', $this->id)
                ->count();

            if ($userReferralsCount >= $this->max_referrals_per_user) {
                return false;
            }
        }

        return true;
    }
}