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
        // جدول سجلات الحضور
        if (!Schema::hasTable('attendance_records')) {
            Schema::create('attendance_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lecture_id')->constrained('lectures')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->dateTime('joined_at')->nullable(); // وقت الانضمام
                $table->dateTime('left_at')->nullable(); // وقت المغادرة
                $table->integer('attendance_minutes')->default(0); // عدد دقائق الحضور
                $table->integer('total_minutes')->default(0); // إجمالي دقائق المحاضرة
                $table->decimal('attendance_percentage', 5, 2)->default(0); // نسبة الحضور
                $table->enum('status', ['present', 'late', 'absent', 'partial'])->default('absent');
                $table->string('source')->default('manual'); // manual, teams_auto, teams_file
                $table->json('teams_data')->nullable(); // بيانات من Teams
                $table->string('teams_file_path')->nullable(); // مسار ملف Teams
                $table->timestamps();
                
                $table->unique(['lecture_id', 'student_id']);
                $table->index(['student_id', 'status']);
                $table->index(['lecture_id', 'status']);
            });
        }

        // جدول ملفات حضور Teams
        if (!Schema::hasTable('teams_attendance_files')) {
            Schema::create('teams_attendance_files', function (Blueprint $table) {
            $table->id();
                $table->foreignId('lecture_id')->constrained('lectures')->onDelete('cascade');
                $table->string('file_name');
                $table->string('file_path');
                $table->string('file_type')->default('csv'); // csv, xlsx, etc
                $table->integer('total_records')->default(0);
                $table->integer('processed_records')->default(0);
                $table->enum('status', ['uploaded', 'processing', 'completed', 'failed'])->default('uploaded');
                $table->text('error_message')->nullable();
                $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
                
                $table->index(['lecture_id', 'status']);
            });
        }

        // جدول إحصائيات الحضور
        if (!Schema::hasTable('attendance_statistics')) {
            Schema::create('attendance_statistics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->integer('total_lectures')->default(0);
                $table->integer('attended_lectures')->default(0);
                $table->integer('late_lectures')->default(0);
                $table->integer('absent_lectures')->default(0);
                $table->decimal('attendance_rate', 5, 2)->default(0); // نسبة الحضور الإجمالية
                $table->integer('total_hours')->default(0); // إجمالي ساعات الحضور
                $table->date('period_start')->nullable(); // بداية الفترة
                $table->date('period_end')->nullable(); // نهاية الفترة
                $table->timestamps();
                
                $table->unique(['student_id', 'course_id']);
                $table->index(['course_id', 'attendance_rate']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_statistics');
        Schema::dropIfExists('teams_attendance_files');
        Schema::dropIfExists('attendance_records');
    }
};
