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
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'advanced_course_id')) {
                    $table->unsignedBigInteger('advanced_course_id')->nullable()->after('course_id');
                }
            });
            
            // إضافة Foreign Key باستخدام SQL مباشر
            if (Schema::hasColumn('assignments', 'advanced_course_id')) {
                \DB::statement('ALTER TABLE assignments MODIFY COLUMN advanced_course_id BIGINT UNSIGNED NULL');
                try {
                    \DB::statement('ALTER TABLE assignments ADD CONSTRAINT assignments_advanced_course_id_foreign FOREIGN KEY (advanced_course_id) REFERENCES advanced_courses(id) ON DELETE CASCADE');
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
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (Schema::hasColumn('assignments', 'advanced_course_id')) {
                    $table->dropForeign(['advanced_course_id']);
                    $table->dropColumn('advanced_course_id');
                }
            });
        }
    }
};
