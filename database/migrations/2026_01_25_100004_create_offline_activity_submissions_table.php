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
        Schema::create('offline_activity_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('offline_activities')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('submission_content')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded', 'returned'])->default('pending');
            $table->timestamps();
            
            $table->unique(['activity_id', 'student_id'], 'unique_activity_student');
            $table->index(['activity_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_activity_submissions');
    }
};
