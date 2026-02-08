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
        if (Schema::hasTable('lectures')) {
            Schema::table('lectures', function (Blueprint $table) {
                if (!Schema::hasColumn('lectures', 'course_lesson_id')) {
                    $table->unsignedBigInteger('course_lesson_id')->nullable()->after('course_id');
                }
            });
            
            // إضافة Foreign Key باستخدام SQL مباشر
            if (Schema::hasColumn('lectures', 'course_lesson_id')) {
                \DB::statement('ALTER TABLE lectures MODIFY COLUMN course_lesson_id BIGINT UNSIGNED NULL');
                try {
                    \DB::statement('ALTER TABLE lectures ADD CONSTRAINT lectures_course_lesson_id_foreign FOREIGN KEY (course_lesson_id) REFERENCES course_lessons(id) ON DELETE SET NULL');
                } catch (\Exception $e) {
                    // Foreign key موجود بالفعل
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lectures')) {
            Schema::table('lectures', function (Blueprint $table) {
                if (Schema::hasColumn('lectures', 'course_lesson_id')) {
                    try {
                        $table->dropForeign(['course_lesson_id']);
                    } catch (\Exception $e) {
                        // Foreign key غير موجود
                    }
                    $table->dropColumn('course_lesson_id');
                }
            });
        }
    }
};
