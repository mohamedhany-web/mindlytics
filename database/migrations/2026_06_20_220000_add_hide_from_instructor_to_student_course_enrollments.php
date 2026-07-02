<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('student_course_enrollments', 'hide_from_instructor')) {
                $table->boolean('hide_from_instructor')->default(false)->after('enrollment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_course_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('student_course_enrollments', 'hide_from_instructor')) {
                $table->dropColumn('hide_from_instructor');
            }
        });
    }
};
