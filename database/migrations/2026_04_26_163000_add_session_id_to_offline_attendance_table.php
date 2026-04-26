<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // قد يكون العمود أُضيف جزئياً إذا فشلت migration سابقاً
        if (!Schema::hasColumn('offline_attendance', 'offline_group_session_id')) {
            Schema::table('offline_attendance', function (Blueprint $table) {
                $table->foreignId('offline_group_session_id')
                    ->nullable()
                    ->after('group_id')
                    ->constrained('offline_group_sessions')
                    ->nullOnDelete();
            });
        }

        // إعادة ضبط التفرد ليكون مرتبطًا بالجلسة (المحاضرة/اليوم) بدل "اليوم فقط"
        Schema::table('offline_attendance', function (Blueprint $table) {
            // تأمين index مستقل للمفاتيح الخارجية قبل حذف unique المركّب
            $table->index('student_id', 'offline_attendance_student_id_idx');
            $table->index('offline_course_id', 'offline_attendance_offline_course_id_idx');
        });

        try {
            DB::statement('ALTER TABLE `offline_attendance` DROP INDEX `unique_attendance`');
        } catch (\Throwable) {
            // ignore (قد يكون محذوفاً أو فشل سابقاً)
        }

        Schema::table('offline_attendance', function (Blueprint $table) {
            $table->unique(['student_id', 'offline_course_id', 'offline_group_session_id'], 'unique_attendance_session');
            $table->index(['offline_group_session_id', 'attendance_date'], 'attendance_session_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('offline_attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_session_date_idx');
            $table->dropUnique('unique_attendance_session');
            $table->unique(['student_id', 'offline_course_id', 'attendance_date'], 'unique_attendance');
            $table->dropConstrainedForeignId('offline_group_session_id');
            $table->dropIndex('offline_attendance_student_id_idx');
            $table->dropIndex('offline_attendance_offline_course_id_idx');
        });
    }
};

