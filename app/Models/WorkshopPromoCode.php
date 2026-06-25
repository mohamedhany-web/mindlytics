<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopPromoCode extends Model
{
    protected $fillable = [
        'workshop_id',
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'maximum_discount',
        'minimum_order_amount',
        'applies_to_online',
        'applies_to_offline',
        'applies_to_recorded',
        'applicable_advanced_course_ids',
        'applicable_offline_course_ids',
        'max_activations',
        'activation_count',
        'usage_limit_per_user',
        'starts_at',
        'expires_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'applies_to_online' => 'boolean',
        'applies_to_offline' => 'boolean',
        'applies_to_recorded' => 'boolean',
        'applicable_advanced_course_ids' => 'array',
        'applicable_offline_course_ids' => 'array',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activations(): HasMany
    {
        return $this->hasMany(WorkshopPromoActivation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->gt(now())) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->lt(now()->startOfDay())) {
            return false;
        }

        if ($this->max_activations && $this->activation_count >= $this->max_activations) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount <= 0) {
            return 0;
        }

        if ($this->minimum_order_amount && $amount < (float) $this->minimum_order_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? ($amount * (float) $this->discount_value) / 100
            : (float) $this->discount_value;

        if ($this->maximum_discount && $discount > (float) $this->maximum_discount) {
            $discount = (float) $this->maximum_discount;
        }

        return round(min($discount, $amount), 2);
    }

    public function appliesToAdvancedCourse(int $courseId): bool
    {
        if (! $this->applies_to_online && ! $this->applies_to_recorded) {
            return false;
        }

        if (empty($this->applicable_advanced_course_ids)) {
            return true;
        }

        return in_array($courseId, $this->applicable_advanced_course_ids, true);
    }

    public function appliesToOfflineCourse(int $courseId): bool
    {
        if (! $this->applies_to_offline) {
            return false;
        }

        if (empty($this->applicable_offline_course_ids)) {
            return true;
        }

        return in_array($courseId, $this->applicable_offline_course_ids, true);
    }

    public function discountLabel(): string
    {
        if ($this->discount_type === 'percentage') {
            return number_format((float) $this->discount_value, 0).'%';
        }

        return number_format((float) $this->discount_value, 2).' ج.م';
    }

    public function expiryLabel(): string
    {
        if (! $this->expires_at) {
            return 'بدون انتهاء';
        }

        return $this->expires_at->format('Y-m-d');
    }

    public static function generateCode(?Workshop $workshop = null): string
    {
        $prefix = $workshop
            ? 'WS'.strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $workshop->slug ?: $workshop->title), 0, 4))
            : 'WS';

        do {
            $code = $prefix.'-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
