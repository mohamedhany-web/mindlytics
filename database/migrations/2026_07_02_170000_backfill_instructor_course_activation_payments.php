<?php

use App\Services\InstructorCoursePercentageService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE agreement_payments MODIFY COLUMN type ENUM(
                'course_completion', 'hourly_teaching', 'monthly_salary', 'bonus', 'other', 'course_activation'
            ) DEFAULT 'course_completion'");
        } catch (\Throwable $e) {
            Log::warning('backfill_instructor_course_activation_payments: enum update skipped', [
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $created = InstructorCoursePercentageService::syncAllMissingPayments();
            Log::info('backfill_instructor_course_activation_payments completed', [
                'created' => $created,
            ]);
        } catch (\Throwable $e) {
            Log::error('backfill_instructor_course_activation_payments failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function down(): void
    {
        // لا حذف تلقائي لمدفوعات تم إنشاؤها من المزامنة.
    }
};
