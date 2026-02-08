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
        Schema::create('offline_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('الطالب');
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('offline_course_groups')->onDelete('set null');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->enum('status', ['pending', 'active', 'completed', 'suspended', 'cancelled'])->default('pending');
            $table->decimal('progress', 5, 2)->default(0)->comment('نسبة التقدم');
            $table->integer('attendance_count')->default(0)->comment('عدد مرات الحضور');
            $table->integer('absence_count')->default(0)->comment('عدد مرات الغياب');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'offline_course_id'], 'unique_student_offline_course');
            $table->index(['offline_course_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_course_enrollments');
    }
};
