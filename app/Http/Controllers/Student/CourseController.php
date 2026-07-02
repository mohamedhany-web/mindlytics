<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseController extends Controller
{
    /**
     * عرض تفاصيل الكورس
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load(['academicYear', 'academicSubject']);
        
        // التحقق من وجود طلب سابق للطالب
        $existingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->latest()
            ->first();

        // التحقق من التسجيل في الكورس
        $isEnrolled = \App\Models\StudentCourseEnrollment::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', 'active')
            ->exists();

        // جلب المحافظ الإلكترونية النشطة المتاحة للتحويل
        $availableWallets = \App\Models\Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function($query) {
                $query->whereNotNull('account_number')
                      ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $workshopPromoCoupon = null;
        $workshopPromoPreview = null;
        if (auth()->check() && $advancedCourse->effectivePrice() > 0) {
            $workshopPromoCoupon = app(\App\Services\WorkshopPromoService::class)
                ->getCouponForAdvancedCourse(auth()->user(), $advancedCourse);
            if ($workshopPromoCoupon) {
                $basePrice = (float) $advancedCourse->effectivePrice();
                $discount = $workshopPromoCoupon->calculateDiscount($basePrice);
                $workshopPromoPreview = [
                    'code' => $workshopPromoCoupon->code,
                    'title' => $workshopPromoCoupon->title ?? $workshopPromoCoupon->name,
                    'discount_amount' => $discount,
                    'final_amount' => max(0, $basePrice - $discount),
                ];
            }
        }

        return view('student.courses.show', compact(
            'advancedCourse',
            'existingOrder',
            'isEnrolled',
            'availableWallets',
            'workshopPromoCoupon',
            'workshopPromoPreview'
        ));
    }
}