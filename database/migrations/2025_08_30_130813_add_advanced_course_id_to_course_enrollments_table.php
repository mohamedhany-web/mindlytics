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
        // التحقق من وجود الجداول المطلوبة
        if (Schema::hasTable('course_enrollments') && Schema::hasTable('advanced_courses')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('course_enrollments', 'advanced_course_id')) {
                    $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['advanced_course_id']);
            $table->dropColumn('advanced_course_id');
        });
    }
};