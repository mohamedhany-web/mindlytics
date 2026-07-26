<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * الخصومات الشخصية للعميل (كوبونات مخصّصة له وحده): استبيان العملاء، الإحالات،
 * أكواد الورش، وأي كوبون يضيفه الأدمن لمستخدم معيّن.
 *
 * يستخدمه الشيك أوت العام والمحفظة لعرض الخصم وتطبيقه.
 */
class CustomerDiscountService
{
    /**
     * كل الكوبونات الشخصية الصالحة للمستخدم، مرتبة بالأقرب انتهاءً.
     *
     * @return Collection<int, Coupon>
     */
    public function availableCoupons(?User $user): Collection
    {
        if (! $user) {
            return collect();
        }

        return Coupon::active()
            ->whereNotNull('applicable_user_ids')
            ->whereJsonContains('applicable_user_ids', $user->id)
            ->orderByRaw('expires_at is null asc')
            ->orderBy('expires_at')
            ->get()
            ->filter(fn (Coupon $coupon) => $coupon->canBeUsedByUser($user->id))
            ->values();
    }

    /**
     * أفضل كوبون شخصي لكورس معيّن (أعلى قيمة خصم فعلية).
     */
    public function bestCouponForCourse(?User $user, AdvancedCourse $course): ?Coupon
    {
        if (! $user) {
            return null;
        }

        $price = $course->effectivePrice();

        if ($price <= 0) {
            return null;
        }

        return $this->availableCoupons($user)
            ->filter(fn (Coupon $coupon) => $this->appliesToCourse($coupon, $course))
            ->sortByDesc(fn (Coupon $coupon) => $coupon->calculateDiscount($price))
            ->filter(fn (Coupon $coupon) => $coupon->calculateDiscount($price) > 0)
            ->first();
    }

    /**
     * كوبون بكود محدد يرسله المستخدم، بشرط أن يكون صالحاً له ولهذا الكورس.
     */
    public function couponByCode(?User $user, AdvancedCourse $course, ?string $code): ?Coupon
    {
        $code = trim((string) $code);

        if ($code === '' || ! $user) {
            return null;
        }

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [mb_strtoupper($code)])->first();

        if (! $coupon || ! $coupon->canBeUsedByUser($user->id)) {
            return null;
        }

        if (! $this->appliesToCourse($coupon, $course)) {
            return null;
        }

        return $coupon->calculateDiscount($course->effectivePrice()) > 0 ? $coupon : null;
    }

    public function appliesToCourse(Coupon $coupon, AdvancedCourse $course): bool
    {
        return match ($coupon->applicable_to) {
            'specific' => in_array($course->id, (array) $coupon->applicable_course_ids, false),
            'subscriptions' => false,
            default => true,
        };
    }

    /**
     * تفصيل السعر النهائي: خصم الكورس + خصم الكوبون الشخصي.
     *
     * @return array{original_amount: float, course_discount: float, coupon_discount: float, discount_amount: float, amount: float, coupon: ?Coupon}
     */
    public function breakdown(AdvancedCourse $course, ?Coupon $coupon = null): array
    {
        $pricing = $course->paymentBreakdown();
        $original = (float) $pricing['original_amount'];
        $courseDiscount = (float) $pricing['discount_amount'];
        $afterCourseDiscount = (float) $pricing['amount'];

        $couponDiscount = 0.0;

        if ($coupon && $afterCourseDiscount > 0) {
            $couponDiscount = (float) $coupon->calculateDiscount($afterCourseDiscount);
        }

        $final = max(0, round($afterCourseDiscount - $couponDiscount, 2));

        return [
            'original_amount' => round($original, 2),
            'course_discount' => round($courseDiscount, 2),
            'coupon_discount' => round($couponDiscount, 2),
            'discount_amount' => round($courseDiscount + $couponDiscount, 2),
            'amount' => $final,
            'coupon' => $couponDiscount > 0 ? $coupon : null,
        ];
    }

    /**
     * تسجيل استهلاك الكوبون. آمن للتكرار: لا يسجّل مرتين لنفس (كوبون + مستخدم).
     */
    public function markUsed(Coupon $coupon, User $user, float $orderAmount, float $discountAmount, ?int $invoiceId = null): void
    {
        if ($discountAmount <= 0) {
            return;
        }

        $alreadyRecorded = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRecorded) {
            return;
        }

        try {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'invoice_id' => $invoiceId,
                'discount_amount' => $discountAmount,
                'order_amount' => $orderAmount,
                'final_amount' => max(0, round($orderAmount - $discountAmount, 2)),
            ]);

            $coupon->incrementUsage();
        } catch (\Throwable $e) {
            Log::warning('Failed to record coupon usage', [
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
