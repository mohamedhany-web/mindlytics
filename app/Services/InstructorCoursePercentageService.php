<?php

namespace App\Services;

use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
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
    public static function processEnrollmentActivation(
        StudentCourseEnrollment $enrollment,
        ?InstructorAgreement $agreement = null,
        bool $rethrowOnFailure = false,
    ): ?AgreementPayment {
        if ($enrollment->status !== 'active' || !$enrollment->advanced_course_id) {
            return null;
        }

        if ($enrollment->isHiddenFromInstructor()) {
            return null;
        }

        if ($enrollment->isScholarshipEnrollment()) {
            return null;
        }

        $course = $enrollment->course;
        if (! $course) {
            return null;
        }

        if ($agreement) {
            if (! $agreement->isCoursePercentageType()
                || (int) $agreement->advanced_course_id !== (int) $enrollment->advanced_course_id
                || $agreement->status !== InstructorAgreement::STATUS_ACTIVE) {
                return null;
            }
        } else {
            $agreement = self::findAgreementForEnrollment($enrollment, $course);
        }

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
        if ($finalPrice <= 0) {
            return null;
        }
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
                $createdBy = $enrollment->activated_by;
                if ($createdBy && ! User::query()->whereKey($createdBy)->exists()) {
                    $createdBy = null;
                }

                $payment = AgreementPayment::create([
                    'agreement_id' => $agreement->id,
                    'instructor_id' => $agreement->instructor_id,
                    'type' => AgreementPayment::TYPE_COURSE_ACTIVATION,
                    'amount' => $instructorAmount,
                    'status' => AgreementPayment::STATUS_APPROVED,
                    'description' => 'نسبة من تفعيل الطالب للكورس: ' . ($enrollment->course->title ?? '') . $discountNote,
                    'related_course_id' => $enrollment->advanced_course_id,
                    'student_course_enrollment_id' => $enrollment->id,
                    'payment_date' => $enrollment->activated_at ?? now(),
                    'created_by' => $createdBy,
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
                'agreement_id' => $agreement->id,
                'message' => $e->getMessage(),
            ]);
            if ($rethrowOnFailure) {
                throw $e;
            }

            return null;
        }
    }

    /**
     * إنشاء مدفوعات نسبة المدرب الناقصة لتفعيلات كورس مرتبطة باتفاقية محددة.
     *
     * @return AgreementPayment[]
     */
    public static function syncMissingPaymentsForAgreementDetailed(InstructorAgreement $agreement): array
    {
        if (! $agreement->isCoursePercentageType() || $agreement->status !== InstructorAgreement::STATUS_ACTIVE) {
            return [];
        }

        $created = [];

        StudentCourseEnrollment::query()
            ->where('advanced_course_id', $agreement->advanced_course_id)
            ->where('status', 'active')
            ->visibleToInstructor()
            ->with(['course', 'student', 'invoice', 'payment'])
            ->orderBy('id')
            ->chunkById(100, function ($enrollments) use ($agreement, &$created) {
                foreach ($enrollments as $enrollment) {
                    if (self::resolveActivationBaseAmount($enrollment, $enrollment->course) <= 0) {
                        continue;
                    }

                    try {
                        $payment = self::processEnrollmentActivation($enrollment, $agreement);
                        if ($payment) {
                            $created[] = $payment->loadMissing(['enrollment.student', 'enrollment.course']);
                        }
                    } catch (\Throwable $e) {
                        Log::warning('InstructorCoursePercentageService: sync skipped enrollment', [
                            'agreement_id' => $agreement->id,
                            'enrollment_id' => $enrollment->id,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }
            });

        return $created;
    }

    public static function syncMissingPaymentsForAgreement(InstructorAgreement $agreement): int
    {
        return count(self::syncMissingPaymentsForAgreementDetailed($agreement));
    }

    /**
     * مزامنة كل مدفوعات نسبة المدرب الناقصة لكل الاتفاقيات النشطة من نوع نسبة من الكورس.
     */
    public static function syncAllMissingPayments(): int
    {
        $total = 0;

        InstructorAgreement::query()
            ->coursePercentageActive()
            ->orderBy('id')
            ->each(function (InstructorAgreement $agreement) use (&$total) {
                $total += self::syncMissingPaymentsForAgreement($agreement);
            });

        return $total;
    }

    public static function findAgreementForEnrollment(
        StudentCourseEnrollment $enrollment,
        ?\App\Models\AdvancedCourse $course = null,
    ): ?InstructorAgreement {
        $course ??= $enrollment->course;
        if (! $course) {
            return null;
        }

        $baseQuery = InstructorAgreement::query()
            ->coursePercentageActive()
            ->where('advanced_course_id', $enrollment->advanced_course_id);

        if ($course->instructor_id) {
            $matched = (clone $baseQuery)
                ->where('instructor_id', $course->instructor_id)
                ->orderByDesc('id')
                ->first();
            if ($matched) {
                return $matched;
            }
        }

        return $baseQuery->orderByDesc('id')->first();
    }

    /**
     * @return array{eligible: bool, reason: string, amount: float}
     */
    public static function diagnoseEnrollmentForAgreement(
        StudentCourseEnrollment $enrollment,
        InstructorAgreement $agreement,
    ): array {
        if ($enrollment->status !== 'active') {
            return ['eligible' => false, 'reason' => 'التسجيل غير نشط', 'amount' => 0];
        }

        if ((int) $enrollment->advanced_course_id !== (int) $agreement->advanced_course_id) {
            return ['eligible' => false, 'reason' => 'كورس مختلف عن الاتفاقية', 'amount' => 0];
        }

        if ($enrollment->isHiddenFromInstructor()) {
            return ['eligible' => false, 'reason' => 'تفعيل مجاني / مخفي عن المدرب', 'amount' => 0];
        }

        $amount = self::resolveActivationBaseAmount($enrollment, $enrollment->course);
        if ($amount <= 0) {
            return ['eligible' => false, 'reason' => 'لا يوجد مبلغ مدفوع مسجل', 'amount' => 0];
        }

        $exists = AgreementPayment::query()
            ->where('agreement_id', $agreement->id)
            ->where('student_course_enrollment_id', $enrollment->id)
            ->where('type', AgreementPayment::TYPE_COURSE_ACTIVATION)
            ->exists();

        if ($exists) {
            return ['eligible' => false, 'reason' => 'موجود بالفعل في الاتفاقية', 'amount' => $amount];
        }

        return ['eligible' => true, 'reason' => 'يحتاج مزامنة', 'amount' => $amount];
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
            $computed = round(max(0, $original - $discount), 2);
            if ($computed > 0) {
                return $computed;
            }
        }

        if ($enrollment->invoice_id) {
            $invoice = $enrollment->relationLoaded('invoice')
                ? $enrollment->invoice
                : \App\Models\Invoice::query()->find($enrollment->invoice_id);
            if ($invoice && (float) $invoice->total_amount > 0) {
                return round((float) $invoice->total_amount, 2);
            }
        }

        if ($enrollment->payment_id) {
            $payment = $enrollment->relationLoaded('payment')
                ? $enrollment->payment
                : \App\Models\Payment::query()->find($enrollment->payment_id);
            if ($payment && (float) $payment->amount > 0) {
                return round((float) $payment->amount, 2);
            }
        }

        if ($course) {
            return round($course->effectivePrice(), 2);
        }

        return 0;
    }
}
