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
        if (!Schema::hasTable('lesson_progress')) {
            Schema::create('lesson_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_lesson_id')->constrained('course_lessons')->onDelete('cascade');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->integer('watch_time')->default(0)->comment('وقت المشاهدة بالثواني');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // فهرس فريد لمنع التكرار
                $table->unique(['user_id', 'course_lesson_id'], 'user_lesson_unique');
                $table->index(['user_id', 'is_completed']);
                $table->index(['course_lesson_id', 'is_completed']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
