<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Coupon;
use App\Models\OfflineCourse;
use App\Models\User;
use App\Models\WorkshopPromoActivation;
use App\Models\WorkshopPromoCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WorkshopPromoService
{
    /**
     * تفعيل كود عند التسجيل — فوري (لا يعتمد على Queue).
     *
     * @return array{success: bool, message: string}|null
     */
    public function tryActivateOnRegistration(User $user, ?string $rawCode): ?array
    {
        if ($rawCode === null || trim($rawCode) === '') {
            return null;
        }

        if (! Schema::hasTable('workshop_promo_activations') || ! Schema::hasTable('workshop_promo_codes')) {
            Log::error('Workshop promo tables missing — run migration 2026_06_20_200000_create_workshop_promo_codes_table');

            return [
                'success' => false,
                'message' => 'نظام أكواد الورش غير مفعّل على السيرفر بعد',
            ];
        }

        $result = $this->activateForUser($user, $rawCode);

        if ($result['success']) {
            Log::info('Workshop promo activated on registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => strtoupper(trim($rawCode)),
            ]);
        } else {
            Log::warning('Workshop promo activation failed on registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => strtoupper(trim($rawCode)),
                'reason' => $result['message'],
            ]);
        }

        return $result;
    }

    /**
     * تفعيل كود ورشة للمستخدم — ينشئ كوبون خاص به.
     */
    public function activateForUser(User $user, string $rawCode): array
    {
        $code = strtoupper(trim($rawCode));
        if ($code === '') {
            return ['success' => false, 'message' => 'أدخل كود الخصم'];
        }

        $promo = WorkshopPromoCode::where('code', $code)->first();
        if (! $promo) {
            return ['success' => false, 'message' => 'كود الخصم غير صحيح'];
        }

        if (! $promo->isValid()) {
            return ['success' => false, 'message' => 'كود الخصم منتهي أو غير نشط'];
        }

        $existing = WorkshopPromoActivation::where('workshop_promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->isUsable()) {
                return [
                    'success' => true,
                    'message' => 'الكود مفعّل لحسابك بالفعل — خصم '.$promo->discountLabel(),
                    'activation' => $existing,
                    'promo' => $promo,
                ];
            }

            return ['success' => false, 'message' => 'استخدمت هذا الكود مسبقاً'];
        }

        try {
            $activation = DB::transaction(function () use ($promo, $user) {
                $promo->refresh();
                if (! $promo->isValid()) {
                    throw new \RuntimeException('كود الخصم لم يعد متاحاً');
                }

                $coupon = $this->createPrivateCoupon($promo, $user);

                $activation = WorkshopPromoActivation::create([
                    'workshop_promo_code_id' => $promo->id,
                    'user_id' => $user->id,
                    'coupon_id' => $coupon->id,
                    'status' => WorkshopPromoActivation::STATUS_ACTIVE,
                    'activated_at' => now(),
                ]);

                $promo->increment('activation_count');

                return $activation;
            });

            return [
                'success' => true,
                'message' => 'تم تفعيل الخصم بنجاح! خصم '.$promo->discountLabel().' على الكورسات المؤهلة',
                'activation' => $activation->load('promoCode', 'coupon'),
                'promo' => $promo,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * كوبون نشط للكورس الأونلاين/المسجّل.
     */
    public function getCouponForAdvancedCourse(User $user, AdvancedCourse $course): ?Coupon
    {
        $activation = $this->getActiveActivation($user);
        if (! $activation) {
            return null;
        }

        $promo = $activation->promoCode;
        if (! $promo->appliesToAdvancedCourse($course->id)) {
            return null;
        }

        $coupon = $activation->coupon;
        if ($coupon && $coupon->canBeUsedByUser($user->id)) {
            return $coupon;
        }

        return null;
    }

    /**
     * خصم كورس أوفلاين للطالب.
     *
     * @return array{discount: float, activation: ?WorkshopPromoActivation, promo: ?WorkshopPromoCode}
     */
    public function calculateOfflineDiscount(User $user, OfflineCourse $course, float $listPrice): array
    {
        $activation = $this->getActiveActivation($user);
        if (! $activation || $listPrice <= 0) {
            return ['discount' => 0, 'activation' => null, 'promo' => null];
        }

        $promo = $activation->promoCode;
        if (! $promo || ! $promo->appliesToOfflineCourse($course->id)) {
            return ['discount' => 0, 'activation' => null, 'promo' => null];
        }

        return [
            'discount' => $promo->calculateDiscount($listPrice),
            'activation' => $activation,
            'promo' => $promo,
        ];
    }

    public function getActiveActivation(User $user): ?WorkshopPromoActivation
    {
        return WorkshopPromoActivation::query()
            ->with(['promoCode', 'coupon'])
            ->where('user_id', $user->id)
            ->where('status', WorkshopPromoActivation::STATUS_ACTIVE)
            ->whereHas('promoCode', fn ($q) => $q->active())
            ->latest('activated_at')
            ->first();
    }

    public function markUsed(WorkshopPromoActivation $activation, string $type, int $id): void
    {
        if ($activation->status !== WorkshopPromoActivation::STATUS_ACTIVE) {
            return;
        }

        $activation->update([
            'status' => WorkshopPromoActivation::STATUS_USED,
            'used_at' => now(),
            'used_on_type' => $type,
            'used_on_id' => $id,
        ]);
    }

    private function createPrivateCoupon(WorkshopPromoCode $promo, User $user): Coupon
    {
        do {
            $couponCode = 'WSP-'.strtoupper(Str::random(8));
        } while (Coupon::where('code', $couponCode)->exists());

        $applicableTo = 'courses';
        $courseIds = null;
        if (! empty($promo->applicable_advanced_course_ids)) {
            $applicableTo = 'specific';
            $courseIds = $promo->applicable_advanced_course_ids;
        }

        return Coupon::create([
            'code' => $couponCode,
            'name' => 'خصم ورشة: '.$promo->title,
            'title' => 'خصم ورشة — '.$promo->code,
            'description' => $promo->description ?? 'كود خصم مرتبط بورشة '.$promo->code,
            'discount_type' => $promo->discount_type,
            'discount_value' => $promo->discount_value,
            'minimum_amount' => $promo->minimum_order_amount,
            'maximum_discount' => $promo->maximum_discount,
            'usage_limit' => 1,
            'usage_limit_per_user' => 1,
            'starts_at' => $promo->starts_at,
            'expires_at' => $promo->expires_at,
            'applicable_to' => $applicableTo,
            'applicable_course_ids' => $courseIds,
            'applicable_user_ids' => [$user->id],
            'is_active' => true,
            'is_public' => false,
        ]);
    }
}
