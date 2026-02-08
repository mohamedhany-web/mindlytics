<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * محاضرات الكورس الأوفلاين (عنوان، وصف، روابط تحميل، مرفقات)
     */
    public function up(): void
    {
        Schema::create('offline_lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('offline_course_groups')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('recording_url')->nullable()->comment('رابط تسجيل المحاضرة');
            $table->json('download_links')->nullable()->comment('روابط تحميل [{"label":"","url":""}]');
            $table->json('attachments')->nullable()->comment('مرفقات المحاضرة');
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['offline_course_id', 'is_active']);
            $table->index(['group_id', 'is_active']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_lectures');
    }
};
