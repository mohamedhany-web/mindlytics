<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'presence_last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('presence_last_seen_at')->nullable()->after('remember_token');
                $table->string('presence_status', 24)->nullable()->after('presence_last_seen_at');
            });
        }

        if (! Schema::hasTable('employee_presence_daily')) {
            Schema::create('employee_presence_daily', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('work_date');
                $table->unsignedBigInteger('employee_attendance_record_id')->nullable();
                $table->timestamp('first_seen_at')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->unsignedInteger('heartbeat_count')->default(0);
                $table->unsignedInteger('online_seconds')->default(0);
                $table->unsignedInteger('away_seconds')->default(0);
                $table->unsignedInteger('offline_seconds')->default(0);
                $table->unsignedSmallInteger('violation_count')->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'work_date']);
                $table->foreign('employee_attendance_record_id', 'epd_attendance_fk')
                    ->references('id')->on('employee_attendance_records')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('employee_presence_violations')) {
            Schema::create('employee_presence_violations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('work_date');
                $table->unsignedBigInteger('employee_attendance_record_id')->nullable();
                $table->timestamp('started_at');
                $table->timestamp('ended_at')->nullable();
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->string('reason', 32)->default('no_heartbeat');
                $table->string('status', 16)->default('open');
                $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('acknowledged_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['work_date', 'status']);
                $table->index(['user_id', 'work_date']);
                $table->foreign('employee_attendance_record_id', 'epv_attendance_fk')
                    ->references('id')->on('employee_attendance_records')->nullOnDelete();
            });
        }

        if (Schema::hasTable('employee_attendance_records')
            && ! Schema::hasColumn('employee_attendance_records', 'presence_deduction_id')) {
            Schema::table('employee_attendance_records', function (Blueprint $table) {
                $table->unsignedBigInteger('presence_deduction_id')->nullable()->after('incomplete_deduction_id');
            });
            Schema::table('employee_attendance_records', function (Blueprint $table) {
                $table->foreign('presence_deduction_id', 'ear_presence_ded_fk')
                    ->references('id')->on('employee_salary_deductions')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employee_attendance_records')
            && Schema::hasColumn('employee_attendance_records', 'presence_deduction_id')) {
            Schema::table('employee_attendance_records', function (Blueprint $table) {
                $table->dropForeign('ear_presence_ded_fk');
                $table->dropColumn('presence_deduction_id');
            });
        }

        Schema::dropIfExists('employee_presence_violations');
        Schema::dropIfExists('employee_presence_daily');

        if (Schema::hasColumn('users', 'presence_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['presence_last_seen_at', 'presence_status']);
            });
        }
    }
};
