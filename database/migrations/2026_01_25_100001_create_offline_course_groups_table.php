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
        Schema::create('offline_course_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade')->comment('المدرب المسؤول عن المجموعة');
            $table->string('name')->comment('اسم المجموعة');
            $table->text('description')->nullable();
            $table->integer('max_students')->default(0);
            $table->integer('current_students')->default(0);
            $table->string('location')->nullable()->comment('مكان المجموعة');
            $table->time('class_time')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['offline_course_id', 'instructor_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_course_groups');
    }
};
