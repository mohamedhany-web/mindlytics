<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من أن المستخدم يملك المورد الذي يحاول الوصول إليه
 * يستخدم للتأكد من أن المستخدم لا يمكنه الوصول إلى موارد مستخدمين آخرين
 */
class EnsureOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $resource  اسم المورد (user, order, etc.)
     * @param  string  $parameter  اسم المعامل في الـ route (user, order, etc.)
     */
    public function handle(Request $request, Closure $next, string $resource = 'user', string $parameter = 'id'): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // إذا كان المستخدم admin، يتجاوز التحقق من الملكية
        if ($user->isAdmin()) {
            return $next($request);
        }

        // الحصول على القيمة من الـ route parameter (قد تكون id أو نموذجاً بعد الربط التلقائي)
        $param = $request->route($parameter);

        if ($param === null) {
            abort(404, 'المورد غير موجود');
        }

        $resourceId = $param instanceof \Illuminate\Database\Eloquent\Model
            ? $param->getKey()
            : $param;

        // التحقق من الملكية بناءً على نوع المورد
        switch ($resource) {
            case 'user':
                if ($user->id != $resourceId) {
                    abort(403, 'غير مسموح لك بالوصول إلى هذه البيانات');
                }
                break;

            case 'order':
                $order = $param instanceof \App\Models\Order ? $param : \App\Models\Order::findOrFail($resourceId);
                if ($order->user_id != $user->id) {
                    abort(403, 'غير مسموح لك بالوصول إلى هذا الطلب');
                }
                break;

            case 'assignment':
                $assignment = $param instanceof \App\Models\Assignment ? $param : \App\Models\Assignment::findOrFail($resourceId);
                // التحقق من أن المستخدم هو الطالب أو المدرب
                if ($user->isStudent()) {
                    // للطلاب: التحقق من التسجيل في الكورس
                    if (!$user->isEnrolledIn($assignment->advanced_course_id)) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذا الواجب');
                    }
                } elseif ($user->isInstructor()) {
                    // للمدربين: التحقق من أن الواجب يخص الكورس الذي يدرسه
                    $course = \App\Models\AdvancedCourse::find($assignment->advanced_course_id);
                    if (!$course || $course->instructor_id != $user->id) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذا الواجب');
                    }
                } else {
                    abort(403, 'غير مسموح لك بالوصول إلى هذا الواجب');
                }
                break;

            case 'course':
                $course = $param instanceof \App\Models\AdvancedCourse ? $param : \App\Models\AdvancedCourse::findOrFail($resourceId);
                if ($user->isStudent()) {
                    // للطلاب: التحقق من التسجيل
                    if (!$user->isEnrolledIn($resourceId)) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذا الكورس');
                    }
                } elseif ($user->isInstructor()) {
                    // للمدربين: التحقق من أن الكورس يخصه
                    if ($course->instructor_id != $user->id) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذا الكورس');
                    }
                } else {
                    abort(403, 'غير مسموح لك بالوصول إلى هذا الكورس');
                }
                break;

            case 'enrollment':
                $enrollment = $param instanceof \App\Models\StudentCourseEnrollment ? $param : \App\Models\StudentCourseEnrollment::findOrFail($resourceId);
                if ($user->isStudent()) {
                    if ($enrollment->user_id != $user->id) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذه التسجيلات');
                    }
                } elseif ($user->isInstructor()) {
                    $course = $enrollment->course;
                    if (!$course || $course->instructor_id != $user->id) {
                        abort(403, 'غير مسموح لك بالوصول إلى هذه التسجيلات');
                    }
                } else {
                    abort(403, 'غير مسموح لك بالوصول إلى هذه التسجيلات');
                }
                break;

            case 'wallet':
                $wallet = $param instanceof \App\Models\Wallet ? $param : \App\Models\Wallet::findOrFail($resourceId);
                if ($wallet->user_id != $user->id) {
                    abort(403, 'غير مسموح لك بالوصول إلى هذه المحفظة');
                }
                break;

            case 'invoice':
                $invoice = $param instanceof \App\Models\Invoice ? $param : \App\Models\Invoice::findOrFail($resourceId);
                if ($invoice->user_id != $user->id) {
                    abort(403, 'غير مسموح لك بالوصول إلى هذه الفاتورة');
                }
                break;

            default:
                // للموارد المخصصة، يمكن تمديد هذا
                break;
        }

        return $next($request);
    }
}
