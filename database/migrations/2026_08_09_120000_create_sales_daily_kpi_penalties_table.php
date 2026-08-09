<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_daily_kpi_penalties')) {
            return;
        }

        Schema::create('sales_daily_kpi_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->string('metric_key', 64);
            $table->decimal('actual', 10, 2)->default(0);
            $table->decimal('target', 10, 2)->default(0);
            $table->decimal('pct', 6, 2)->default(0);
            $table->foreignId('deduction_id')->nullable()->constrained('employee_salary_deductions')->nullOnDelete();
            $table->timestamp('waived_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'work_date', 'metric_key'], 'sales_daily_kpi_penalties_unique');
            $table->index(['work_date', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_daily_kpi_penalties');
    }
};
