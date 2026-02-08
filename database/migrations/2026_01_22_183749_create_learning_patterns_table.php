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
        if (!Schema::hasTable('learning_patterns')) {
            Schema::create('learning_patterns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('advanced_course_id')->index();
                $table->unsignedBigInteger('instructor_id')->index();
                $table->string('type'); // code_challenge, quiz, playground, project, debugging, snippet, peer_review, gamification, etc.
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                
                // البيانات التفاعلية (JSON)
                $table->json('pattern_data')->nullable(); // يحتوي على البيانات الخاصة بكل نوع
                
                // إعدادات
                $table->integer('points')->default(0); // نقاط عند الإكمال
                $table->integer('time_limit_minutes')->nullable(); // حد زمني (اختياري)
                $table->integer('difficulty_level')->default(1); // 1-5
                $table->boolean('is_required')->default(false); // إلزامي أم اختياري
                $table->boolean('allow_multiple_attempts')->default(true);
                $table->integer('max_attempts')->nullable();
                
                // ترتيب وعرض
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                
                // إحصائيات
                $table->integer('total_attempts')->default(0);
                $table->integer('total_completions')->default(0);
                
                $table->timestamps();
                
                $table->foreign('advanced_course_id', 'lp_advanced_course_fk')
                    ->references('id')
                    ->on('advanced_courses')
                    ->onDelete('cascade');
                $table->foreign('instructor_id', 'lp_instructor_fk')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->index(['advanced_course_id', 'type', 'is_active'], 'lp_course_type_active_idx');
            });
        }
        
        // جدول لتتبع محاولات الطلاب
        if (!Schema::hasTable('learning_pattern_attempts')) {
            Schema::create('learning_pattern_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('learning_pattern_id');
                $table->unsignedBigInteger('user_id');
                $table->enum('status', ['started', 'in_progress', 'completed', 'failed', 'abandoned'])->default('started');
                $table->json('attempt_data')->nullable(); // بيانات المحاولة (الإجابات، الكود، etc.)
                $table->integer('score')->nullable();
                $table->integer('points_earned')->default(0);
                $table->timestamp('started_at');
                $table->timestamp('completed_at')->nullable();
                $table->integer('time_spent_seconds')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamps();
                
                $table->foreign('learning_pattern_id', 'lpa_pattern_fk')
                    ->references('id')
                    ->on('learning_patterns')
                    ->onDelete('cascade');
                $table->foreign('user_id', 'lpa_user_fk')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->index(['learning_pattern_id', 'user_id', 'status'], 'lpa_pattern_user_status_idx');
                $table->index(['user_id', 'status'], 'lpa_user_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_pattern_attempts');
        Schema::dropIfExists('learning_patterns');
    }
};
