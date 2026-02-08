<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'applicable_to',
        'applicable_course_ids',
        'applicable_user_ids',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'applicable_course_ids' => 'array',
        'applicable_user_ids' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    // العلاقات
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
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

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
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

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsedByUser($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        // التحقق من حد الاستخدام لكل مستخدم
        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->count();

        if ($userUsageCount >= $this->usage_limit_per_user) {
            return false;
        }

        // التحقق من المستخدمين المحددين
        if ($this->applicable_user_ids && !in_array($userId, $this->applicable_user_ids)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
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

    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}
