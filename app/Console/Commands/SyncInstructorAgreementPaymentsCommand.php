<?php

namespace App\Console\Commands;

use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\StudentCourseEnrollment;
use App\Services\InstructorCoursePercentageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInstructorAgreementPaymentsCommand extends Command
{
    protected $signature = 'agreements:sync-instructor-payments
                            {--agreement= : رقم اتفاقية محددة (مثال: 6)}
                            {--dry-run : عرض التفعيلات الناقصة فقط بدون إنشاء مدفوعات}
                            {--diagnose : عرض كل التفعيلات على كورس الاتفاقية وسبب تخطيها أو قبولها}';

    protected $description = 'مزامنة مدفوعات نسبة المدرب للتفعيلات السابقة التي لم تُسجّل في الاتفاقية';

    public function handle(): int
    {
        $agreementId = $this->option('agreement');
        $dryRun = (bool) $this->option('dry-run');
        $diagnose = (bool) $this->option('diagnose');

        $agreements = $this->resolveAgreements($agreementId);
        if ($agreements === null) {
            return self::FAILURE;
        }

        $this->ensureAgreementPaymentsSchema();

        $totalCreated = 0;
        $totalPending = 0;

        foreach ($agreements as $agreement) {
            $courseTitle = $agreement->advancedCourse?->title ?? '—';
            $instructorName = $agreement->instructor?->name ?? '—';

            $this->newLine();
            $this->info("اتفاقية #{$agreement->id} — {$agreement->agreement_number}");
            $this->line("المدرب: {$instructorName} | الكورس: {$courseTitle} (#{$agreement->advanced_course_id}) | النسبة: {$agreement->course_percentage}%");
            $this->line('billing_type: ' . ($agreement->billing_type ?? '—') . ' | الحالة: ' . ($agreement->status ?? '—'));

            if ($diagnose) {
                $this->printDiagnosis($agreement);
                continue;
            }

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

            $created = [];
            $errors = [];

            foreach ($missing as $enrollment) {
                try {
                    $payment = InstructorCoursePercentageService::processEnrollmentActivation(
                        $enrollment,
                        $agreement,
                        rethrowOnFailure: true,
                    );
                    if ($payment) {
                        $payment->loadMissing(['enrollment.student']);
                        $created[] = $payment;
                    } else {
                        $errors[] = ($enrollment->student?->name ?? '#' . $enrollment->id)
                            . ': لم يُنشأ السجل (تحقق من شروط الاتفاقية أو السجل المكرر).';
                    }
                } catch (\Throwable $e) {
                    $errors[] = ($enrollment->student?->name ?? '#' . $enrollment->id) . ': ' . $e->getMessage();
                }
            }

            $totalCreated += count($created);

            if ($created !== []) {
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

            if ($errors !== []) {
                $this->error('  أخطاء أثناء المزامنة:');
                foreach ($errors as $error) {
                    $this->line('  - ' . $error);
                }
            }

            if ($created === [] && $errors === []) {
                $this->comment('  لم يُنشأ شيء — شغّل --diagnose لمعرفة السبب.');
            }
        }

        $this->newLine();
        if ($diagnose) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("معاينة فقط: {$totalPending} تفعيل(ات) تحتاج مزامنة.");
            $this->comment('لتنفيذ المزامنة شغّل الأمر بدون --dry-run');

            return self::SUCCESS;
        }

        $this->info("تم إنشاء {$totalCreated} مدفوعة مدرب في الاتفاقيات.");

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Support\Collection<int, InstructorAgreement>|null
     */
    private function resolveAgreements(?string $agreementId)
    {
        if ($agreementId) {
            $agreement = InstructorAgreement::query()
                ->with(['instructor', 'advancedCourse'])
                ->find((int) $agreementId);

            if (! $agreement) {
                $this->error("الاتفاقية رقم {$agreementId} غير موجودة.");

                return null;
            }

            if (! $agreement->isCoursePercentageType()) {
                $this->error('الاتفاقية ليست من نوع «نسبة من الكورس» أو لا يوجد كورس/نسبة محددة.');
                $this->line('billing_type: ' . ($agreement->billing_type ?? '—'));
                $this->line('advanced_course_id: ' . ($agreement->advanced_course_id ?? '—'));
                $this->line('course_percentage: ' . ($agreement->course_percentage ?? '—'));

                return null;
            }

            if ($agreement->status !== InstructorAgreement::STATUS_ACTIVE) {
                $this->warn('الاتفاقية ليست نشطة — سيتم المزامنة على أي حال.');
            }

            return collect([$agreement]);
        }

        $agreements = InstructorAgreement::query()
            ->coursePercentageActive()
            ->with(['instructor', 'advancedCourse'])
            ->orderBy('id')
            ->get();

        if ($agreements->isEmpty()) {
            $this->warn('لا توجد اتفاقيات نشطة من نوع «نسبة من الكورس».');

            return null;
        }

        return $agreements;
    }

    private function printDiagnosis(InstructorAgreement $agreement): void
    {
        $enrollments = StudentCourseEnrollment::query()
            ->where('advanced_course_id', $agreement->advanced_course_id)
            ->with(['student', 'course', 'invoice', 'payment'])
            ->orderBy('id')
            ->get();

        if ($enrollments->isEmpty()) {
            $this->warn('  لا يوجد أي تسجيلات على كورس هذه الاتفاقية.');

            return;
        }

        $this->table(
            ['#', 'الطالب', 'الحالة', 'المبلغ', 'السبب'],
            $enrollments->map(function (StudentCourseEnrollment $enrollment, int $index) use ($agreement) {
                $diag = InstructorCoursePercentageService::diagnoseEnrollmentForAgreement($enrollment, $agreement);

                return [
                    $index + 1,
                    $enrollment->student?->name ?? '—',
                    $enrollment->status,
                    number_format($diag['amount'], 2) . ' ج.م',
                    $diag['reason'],
                ];
            })->all()
        );
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
            ->with(['course', 'student', 'invoice', 'payment'])
            ->orderBy('id')
            ->get();

        return $enrollments->filter(function (StudentCourseEnrollment $enrollment) use ($agreement) {
            return InstructorCoursePercentageService::diagnoseEnrollmentForAgreement($enrollment, $agreement)['eligible'];
        })->values();
    }

    private function ensureAgreementPaymentsSchema(): void
    {
        $issues = [];

        if (! \Illuminate\Support\Facades\Schema::hasColumn('agreement_payments', 'student_course_enrollment_id')) {
            $issues[] = 'العمود student_course_enrollment_id غير موجود — شغّل: php artisan migrate --force';
        }

        try {
            $column = DB::selectOne("SHOW COLUMNS FROM agreement_payments WHERE Field = 'type'");
            $typeDefinition = (string) ($column->Type ?? '');
            if ($typeDefinition !== '' && ! str_contains($typeDefinition, 'course_activation')) {
                DB::statement("ALTER TABLE agreement_payments MODIFY COLUMN type ENUM(
                    'course_completion', 'hourly_teaching', 'monthly_salary', 'bonus', 'other', 'course_activation'
                ) DEFAULT 'course_completion'");
                $this->info('تم تحديث ENUM لجدول agreement_payments وإضافة course_activation.');
            }
        } catch (\Throwable $e) {
            $issues[] = 'تعذر التحقق/تحديث ENUM لجدول agreement_payments: ' . $e->getMessage();
        }

        foreach ($issues as $issue) {
            $this->error($issue);
        }
    }
}
