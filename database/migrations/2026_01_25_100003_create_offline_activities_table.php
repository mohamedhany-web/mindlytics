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
        Schema::create('offline_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('offline_course_groups')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['assignment', 'quiz', 'project', 'presentation', 'exam', 'other'])->default('assignment');
            $table->date('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->text('instructions')->nullable();
            $table->json('attachments')->nullable()->comment('مرفقات الأنشطة');
            $table->enum('status', ['draft', 'published', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['offline_course_id', 'status']);
            $table->index(['group_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_activities');
    }
};
