<?php

namespace App\Console\Commands;

use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\StudentCourseEnrollment;
use App\Services\InstructorCoursePercentageService;
use Illuminate\Console\Command;

class SyncInstructorAgreementPaymentsCommand extends Command
{
    protected $signature = 'agreements:sync-instructor-payments
                            {--agreement= : رقم اتفاقية محددة (مثال: 6)}
                            {--dry-run : عرض التفعيلات الناقصة فقط بدون إنشاء مدفوعات}';

    protected $description = 'مزامنة مدفوعات نسبة المدرب للتفعيلات السابقة التي لم تُسجّل في الاتفاقية';

    public function handle(): int
    {
        $agreementId = $this->option('agreement');
        $dryRun = (bool) $this->option('dry-run');

        $query = InstructorAgreement::query()
            ->where('billing_type', InstructorAgreement::BILLING_COURSE_PERCENTAGE)
            ->where('status', InstructorAgreement::STATUS_ACTIVE)
            ->whereNotNull('advanced_course_id')
            ->whereNotNull('course_percentage')
            ->with(['instructor', 'advancedCourse']);

        if ($agreementId) {
            $query->whereKey((int) $agreementId);
        }

        $agreements = $query->orderBy('id')->get();

        if ($agreements->isEmpty()) {
            $this->warn('لا توجد اتفاقيات نشطة من نوع «نسبة من الكورس»'
                . ($agreementId ? " برقم {$agreementId}" : '') . '.');

            return self::FAILURE;
        }

        $totalCreated = 0;
        $totalPending = 0;

        foreach ($agreements as $agreement) {
            $courseTitle = $agreement->advancedCourse?->title ?? '—';
            $instructorName = $agreement->instructor?->name ?? '—';

            $this->newLine();
            $this->info("اتفاقية #{$agreement->id} — {$agreement->agreement_number}");
            $this->line("المدرب: {$instructorName} | الكورس: {$courseTitle} | النسبة: {$agreement->course_percentage}%");

            $missing = $this->findMissingEnrollments($agreement);

            if ($missing->isEmpty()) {
                $this->comment('  لا يوجد تفعيلات ناقصة — كل شيء محدّث.');
                continue;
            }

            $totalPending += $missing->count();

            if ($dryRun) {
                $this->table(
                    ['#', 'الطالب', 'البريد', 'المبلغ المدفوع', 'نسبة المدرب'],
                    $missing->map(function (StudentCourseEnrollment $enrollment, int $index) use ($agreement) {
                        $base = InstructorCoursePercentageService::resolveActivationBaseAmount($enrollment, $enrollment->course);
                        $amount = round($base * ((float) $agreement->course_percentage / 100), 2);

                        return [
                            $index + 1,
                            $enrollment->student?->name ?? '—',
                            $enrollment->student?->email ?? '—',
                            number_format($base, 2) . ' ج.م',
                            number_format($amount, 2) . ' ج.م',
                        ];
                    })->all()
                );
                continue;
            }

            $created = InstructorCoursePercentageService::syncMissingPaymentsForAgreementDetailed($agreement);
            $totalCreated += count($created);

            if ($created === []) {
                $this->comment('  لم يُنشأ شيء (ربما فشلت بعض السجلات — راجع laravel.log).');
                continue;
            }

            $this->table(
                ['#', 'الطالب', 'المبلغ', 'رقم المدفوعة'],
                collect($created)->map(function (AgreementPayment $payment, int $index) {
                    return [
                        $index + 1,
                        $payment->enrollment?->student?->name ?? '—',
                        number_format((float) $payment->amount, 2) . ' ج.م',
                        $payment->payment_number,
                    ];
                })->all()
            );
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("معاينة فقط: {$totalPending} تفعيل(ات) تحتاج مزامنة.");
            $this->comment('لتنفيذ المزامنة شغّل الأمر بدون --dry-run');

            return self::SUCCESS;
        }

        $this->info("تم إنشاء {$totalCreated} مدفوعة مدرب في الاتفاقيات.");

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Support\Collection<int, StudentCourseEnrollment>
     */
    private function findMissingEnrollments(InstructorAgreement $agreement)
    {
        $enrollments = StudentCourseEnrollment::query()
            ->where('advanced_course_id', $agreement->advanced_course_id)
            ->where('status', 'active')
            ->visibleToInstructor()
            ->with(['course', 'student'])
            ->orderBy('id')
            ->get();

        return $enrollments->filter(function (StudentCourseEnrollment $enrollment) use ($agreement) {
            $course = $enrollment->course;
            if (! $course || (int) $course->instructor_id !== (int) $agreement->instructor_id) {
                return false;
            }

            if (InstructorCoursePercentageService::resolveActivationBaseAmount($enrollment, $course) <= 0) {
                return false;
            }

            return ! AgreementPayment::query()
                ->where('agreement_id', $agreement->id)
                ->where('student_course_enrollment_id', $enrollment->id)
                ->where('type', AgreementPayment::TYPE_COURSE_ACTIVATION)
                ->exists();
        })->values();
    }
}
