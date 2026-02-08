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
        // جدول المحاضرات
        if (!Schema::hasTable('lectures')) {
            Schema::create('lectures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('teams_registration_link')->nullable(); // رابط تسجيل Teams
                $table->string('teams_meeting_link')->nullable(); // رابط اجتماع Teams
                $table->string('recording_url')->nullable(); // رابط تسجيل المحاضرة
                $table->string('recording_file_path')->nullable(); // مسار ملف التسجيل المحلي
                $table->dateTime('scheduled_at'); // موعد المحاضرة
                $table->integer('duration_minutes')->default(60); // مدة المحاضرة بالدقائق
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
                $table->text('notes')->nullable();
                $table->boolean('has_attendance_tracking')->default(true); // تتبع الحضور
                $table->boolean('has_assignment')->default(false); // هل يوجد واجب
                $table->boolean('has_evaluation')->default(false); // هل يوجد تقييم للمحاضر
                $table->timestamps();
                
                $table->index(['course_id', 'scheduled_at']);
                $table->index(['instructor_id', 'status']);
            });
        }

        // جدول واجبات المحاضرات
        if (!Schema::hasTable('lecture_assignments')) {
            Schema::create('lecture_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lecture_id')->constrained('lectures')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->integer('max_score')->default(100);
                $table->boolean('allow_late_submission')->default(false);
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->timestamps();
            });
        }

        // جدول تسليم واجبات المحاضرات
        if (!Schema::hasTable('lecture_assignment_submissions')) {
            Schema::create('lecture_assignment_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('lecture_assignments')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->text('content')->nullable();
                $table->json('attachments')->nullable(); // ملفات مرفقة
                $table->string('github_link')->nullable(); // رابط GitHub
                $table->timestamp('submitted_at')->useCurrent();
                $table->integer('score')->nullable();
                $table->text('feedback')->nullable();
                $table->string('voice_feedback_path')->nullable(); // مسار ملاحظة صوتية
                $table->json('feedback_attachments')->nullable(); // ملفات ملاحظات
                $table->timestamp('graded_at')->nullable();
                $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', ['submitted', 'graded', 'returned'])->default('submitted');
                $table->integer('version')->default(1); // رقم الإصدار
                $table->timestamps();
                
                $table->unique(['assignment_id', 'student_id', 'version'], 'unique_submission_version');
            });
        }

        // جدول تقييمات المحاضرات
        if (!Schema::hasTable('lecture_evaluations')) {
            Schema::create('lecture_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lecture_id')->constrained('lectures')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->integer('rating')->default(5); // من 1 إلى 5
                $table->text('feedback')->nullable();
                $table->json('evaluation_data')->nullable(); // بيانات إضافية للتقييم
                $table->timestamps();
                
                $table->unique(['lecture_id', 'student_id']);
            });
        }

        // جدول المجموعات
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('leader_id')->nullable()->constrained('users')->onDelete('set null'); // قائد المجموعة
                $table->integer('max_members')->default(10);
                $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
                $table->timestamps();
                
                $table->index(['course_id', 'status']);
            });
        }

        // جدول أعضاء المجموعات
        if (!Schema::hasTable('group_members')) {
            Schema::create('group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('role', ['leader', 'member'])->default('member');
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamps();
                
                $table->unique(['group_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('lecture_evaluations');
        Schema::dropIfExists('lecture_assignment_submissions');
        Schema::dropIfExists('lecture_assignments');
        Schema::dropIfExists('lectures');
    }
};
