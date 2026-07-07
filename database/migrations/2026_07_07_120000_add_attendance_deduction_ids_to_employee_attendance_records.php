<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employee_attendance_records')) {
            return;
        }

        Schema::table('employee_attendance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_attendance_records', 'late_deduction_id')) {
                $table->foreignId('late_deduction_id')->nullable()->after('metadata')
                    ->constrained('employee_salary_deductions')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_attendance_records', 'absence_deduction_id')) {
                $table->foreignId('absence_deduction_id')->nullable()->after('late_deduction_id')
                    ->constrained('employee_salary_deductions')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_attendance_records', 'incomplete_deduction_id')) {
                $table->foreignId('incomplete_deduction_id')->nullable()->after('absence_deduction_id')
                    ->constrained('employee_salary_deductions')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employee_attendance_records')) {
            return;
        }

        Schema::table('employee_attendance_records', function (Blueprint $table) {
            foreach (['incomplete_deduction_id', 'absence_deduction_id', 'late_deduction_id'] as $col) {
                if (Schema::hasColumn('employee_attendance_records', $col)) {
                    $table->dropConstrainedForeignId($col);
                }
            }
        });
    }
};
