<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * السماح بربط الامتحانات بكورس أوفلاين (نفس نظام الامتحانات)
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'offline_course_id')) {
                $table->foreignId('offline_course_id')->nullable()->after('advanced_course_id')->constrained('offline_courses')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'offline_course_id')) {
                $table->dropForeign(['offline_course_id']);
            }
        });
    }
};
