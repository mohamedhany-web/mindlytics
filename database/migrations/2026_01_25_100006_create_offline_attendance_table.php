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
        Schema::create('offline_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('offline_course_groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('attendance_time')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade')->comment('من قام بتسجيل الحضور');
            $table->timestamps();
            
            $table->unique(['student_id', 'offline_course_id', 'attendance_date'], 'unique_attendance');
            $table->index(['offline_course_id', 'attendance_date']);
            $table->index(['group_id', 'attendance_date']);
            $table->index('attendance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_attendance');
    }
};
