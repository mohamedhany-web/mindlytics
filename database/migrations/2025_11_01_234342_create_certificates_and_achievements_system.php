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
        // جدول الشهادات
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('certificate_number')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->nullable()->constrained('advanced_courses')->onDelete('set null');
                $table->string('course_name');
                $table->enum('certificate_type', ['completion', 'achievement', 'participation', 'certification'])->default('completion');
                $table->date('issue_date');
                $table->date('expiry_date')->nullable();
                $table->string('template')->nullable(); // قالب الشهادة
                $table->string('pdf_path')->nullable(); // مسار ملف PDF
                $table->string('verification_code')->unique(); // رمز التحقق
                $table->text('metadata')->nullable(); // بيانات إضافية
                $table->boolean('is_verified')->default(true);
                $table->boolean('is_public')->default(false); // قابلية النشر العام
                $table->timestamps();
                
                $table->index(['user_id', 'course_id']);
                $table->index('certificate_number');
                $table->index('verification_code');
            });
        }
        
        // جدول الإنجازات
        if (!Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->enum('type', ['course_completion', 'exam_score', 'streak', 'points', 'custom'])->default('custom');
                $table->json('requirements')->nullable(); // متطلبات الحصول على الإنجاز
                $table->integer('points_reward')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
        
        // جدول إنجازات المستخدمين
        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('achievement_id')->constrained('achievements')->onDelete('cascade');
                $table->foreignId('course_id')->nullable()->constrained('advanced_courses')->onDelete('set null');
                $table->dateTime('earned_at');
                $table->integer('progress')->default(100); // تقدم الإنجاز (0-100)
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->unique(['user_id', 'achievement_id', 'course_id'], 'unique_user_achievement');
                $table->index(['user_id', 'earned_at']);
            });
        }
        
        // جدول الشارات
        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
            $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->default('#3B82F6');
                $table->enum('type', ['skill', 'milestone', 'special', 'seasonal'])->default('skill');
                $table->json('requirements')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }
        
        // جدول شارات المستخدمين
        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('badge_id')->constrained('badges')->onDelete('cascade');
                $table->dateTime('earned_at');
                $table->boolean('is_displayed')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->unique(['user_id', 'badge_id']);
                $table->index(['user_id', 'is_displayed']);
            });
        }
        
        // جدول سجلات التقدم
        if (!Schema::hasTable('progress_tracks')) {
            Schema::create('progress_tracks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->nullable()->constrained('advanced_courses')->onDelete('cascade');
                $table->enum('track_type', ['course', 'lesson', 'exam', 'assignment', 'overall'])->default('course');
                $table->foreignId('item_id')->nullable(); // ID العنصر (درس، امتحان، إلخ)
                $table->integer('progress_percentage')->default(0);
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'failed'])->default('not_started');
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->integer('time_spent_minutes')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'course_id', 'track_type']);
                $table->index(['status', 'progress_percentage']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_tracks');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('certificates');
    }
};
