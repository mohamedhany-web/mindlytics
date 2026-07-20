<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_daily_reports') && ! Schema::hasColumn('sales_daily_reports', 'penalty_waived_at')) {
            Schema::table('sales_daily_reports', function (Blueprint $table) {
                $table->timestamp('penalty_waived_at')->nullable()->after('auto_deduction_id');
            });
        }

        if (Schema::hasTable('employee_daily_reports') && ! Schema::hasColumn('employee_daily_reports', 'penalty_waived_at')) {
            Schema::table('employee_daily_reports', function (Blueprint $table) {
                $table->timestamp('penalty_waived_at')->nullable()->after('auto_deduction_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_daily_reports') && Schema::hasColumn('sales_daily_reports', 'penalty_waived_at')) {
            Schema::table('sales_daily_reports', function (Blueprint $table) {
                $table->dropColumn('penalty_waived_at');
            });
        }

        if (Schema::hasTable('employee_daily_reports') && Schema::hasColumn('employee_daily_reports', 'penalty_waived_at')) {
            Schema::table('employee_daily_reports', function (Blueprint $table) {
                $table->dropColumn('penalty_waived_at');
            });
        }
    }
};
