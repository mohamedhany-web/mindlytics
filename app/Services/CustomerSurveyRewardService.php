<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\MarketingCustomerSurvey;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * مكافأة استبيان العملاء: كوبون خصم شخصي 20% يُمنح مرة واحدة لكل عميل
 * بعد تعبئة الاستبيان، ويظهر له في المحفظة ويُطبّق تلقائياً في الشيك أوت.
 */
class CustomerSurveyRewardService
{
    public const DISCOUNT_PERCENTAGE = 20;

    public const VALID_DAYS = 90;

    public const CODE_PREFIX = 'THANKS20';

    /**
     * إنشاء كوبون المكافأة وربطه بالاستبيان. يعيد الكوبون الموجود إن كان مُنح سابقاً.
     */
    public function grant(MarketingCustomerSurvey $survey): ?Coupon
    {
        if ($survey->reward_coupon_id && $survey->rewardCoupon) {
            return $survey->rewardCoupon;
        }

        if (! $survey->user_id) {
            return null;
        }

        $percentage = $survey->reward_percentage ?: self::DISCOUNT_PERCENTAGE;
        $expiresAt = Carbon::now()->addDays(self::VALID_DAYS);

        $coupon = Coupon::create([
            'code' => $this->generateCode(),
            'name' => 'مكافأة استبيان العملاء - '.$survey->name,
            'title' => 'خصم '.$percentage.'% كشكر على تقييمك',
            'description' => 'خصم شخصي '.$percentage.'% على أي كورس في الأكاديمية، كشكر على مشاركة رأيك في استبيان العملاء.',
            'discount_type' => 'percentage',
            'discount_value' => $percentage,
            'minimum_amount' => null,
            'maximum_discount' => null,
            'usage_limit' => 1,
            'usage_limit_per_user' => 1,
            'applicable_to' => 'all',
            'applicable_user_ids' => [$survey->user_id],
            'starts_at' => now(),
            'expires_at' => $expiresAt,
            'is_active' => true,
            'is_public' => false,
        ]);

        $survey->update([
            'reward_coupon_id' => $coupon->id,
            'reward_percentage' => $percentage,
            'reward_granted_at' => now(),
        ]);

        return $coupon;
    }

    private function generateCode(): string
    {
        do {
            $code = self::CODE_PREFIX.'-'.strtoupper(Str::random(6));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
