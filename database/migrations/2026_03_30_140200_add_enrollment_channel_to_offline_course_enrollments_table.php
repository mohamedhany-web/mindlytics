<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_enrollments', function (Blueprint $table) {
            $table->string('enrollment_channel', 16)->default('offline')->after('group_id');
            $table->dropUnique('unique_student_offline_course');
            $table->unique(['user_id', 'offline_course_id', 'enrollment_channel'], 'uniq_student_course_channel');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_enrollments', function (Blueprint $table) {
            $table->dropUnique('uniq_student_course_channel');
            $table->dropColumn('enrollment_channel');
            $table->unique(['user_id', 'offline_course_id'], 'unique_student_offline_course');
        });
    }
};

