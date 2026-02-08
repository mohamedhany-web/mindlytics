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
        // تحديث جدول الواجبات إذا كان موجوداً
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'github_link_support')) {
                    $table->boolean('github_link_support')->default(true)->after('allow_late_submission');
                }
                if (!Schema::hasColumn('assignments', 'code_testing_api')) {
                    $table->string('code_testing_api')->nullable()->after('github_link_support'); // Judge0, etc
                }
                if (!Schema::hasColumn('assignments', 'code_testing_config')) {
                    $table->json('code_testing_config')->nullable()->after('code_testing_api');
                }
            });
        }

        // تحديث جدول تسليم الواجبات
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('assignment_submissions', 'id')) {
                    $table->id()->first();
                }
                if (!Schema::hasColumn('assignment_submissions', 'github_link')) {
                    $table->string('github_link')->nullable()->after('attachments');
                }
                if (!Schema::hasColumn('assignment_submissions', 'version')) {
                    $table->integer('version')->default(1)->after('status');
                }
                if (!Schema::hasColumn('assignment_submissions', 'voice_feedback_path')) {
                    $table->string('voice_feedback_path')->nullable()->after('feedback');
                }
                if (!Schema::hasColumn('assignment_submissions', 'feedback_attachments')) {
                    $table->json('feedback_attachments')->nullable()->after('voice_feedback_path');
                }
                if (!Schema::hasColumn('assignment_submissions', 'code_test_results')) {
                    $table->json('code_test_results')->nullable()->after('feedback_attachments');
                }
            });
        }

        // إنشاء جدول نسخ تسليم الواجبات (للاحتفاظ بجميع الإصدارات)
        if (!Schema::hasTable('assignment_submission_versions')) {
            Schema::create('assignment_submission_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('assignment_submissions')->onDelete('cascade');
                $table->integer('version');
                $table->text('content')->nullable();
                $table->json('attachments')->nullable();
                $table->string('github_link')->nullable();
                $table->timestamp('submitted_at');
                $table->timestamps();
                
                $table->unique(['submission_id', 'version']);
                $table->index(['submission_id', 'submitted_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_versions');
        
        if (Schema::hasTable('assignment_submissions')) {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $columns = ['github_link', 'version', 'voice_feedback_path', 'feedback_attachments', 'code_test_results'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('assignment_submissions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                $columns = ['github_link_support', 'code_testing_api', 'code_testing_config'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('assignments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
