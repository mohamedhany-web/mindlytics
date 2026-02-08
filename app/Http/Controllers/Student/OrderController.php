<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ReferralService;

class OrderController extends Controller
{
    /**
     * عرض طلبات الطالب
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['course.academicSubject', 'course.academicYear', 'learningPath'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }

    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request, AdvancedCourse $advancedCourse)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'wallet_id' => 'nullable|exists:wallets,id',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'wallet_id.exists' => 'المحفظة المختارة غير صحيحة',
            'payment_proof.required' => 'صورة الإيصال مطلوبة',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ]);

        // التحقق من عدم وجود طلب مقبول مسبق
        $existingApprovedOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_APPROVED)
            ->exists();

        if ($existingApprovedOrder) {
            return back()->with('error', 'أنت مسجل بالفعل في هذا الكورس');
        }

        // التحقق من وجود طلب في الانتظار
        $existingPendingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_PENDING)
            ->exists();

        if ($existingPendingOrder) {
            return back()->with('error', 'لديك طلب في الانتظار لهذا الكورس');
        }

        // رفع صورة الإيصال
        $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        // حساب السعر النهائي (بعد خصم الإحالة إذا كان موجوداً)
        $originalAmount = $advancedCourse->price ?? 0;
        $finalAmount = $originalAmount;
        $discountAmount = 0;
        $referralCoupon = null;

        // تطبيق خصم الإحالة تلقائياً
        $referralService = app(ReferralService::class);
        $referralCoupon = $referralService->applyReferralDiscount(auth()->user(), $originalAmount);

        if ($referralCoupon) {
            $discountAmount = $referralCoupon->calculateDiscount($originalAmount);
            $finalAmount = $originalAmount - $discountAmount;
            
            // زيادة عدد مرات استخدام الخصم
            $referral = \App\Models\Referral::where('auto_coupon_id', $referralCoupon->id)->first();
            if ($referral) {
                $referral->incrementDiscountUsage();
                $referral->update(['discount_amount' => $discountAmount]);
            }
        }

        $couponId = null;
        $couponDiscountAmount = 0;

        // التحقق من كوبون يدوي إذا كان موجوداً
        if ($request->filled('applied_coupon_id')) {
            $coupon = Coupon::find($request->applied_coupon_id);
            if ($coupon && $coupon->isValid() && $coupon->canBeUsedByUser(auth()->id())) {
                // التحقق من أن الكوبون ينطبق على الكورس
                $canApply = true;
                if ($coupon->applicable_to === 'specific' && $coupon->applicable_course_ids) {
                    if (!in_array($advancedCourse->id, $coupon->applicable_course_ids)) {
                        $canApply = false;
                    }
                }

                if ($canApply) {
                    $couponDiscountAmount = $coupon->calculateDiscount($finalAmount);
                    $discountAmount += $couponDiscountAmount;
                    $finalAmount -= $couponDiscountAmount;
                    $couponId = $coupon->id;
                    
                    // زيادة عدد مرات استخدام الكوبون
                    $coupon->incrementUsage();
                    
                    // تسجيل استخدام الكوبون
                    \App\Models\CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => auth()->id(),
                        'discount_amount' => $couponDiscountAmount,
                        'order_amount' => $originalAmount,
                        'final_amount' => $finalAmount,
                    ]);
                }
            }
        }

        // إنشاء الطلب
        $orderData = [
            'user_id' => auth()->id(),
            'advanced_course_id' => $advancedCourse->id,
            'coupon_id' => $couponId,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'amount' => $finalAmount, // السعر النهائي بعد الخصم
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'notes' => $request->notes ?? '',
            'status' => Order::STATUS_PENDING,
        ];

        // إضافة ملاحظة عن الخصومات
        $discountNotes = [];
        if ($referralCoupon && isset($discountAmount)) {
            $referralDiscountAmount = $discountAmount - $couponDiscountAmount;
            if ($referralDiscountAmount > 0) {
                $discountNotes[] = "خصم الإحالة: " . number_format($referralDiscountAmount, 2) . " ج.م";
            }
        }
        if ($couponDiscountAmount > 0) {
            $discountNotes[] = "خصم الكوبون (" . ($coupon->code ?? '') . "): " . number_format($couponDiscountAmount, 2) . " ج.م";
        }
        if (!empty($discountNotes)) {
            $orderData['notes'] .= (!empty($orderData['notes']) ? "\n" : '') . implode("\n", $discountNotes);
        }

        // إضافة wallet_id إذا كان موجوداً
        if ($request->filled('wallet_id')) {
            $orderData['wallet_id'] = $request->wallet_id;
        }

        Order::create($orderData);

        return back()->with('success', 'تم إرسال طلبك بنجاح! سيتم مراجعته قريباً');
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        // التأكد من أن الطلب يخص الطالب الحالي
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['course.academicSubject', 'course.academicYear', 'learningPath', 'approver']);

        return view('student.orders.show', compact('order'));
    }
}