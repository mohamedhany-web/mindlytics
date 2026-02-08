<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * موارد الكورس الأوفلاين (ملفات/روابط يرفعها المدرب للطلاب)
     */
    public function up(): void
    {
        Schema::create('offline_course_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_course_id')->constrained('offline_courses')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('offline_course_groups')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'link'])->default('file');
            $table->string('file_path')->nullable()->comment('مسار الملف المرفوع');
            $table->string('file_name')->nullable()->comment('اسم الملف الأصلي');
            $table->string('url')->nullable()->comment('رابط خارجي إن كان type=link');
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['offline_course_id', 'is_active']);
            $table->index(['group_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_course_resources');
    }
};
