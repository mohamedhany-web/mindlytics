<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_daily_reports')) {
            Schema::create('sales_daily_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('report_date');
                $table->string('status', 24)->default('draft'); // draft | submitted
                $table->timestamp('submitted_at')->nullable();

                // تقرير النشاط اليومي
                $table->unsignedInteger('messages_replied')->nullable();
                $table->unsignedInteger('leads_qualified')->nullable();
                $table->unsignedInteger('bookings_from_leads')->nullable();
                $table->text('activity_notes')->nullable();

                // تقرير الإنتاجية
                $table->unsignedInteger('numbers_worked')->nullable();
                $table->unsignedInteger('followups_done')->nullable();
                $table->unsignedInteger('calls_made')->nullable();
                $table->unsignedInteger('meetings_held')->nullable();
                $table->unsignedInteger('calls_answered')->nullable();
                $table->text('productivity_notes')->nullable();

                $table->json('missing_fields')->nullable();
                $table->foreignId('auto_deduction_id')->nullable()->constrained('employee_salary_deductions')->nullOnDelete();

                $table->timestamps();

                $table->unique(['user_id', 'report_date']);
                $table->index(['report_date', 'status']);
            });
        }

        if (! Schema::hasTable('sales_daily_report_contacts')) {
            Schema::create('sales_daily_report_contacts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_daily_report_id')->constrained('sales_daily_reports')->cascadeOnDelete();
                $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
                $table->string('contact_name')->nullable();
                $table->string('contact_phone');
                $table->string('interaction_type', 16); // call | meeting
                $table->text('client_status');
                $table->text('client_problems');
                $table->timestamps();

                $table->index('sales_daily_report_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_daily_report_contacts');
        Schema::dropIfExists('sales_daily_reports');
    }
};
