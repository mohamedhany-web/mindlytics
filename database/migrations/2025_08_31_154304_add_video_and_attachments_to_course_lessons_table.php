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
        // التحقق من وجود الجدول قبل التعديل
        if (Schema::hasTable('course_lessons')) {
            Schema::table('course_lessons', function (Blueprint $table) {
                if (!Schema::hasColumn('course_lessons', 'video_url')) {
                    $table->string('video_url')->nullable()->after('content');
                }
                if (!Schema::hasColumn('course_lessons', 'attachments')) {
                    $table->json('attachments')->nullable()->after('video_url');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_lessons')) {
            Schema::table('course_lessons', function (Blueprint $table) {
                if (Schema::hasColumn('course_lessons', 'video_url')) {
                    $table->dropColumn('video_url');
                }
                if (Schema::hasColumn('course_lessons', 'attachments')) {
                    $table->dropColumn('attachments');
                }
            });
        }
    }
};
