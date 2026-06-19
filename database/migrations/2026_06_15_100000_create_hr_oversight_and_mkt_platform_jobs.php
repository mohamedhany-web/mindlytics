<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('moderator_mkt_platform_jobs')) {
            Schema::create('moderator_mkt_platform_jobs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('platform_id');
                $table->foreign('platform_id', 'fk_mkt_pj_platform')
                    ->references('id')
                    ->on('moderator_mkt_platforms')
                    ->cascadeOnDelete();
                $table->foreignId('employee_job_id')
                    ->constrained('employee_jobs')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['platform_id', 'employee_job_id'], 'mkt_plat_job_unique');
            });
        }

        if (! Schema::hasTable('employee_daily_reports')) {
            Schema::create('employee_daily_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('report_date');
                $table->text('summary')->nullable();
                $table->text('tasks_done')->nullable();
                $table->text('tomorrow_plan')->nullable();
                $table->text('blockers')->nullable();
                $table->decimal('hours_worked', 4, 1)->nullable();
                $table->string('status', 20)->default('draft')->index();
                $table->timestamp('submitted_at')->nullable();
                $table->foreignId('auto_deduction_id')->nullable()
                    ->constrained('employee_salary_deductions')
                    ->nullOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'report_date'], 'emp_daily_report_user_date');
                $table->index(['report_date', 'status'], 'emp_daily_report_date_status');
            });
        }

        if (! Schema::hasTable('employee_salary_additions')) {
            Schema::create('employee_salary_additions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('agreement_id')->nullable()->constrained('employee_agreements')->nullOnDelete();
                $table->string('addition_number')->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('amount', 10, 2);
                $table->enum('type', ['bonus', 'overtime', 'allowance', 'incentive', 'other'])->default('other');
                $table->date('addition_date');
                $table->enum('status', ['pending', 'applied', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
                $table->index('addition_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_additions');
        Schema::dropIfExists('employee_daily_reports');
        Schema::dropIfExists('moderator_mkt_platform_jobs');
    }
};
