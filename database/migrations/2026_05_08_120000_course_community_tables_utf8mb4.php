<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * يضمن تخزين العربية والرموز التعبيرية في منشورات/تعليقات المجتمع (MySQL).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach (['course_community_comments', 'course_community_posts'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            try {
                DB::statement(
                    'ALTER TABLE `'.$table.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
            } catch (\Throwable) {
                // تجاهل إن كان الجدول بالفعل utf8mb4 أو بيئة غير متوقعة
            }
        }
    }

    public function down(): void
    {
        // لا نعيد utf8 القديم لتجنب فقدان البيانات
    }
};
