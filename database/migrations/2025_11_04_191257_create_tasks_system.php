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
        // جدول المهام الشخصية
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->dateTime('due_date')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->foreignId('related_course_id')->nullable()->constrained('advanced_courses')->onDelete('set null');
                $table->foreignId('related_lecture_id')->nullable()->constrained('lectures')->onDelete('set null');
                $table->foreignId('related_assignment_id')->nullable()->constrained('assignments')->onDelete('set null');
                $table->string('related_type')->nullable(); // course, lecture, assignment, exam, etc
                $table->unsignedBigInteger('related_id')->nullable();
                $table->boolean('is_reminder')->default(false);
                $table->dateTime('reminder_at')->nullable();
                $table->json('tags')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index(['user_id', 'due_date']);
                $table->index(['related_type', 'related_id']);
            });
        }

        // جدول تعليقات المهام
        if (!Schema::hasTable('task_comments')) {
            Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->text('comment');
                $table->string('voice_comment_path')->nullable(); // ملاحظة صوتية
                $table->json('attachments')->nullable();
            $table->timestamps();
                
                $table->index(['task_id', 'created_at']);
            });
        }

        // جدول إشعارات المهام
        if (!Schema::hasTable('task_notifications')) {
            Schema::create('task_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('type', ['reminder', 'due_soon', 'overdue', 'completed', 'comment'])->default('reminder');
                $table->boolean('is_read')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'is_read']);
                $table->index(['task_id', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_notifications');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
    }
};
