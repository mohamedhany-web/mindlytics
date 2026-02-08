<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            // تغيير student_id إلى user_id
            if (Schema::hasColumn('exam_attempts', 'student_id')) {
                $table->renameColumn('student_id', 'user_id');
            }
            
            // إضافة الأعمدة المفقودة
            if (!Schema::hasColumn('exam_attempts', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('status');
            }
            if (!Schema::hasColumn('exam_attempts', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('exam_attempts', 'tab_switches')) {
                $table->integer('tab_switches')->default(0)->after('time_spent');
            }
            if (!Schema::hasColumn('exam_attempts', 'suspicious_activities')) {
                $table->json('suspicious_activities')->nullable()->after('tab_switches');
            }
            if (!Schema::hasColumn('exam_attempts', 'percentage')) {
                $table->decimal('percentage', 5, 2)->nullable()->after('score');
            }
            if (!Schema::hasColumn('exam_attempts', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            // عكس التغييرات
            if (Schema::hasColumn('exam_attempts', 'user_id')) {
                $table->renameColumn('user_id', 'student_id');
            }
            
            $columnsToRemove = [];
            if (Schema::hasColumn('exam_attempts', 'ip_address')) $columnsToRemove[] = 'ip_address';
            if (Schema::hasColumn('exam_attempts', 'user_agent')) $columnsToRemove[] = 'user_agent';
            if (Schema::hasColumn('exam_attempts', 'tab_switches')) $columnsToRemove[] = 'tab_switches';
            if (Schema::hasColumn('exam_attempts', 'suspicious_activities')) $columnsToRemove[] = 'suspicious_activities';
            if (Schema::hasColumn('exam_attempts', 'percentage')) $columnsToRemove[] = 'percentage';
            if (Schema::hasColumn('exam_attempts', 'completed_at')) $columnsToRemove[] = 'completed_at';
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
