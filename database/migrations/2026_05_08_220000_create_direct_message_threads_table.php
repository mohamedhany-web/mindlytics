<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('direct_message_threads')) {
            return;
        }

        Schema::create('direct_message_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('advanced_courses')->onDelete('set null');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'instructor_id', 'student_id'], 'dm_threads_unique');
            $table->index(['instructor_id', 'last_message_at']);
            $table->index(['student_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_message_threads');
    }
};

