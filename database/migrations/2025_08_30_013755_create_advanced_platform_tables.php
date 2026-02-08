<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول بنك الأسئلة
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول الأسئلة
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay', 'fill_blank']);
            $table->json('options')->nullable(); // للخيارات المتعددة
            $table->text('correct_answer');
            $table->text('explanation')->nullable();
            $table->integer('points')->default(1);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول الامتحانات
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('duration_minutes');
            $table->integer('total_marks');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('attempts_allowed')->default(1);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('show_results')->default(true);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('settings')->nullable(); // إعدادات إضافية
            $table->timestamps();
        });

        // جدول أسئلة الامتحان
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('order');
            $table->integer('marks');
            $table->timestamps();
        });

        // جدول محاولات الامتحان
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->integer('score')->nullable();
            $table->json('answers'); // إجابات الطالب
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted'])->default('in_progress');
            $table->integer('time_spent')->nullable(); // بالثواني
            $table->timestamps();
        });

        // جدول مراقبة النشاطات
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });

        // جدول حماية الفيديوهات
        Schema::create('video_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token')->unique();
            $table->dateTime('expires_at');
            $table->string('ip_address')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamps();
        });

        // جدول تتبع مشاهدة الفيديوهات
        Schema::create('video_watches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('watch_time'); // بالثواني
            $table->integer('video_duration'); // بالثواني
            $table->decimal('progress_percentage', 5, 2);
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->unique(['lesson_id', 'user_id']);
        });

        // جدول الصلاحيات
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        // جدول الأدوار
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // جدول صلاحيات الأدوار
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // جدول أدوار المستخدمين
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });

        // جدول إعدادات المنصة
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->string('group')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // جدول الإشعارات المتقدمة
        Schema::create('advanced_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info'); // info, success, warning, error
            $table->json('recipients'); // user_ids أو roles
            $table->boolean('is_broadcast')->default(false);
            $table->dateTime('scheduled_at')->nullable();
            $table->enum('status', ['draft', 'sent', 'scheduled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // جدول قراءة الإشعارات
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('advanced_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
        });

        // جدول التقييمات والمراجعات
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('review')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });

        // جدول الشهادات
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->dateTime('issued_at');
            $table->string('template')->nullable();
            $table->json('data')->nullable(); // معلومات إضافية للشهادة
            $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('advanced_notifications');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('video_watches');
        Schema::dropIfExists('video_tokens');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_banks');
    }
};