<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_group_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('offline_course_groups')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(60);
            $table->string('location')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['group_id', 'session_date']);
            $table->index(['instructor_id', 'session_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_group_sessions');
    }
};
