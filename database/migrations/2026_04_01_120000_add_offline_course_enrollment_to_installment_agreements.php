<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->dropForeign(['student_course_enrollment_id']);
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->foreignId('student_course_enrollment_id')->nullable()->change();
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->foreign('student_course_enrollment_id')
                ->references('id')
                ->on('student_course_enrollments')
                ->cascadeOnDelete();
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->foreignId('offline_course_enrollment_id')
                ->nullable()
                ->after('student_course_enrollment_id')
                ->constrained('offline_course_enrollments')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->dropForeign(['offline_course_enrollment_id']);
            $table->dropColumn('offline_course_enrollment_id');
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->dropForeign(['student_course_enrollment_id']);
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->foreignId('student_course_enrollment_id')->nullable(false)->change();
        });

        Schema::table('installment_agreements', function (Blueprint $table) {
            $table->foreign('student_course_enrollment_id')
                ->references('id')
                ->on('student_course_enrollments')
                ->cascadeOnDelete();
        });
    }
};
