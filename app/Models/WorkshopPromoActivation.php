<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopPromoActivation extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_USED = 'used';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'workshop_promo_code_id',
        'user_id',
        'coupon_id',
        'status',
        'activated_at',
        'used_at',
        'used_on_type',
        'used_on_id',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(WorkshopPromoCode::class, 'workshop_promo_code_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function isUsable(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $promo = $this->promoCode;
        if (! $promo || ! $promo->isValid()) {
            return false;
        }

        if ($this->coupon && ! $this->coupon->isValid()) {
            return false;
        }

        return true;
    }
}
