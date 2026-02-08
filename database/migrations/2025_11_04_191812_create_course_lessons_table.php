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
        if (!Schema::hasTable('course_lessons')) {
            Schema::create('course_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advanced_course_id')->constrained('advanced_courses')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('type')->default('video'); // video, quiz, assignment, document
                $table->text('content')->nullable();
                $table->string('video_url')->nullable();
                $table->json('attachments')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_free')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['advanced_course_id', 'order']);
                $table->index(['advanced_course_id', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
