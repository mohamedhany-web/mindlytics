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
        // تحديث جدول الامتحانات
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                if (!Schema::hasColumn('exams', 'prevent_tab_switch')) {
                    $table->boolean('prevent_tab_switch')->default(true)->after('shuffle_questions');
                }
                if (!Schema::hasColumn('exams', 'prevent_copy_paste')) {
                    $table->boolean('prevent_copy_paste')->default(true)->after('prevent_tab_switch');
                }
                if (!Schema::hasColumn('exams', 'prevent_right_click')) {
                    $table->boolean('prevent_right_click')->default(true)->after('prevent_copy_paste');
                }
                if (!Schema::hasColumn('exams', 'require_fullscreen')) {
                    $table->boolean('require_fullscreen')->default(false)->after('prevent_right_click');
                }
                if (!Schema::hasColumn('exams', 'monitor_activity')) {
                    $table->boolean('monitor_activity')->default(true)->after('require_fullscreen');
                }
            });
        }

        // جدول سجلات منع الغش
        if (!Schema::hasTable('exam_anti_cheat_logs')) {
            Schema::create('exam_anti_cheat_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->enum('violation_type', ['tab_switch', 'copy_paste', 'right_click', 'fullscreen_exit', 'window_blur', 'other'])->default('other');
                $table->text('description')->nullable();
                $table->json('metadata')->nullable(); // معلومات إضافية
                $table->timestamp('violation_at');
                $table->timestamps();
                
                $table->index(['exam_id', 'attempt_id']);
                $table->index(['student_id', 'violation_type']);
            });
        }

        // جدول سجلات تبديل التبويبات
        if (!Schema::hasTable('exam_tab_switch_logs')) {
            Schema::create('exam_tab_switch_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->integer('switch_count')->default(0);
                $table->timestamp('first_switch_at')->nullable();
                $table->timestamp('last_switch_at')->nullable();
                $table->json('switch_details')->nullable(); // تفاصيل كل تبديل
                $table->timestamps();
                
                $table->unique(['exam_id', 'attempt_id']);
                $table->index(['student_id', 'switch_count']);
            });
        }

        // جدول سجلات النشاطات أثناء الامتحان
        if (!Schema::hasTable('exam_activity_logs')) {
            Schema::create('exam_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->enum('activity_type', ['focus', 'blur', 'visibility_change', 'mouse_move', 'keyboard', 'copy', 'paste', 'cut'])->default('focus');
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('activity_at');
                $table->timestamps();
                
                $table->index(['exam_id', 'attempt_id', 'activity_type']);
                $table->index(['student_id', 'activity_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_activity_logs');
        Schema::dropIfExists('exam_tab_switch_logs');
        Schema::dropIfExists('exam_anti_cheat_logs');
        
        if (Schema::hasTable('exams')) {
            Schema::table('exams', function (Blueprint $table) {
                $columns = [
                    'prevent_tab_switch', 'prevent_copy_paste', 'prevent_right_click',
                    'require_fullscreen', 'monitor_activity'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('exams', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
