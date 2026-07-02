<?php

namespace App\Services;

use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\StudentCourseEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * عند تفعيل تسجيل الطالب في كورس أونلاين، إنشاء دفع للمدرب إذا وُجدت اتفاقية "نسبة من الكورس".
 */
class InstructorCoursePercentageService
{
    /**
     * معالجة تفعيل تسجيل طالب: إنشاء مدفوعة نسبة من الكورس للمدرب إن وُجدت اتفاقية نشطة.
     */
    public static function processEnrollmentActivation(StudentCourseEnrollment $enrollment): ?AgreementPayment
    {
        if ($enrollment->status !== 'active' || !$enrollment->advanced_course_id) {
            return null;
        }

        $course = $enrollment->course;
        if (!$course || !$course->instructor_id) {
            return null;
        }

        $agreement = InstructorAgreement::where('instructor_id', $course->instructor_id)
            ->where('advanced_course_id', $enrollment->advanced_course_id)
            ->where('billing_type', InstructorAgreement::BILLING_COURSE_PERCENTAGE)
            ->where('status', InstructorAgreement::STATUS_ACTIVE)
            ->whereNotNull('course_percentage')
            ->first();

        if (!$agreement) {
            Log::debug('InstructorCoursePercentageService: no active agreement for course', [
                'course_id' => $enrollment->advanced_course_id,
                'instructor_id' => $course->instructor_id,
            ]);
            return null;
        }

        // تجنب تكرار دفع لنفس التفعيل
        $exists = AgreementPayment::where('agreement_id', $agreement->id)
            ->where('student_course_enrollment_id', $enrollment->id)
            ->where('type', AgreementPayment::TYPE_COURSE_ACTIVATION)
            ->exists();

        if ($exists) {
            return null;
        }

        // مبلغ التفعيل بعد الخصم — يُستخدم لحساب نسبة المدرب
        $finalPrice = self::resolveActivationBaseAmount($enrollment, $course);
        $percentage = (float) $agreement->course_percentage;
        $instructorAmount = round($finalPrice * ($percentage / 100), 2);

        $discountNote = '';
        $original = (float) ($enrollment->original_price ?? 0);
        $discount = (float) ($enrollment->discount_amount ?? 0);
        if ($discount > 0 && $original > 0) {
            $discountNote = sprintf(' (بعد خصم %.2f من %.2f ج.م)', $discount, $original);
        }

        try {
            return DB::transaction(function () use ($agreement, $enrollment, $instructorAmount, $discountNote) {
                $payment = AgreementPayment::create([
                    'agreement_id' => $agreement->id,
                    'instructor_id' => $agreement->instructor_id,
                    'type' => AgreementPayment::TYPE_COURSE_ACTIVATION,
                    'amount' => $instructorAmount,
                    'status' => AgreementPayment::STATUS_APPROVED,
                    'description' => 'نسبة من تفعيل الطالب للكورس: ' . ($enrollment->course->title ?? '') . $discountNote,
                    'related_course_id' => $enrollment->advanced_course_id,
                    'student_course_enrollment_id' => $enrollment->id,
                    'payment_date' => now(),
                    'created_by' => $enrollment->activated_by,
                ]);
                Log::info('Instructor course percentage payment created', [
                    'agreement_id' => $agreement->id,
                    'enrollment_id' => $enrollment->id,
                    'amount' => $instructorAmount,
                ]);
                return $payment;
            });
        } catch (\Throwable $e) {
            Log::error('InstructorCoursePercentageService::processEnrollmentActivation failed', [
                'enrollment_id' => $enrollment->id,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * المبلغ الذي تُحسب عليه نسبة المدرب (بعد الخصم إن وُجد).
     */
    public static function resolveActivationBaseAmount(StudentCourseEnrollment $enrollment, ?\App\Models\AdvancedCourse $course = null): float
    {
        $course ??= $enrollment->course;

        $final = (float) ($enrollment->final_price ?? 0);
        if ($final > 0) {
            return round($final, 2);
        }

        $original = (float) ($enrollment->original_price ?? 0);
        $discount = (float) ($enrollment->discount_amount ?? 0);
        if ($original > 0) {
            return round(max(0, $original - $discount), 2);
        }

        if ($course) {
            return round($course->effectivePrice(), 2);
        }

        return 0;
    }
}
