<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('student_course_enrollments', 'curriculum_completed_at')) {
                $table->timestamp('curriculum_completed_at')->nullable()->after('progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('student_course_enrollments', 'curriculum_completed_at')) {
                $table->dropColumn('curriculum_completed_at');
            }
        });
    }
};
