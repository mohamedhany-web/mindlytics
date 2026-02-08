<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\StudentReport;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reports:send-monthly {--month=} {--dry-run} {--force}';

    /**
     * The console command description.
     */
    protected $description = 'إرسال التقارير الشهرية لأولياء الأمور عبر الواتساب';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?? now()->subMonth()->format('Y-m');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("بدء توليد التقارير الشهرية للشهر: {$month}");

        if ($dryRun) {
            $this->warn("تشغيل تجريبي - لن يتم إرسال رسائل فعلية");
        }

        // التحقق من وجود تقارير مرسلة مسبقاً
        $existingReports = StudentReport::forMonth($month)->sent()->count();
        
        if ($existingReports > 0 && !$force) {
            $this->error("تم إرسال {$existingReports} تقرير مسبقاً لهذا الشهر. استخدم --force لإعادة الإرسال");
            return 1;
        }

        // الحصول على جميع الطلاب النشطين مع أولياء أمورهم
        $students = User::students()
            ->whereHas('courseEnrollments')
            ->with(['parent'])
            ->get();

        if ($students->isEmpty()) {
            $this->error('لا توجد طلاب مسجلين في النظام');
            return 1;
        }

        $this->info("تم العثور على {$students->count()} طالب");

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        $generated = 0;
        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($students as $student) {
            try {
                // تحقق من وجود تقرير مسبق
                $existingReport = StudentReport::where('student_id', $student->id)
                    ->where('report_month', $month)
                    ->first();

                if ($existingReport && !$force) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // توليد بيانات التقرير
                $reportData = $this->whatsappService->generateStudentReportData($student);

                if (!$dryRun) {
                    // إنشاء أو تحديث التقرير
                    $report = StudentReport::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'report_month' => $month,
                            'report_type' => 'monthly'
                        ],
                        [
                            'parent_id' => $student->parent_id,
                            'report_data' => $reportData,
                            'sent_via' => 'whatsapp',
                            'status' => 'pending',
                            'generated_by' => 1, // System user
                        ]
                    );

                    $generated++;

                    // إرسال لولي الأمر إذا كان متاحاً
                    if ($student->parent && $student->parent->phone) {
                        $result = $this->whatsappService->sendMonthlyReport(
                            $student->parent, 
                            $student, 
                            $reportData
                        );

                        if ($result['success']) {
                            $report->update([
                                'status' => 'sent',
                                'sent_at' => now()
                            ]);
                            $sent++;
                        } else {
                            $report->update([
                                'status' => 'failed',
                                'error_message' => $result['error'] ?? 'خطأ غير معروف'
                            ]);
                            $failed++;
                        }
                    } else {
                        // إرسال للطالب إذا لم يكن هناك ولي أمر
                        $result = $this->whatsappService->sendStudentProgress($student);
                        
                        if ($result['success']) {
                            $report->update([
                                'status' => 'sent',
                                'sent_at' => now()
                            ]);
                            $sent++;
                        } else {
                            $report->update([
                                'status' => 'failed',
                                'error_message' => $result['error'] ?? 'خطأ غير معروف'
                            ]);
                            $failed++;
                        }
                    }
                } else {
                    // في حالة التشغيل التجريبي
                    $generated++;
                    $this->line("\n  - سيتم إرسال تقرير للطالب: {$student->name}");
                    if ($student->parent) {
                        $this->line("    ولولي الأمر: {$student->parent->name} ({$student->parent->phone})");
                    }
                }

            } catch (\Exception $e) {
                $failed++;
                Log::error('Error in monthly report generation', [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'error' => $e->getMessage()
                ]);
                
                $this->error("\nخطأ في توليد تقرير للطالب {$student->name}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->info("✅ تم الانتهاء من توليد التقارير:");
        $this->table(
            ['المؤشر', 'العدد'],
            [
                ['التقارير المولدة', $generated],
                ['التقارير المرسلة', $sent],
                ['التقارير الفاشلة', $failed],
                ['التقارير المتجاهلة', $skipped],
            ]
        );

        if ($dryRun) {
            $this->warn("هذا كان تشغيل تجريبي - لم يتم إرسال رسائل فعلية");
            $this->info("لإرسال التقارير فعلياً، استخدم الأمر بدون --dry-run");
        }

        return 0;
    }
}