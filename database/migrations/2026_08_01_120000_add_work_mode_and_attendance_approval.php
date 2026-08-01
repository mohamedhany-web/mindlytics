<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'work_mode')) {
                $table->string('work_mode', 20)->default('online')->after('work_schedule_id');
            }
            if (! Schema::hasColumn('users', 'offline_attendance_type')) {
                $table->string('offline_attendance_type', 30)->nullable()->after('work_mode');
            }
            if (! Schema::hasColumn('users', 'onsite_days')) {
                $table->json('onsite_days')->nullable()->after('offline_attendance_type');
            }
        });

        Schema::table('employee_attendance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_attendance_records', 'attendance_approval_status')) {
                $table->string('attendance_approval_status', 30)->default('not_required')->after('is_late');
            }
            if (! Schema::hasColumn('employee_attendance_records', 'attendance_requested_at')) {
                $table->dateTime('attendance_requested_at')->nullable()->after('attendance_approval_status');
            }
            if (! Schema::hasColumn('employee_attendance_records', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('attendance_requested_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('employee_attendance_records', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('approved_by');
            }
            if (! Schema::hasColumn('employee_attendance_records', 'manager_lateness_decision')) {
                $table->string('manager_lateness_decision', 30)->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('employee_attendance_records', 'late_penalty_waived')) {
                $table->boolean('late_penalty_waived')->default(false)->after('manager_lateness_decision');
            }
            if (! Schema::hasColumn('employee_attendance_records', 'approval_rejection_reason')) {
                $table->string('approval_rejection_reason', 500)->nullable()->after('late_penalty_waived');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendance_records', function (Blueprint $table) {
            if (Schema::hasColumn('employee_attendance_records', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }
            foreach ([
                'attendance_approval_status',
                'attendance_requested_at',
                'approved_at',
                'manager_lateness_decision',
                'late_penalty_waived',
                'approval_rejection_reason',
            ] as $col) {
                if (Schema::hasColumn('employee_attendance_records', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['work_mode', 'offline_attendance_type', 'onsite_days'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
