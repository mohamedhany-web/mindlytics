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
        Schema::table('exams', function (Blueprint $table) {
            // إضافة الأعمدة المفقودة
            if (!Schema::hasColumn('exams', 'start_time')) {
                $table->datetime('start_time')->nullable();
            }
            if (!Schema::hasColumn('exams', 'end_time')) {
                $table->datetime('end_time')->nullable();
            }
            if (!Schema::hasColumn('exams', 'show_results_immediately')) {
                $table->boolean('show_results_immediately')->default(false);
            }
            if (!Schema::hasColumn('exams', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(false);
            }
            if (!Schema::hasColumn('exams', 'show_explanations')) {
                $table->boolean('show_explanations')->default(false);
            }
            if (!Schema::hasColumn('exams', 'allow_review')) {
                $table->boolean('allow_review')->default(false);
            }
            if (!Schema::hasColumn('exams', 'randomize_questions')) {
                $table->boolean('randomize_questions')->default(false);
            }
            if (!Schema::hasColumn('exams', 'randomize_options')) {
                $table->boolean('randomize_options')->default(false);
            }
            if (!Schema::hasColumn('exams', 'require_camera')) {
                $table->boolean('require_camera')->default(false);
            }
            if (!Schema::hasColumn('exams', 'require_microphone')) {
                $table->boolean('require_microphone')->default(false);
            }
            if (!Schema::hasColumn('exams', 'prevent_tab_switch')) {
                $table->boolean('prevent_tab_switch')->default(false);
            }
            if (!Schema::hasColumn('exams', 'auto_submit')) {
                $table->boolean('auto_submit')->default(false);
            }
            if (!Schema::hasColumn('exams', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            if (!Schema::hasColumn('exams', 'instructions')) {
                $table->text('instructions')->nullable();
            }
            if (!Schema::hasColumn('exams', 'settings')) {
                $table->json('settings')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $columnsToRemove = [];
            
            if (Schema::hasColumn('exams', 'start_time')) $columnsToRemove[] = 'start_time';
            if (Schema::hasColumn('exams', 'end_time')) $columnsToRemove[] = 'end_time';
            if (Schema::hasColumn('exams', 'show_results_immediately')) $columnsToRemove[] = 'show_results_immediately';
            if (Schema::hasColumn('exams', 'show_correct_answers')) $columnsToRemove[] = 'show_correct_answers';
            if (Schema::hasColumn('exams', 'show_explanations')) $columnsToRemove[] = 'show_explanations';
            if (Schema::hasColumn('exams', 'allow_review')) $columnsToRemove[] = 'allow_review';
            if (Schema::hasColumn('exams', 'randomize_questions')) $columnsToRemove[] = 'randomize_questions';
            if (Schema::hasColumn('exams', 'randomize_options')) $columnsToRemove[] = 'randomize_options';
            if (Schema::hasColumn('exams', 'require_camera')) $columnsToRemove[] = 'require_camera';
            if (Schema::hasColumn('exams', 'require_microphone')) $columnsToRemove[] = 'require_microphone';
            if (Schema::hasColumn('exams', 'prevent_tab_switch')) $columnsToRemove[] = 'prevent_tab_switch';
            if (Schema::hasColumn('exams', 'auto_submit')) $columnsToRemove[] = 'auto_submit';
            if (Schema::hasColumn('exams', 'is_published')) $columnsToRemove[] = 'is_published';
            if (Schema::hasColumn('exams', 'instructions')) $columnsToRemove[] = 'instructions';
            if (Schema::hasColumn('exams', 'settings')) $columnsToRemove[] = 'settings';
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
