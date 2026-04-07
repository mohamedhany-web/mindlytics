<?php

namespace App\Services;

use App\Models\InstallmentAgreement;
use App\Models\InstallmentPlan;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\StudentCourseEnrollment;
use Carbon\Carbon;

class AutoInstallmentAgreementService
{
    public function ensureFromOfflineEnrollment(OfflineCourseEnrollment $enrollment, ?int $createdBy = null): ?InstallmentAgreement
    {
        $enrollment->loadMissing(['course']);

        $totalAmount = (float) ($enrollment->total_amount ?? $enrollment->course?->price ?? 0);
        $paidAmount = (float) ($enrollment->paid_amount ?? 0);
        $remainingAmount = max(0.0, $totalAmount - $paidAmount);

        if ($totalAmount <= 0 || $remainingAmount <= 0 || $paidAmount <= 0) {
            return null;
        }

        $activeExists = InstallmentAgreement::query()
            ->where('offline_course_enrollment_id', $enrollment->id)
            ->whereIn('status', [InstallmentAgreement::STATUS_ACTIVE, InstallmentAgreement::STATUS_OVERDUE])
            ->exists();

        if ($activeExists) {
            return null;
        }

        $plan = InstallmentPlan::query()
            ->active()
            ->where('auto_generate_on_enrollment', true)
            ->whereNull('advanced_course_id')
            ->orderBy('installments_count')
            ->first();

        if (! $plan) {
            return null;
        }

        $agreement = InstallmentAgreement::create([
            'installment_plan_id' => $plan->id,
            'student_course_enrollment_id' => null,
            'offline_course_enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'advanced_course_id' => null,
            'total_amount' => $totalAmount,
            'deposit_amount' => $paidAmount,
            'installments_count' => max(1, (int) $plan->installments_count),
            'start_date' => Carbon::today(),
            'status' => InstallmentAgreement::STATUS_ACTIVE,
            'notes' => 'تم الإنشاء تلقائياً بسبب دفع جزئي لتسجيل أوفلاين.',
            'created_by' => $createdBy,
        ]);

        $agreement->payments()->delete();
        $agreement->generateSchedule(Carbon::parse($agreement->start_date));

        return $agreement;
    }

    public function ensureFromInvoice(Invoice $invoice, ?int $createdBy = null): ?InstallmentAgreement
    {
        $invoice->refresh();
        $totalAmount = (float) $invoice->total_amount;
        $remainingAmount = (float) $invoice->remaining_amount;
        $paidAmount = max(0.0, $totalAmount - $remainingAmount);

        if ($totalAmount <= 0 || $remainingAmount <= 0 || $paidAmount <= 0) {
            return null;
        }

        $offlineEnrollment = OfflineCourseEnrollment::query()
            ->where('invoice_id', $invoice->id)
            ->first();
        if ($offlineEnrollment) {
            return $this->ensureFromOfflineEnrollment($offlineEnrollment, $createdBy);
        }

        $studentEnrollment = StudentCourseEnrollment::query()
            ->where('invoice_id', $invoice->id)
            ->first();
        if (! $studentEnrollment) {
            return null;
        }

        $activeExists = InstallmentAgreement::query()
            ->where('student_course_enrollment_id', $studentEnrollment->id)
            ->whereIn('status', [InstallmentAgreement::STATUS_ACTIVE, InstallmentAgreement::STATUS_OVERDUE])
            ->exists();
        if ($activeExists) {
            return null;
        }

        $plan = InstallmentPlan::query()
            ->active()
            ->where('auto_generate_on_enrollment', true)
            ->where(function ($q) use ($studentEnrollment) {
                $q->where('advanced_course_id', $studentEnrollment->advanced_course_id)
                    ->orWhereNull('advanced_course_id');
            })
            ->orderByRaw('CASE WHEN advanced_course_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('installments_count')
            ->first();

        if (! $plan) {
            return null;
        }

        $agreement = InstallmentAgreement::create([
            'installment_plan_id' => $plan->id,
            'student_course_enrollment_id' => $studentEnrollment->id,
            'offline_course_enrollment_id' => null,
            'user_id' => $studentEnrollment->user_id,
            'advanced_course_id' => $studentEnrollment->advanced_course_id,
            'total_amount' => $totalAmount,
            'deposit_amount' => $paidAmount,
            'installments_count' => max(1, (int) $plan->installments_count),
            'start_date' => Carbon::today(),
            'status' => InstallmentAgreement::STATUS_ACTIVE,
            'notes' => 'تم الإنشاء تلقائياً بسبب دفع جزئي لفاتورة الكورس.',
            'created_by' => $createdBy,
        ]);

        $agreement->payments()->delete();
        $agreement->generateSchedule(Carbon::parse($agreement->start_date));

        return $agreement;
    }
}

