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
        // التحقق من وجود الجداول المطلوبة
        if (Schema::hasTable('users') && Schema::hasTable('advanced_courses')) {
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID الطالب');
                $table->foreignId('advanced_course_id')->constrained('advanced_courses')->onDelete('cascade')->comment('ID الكورس');
                $table->timestamp('enrolled_at')->useCurrent()->comment('تاريخ التسجيل');
                $table->timestamp('activated_at')->nullable()->comment('تاريخ التفعيل');
                $table->foreignId('activated_by')->nullable()->constrained('users')->onDelete('set null')->comment('مفعل بواسطة');
                $table->enum('status', ['pending', 'active', 'completed', 'suspended'])->default('pending')->comment('حالة التسجيل');
                $table->decimal('progress', 5, 2)->default(0)->comment('نسبة التقدم في الكورس');
                $table->text('notes')->nullable()->comment('ملاحظات إدارية');
                $table->timestamps();
                
                $table->unique(['user_id', 'advanced_course_id'], 'unique_user_course');
                $table->index(['status', 'enrolled_at']);
            });
        } else {
            // إنشاء الجدول بدون foreign keys ثم إضافتها لاحقاً
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->comment('ID الطالب');
                $table->unsignedBigInteger('advanced_course_id')->comment('ID الكورس');
                $table->timestamp('enrolled_at')->useCurrent()->comment('تاريخ التسجيل');
                $table->timestamp('activated_at')->nullable()->comment('تاريخ التفعيل');
                $table->unsignedBigInteger('activated_by')->nullable()->comment('مفعل بواسطة');
                $table->enum('status', ['pending', 'active', 'completed', 'suspended'])->default('pending')->comment('حالة التسجيل');
                $table->decimal('progress', 5, 2)->default(0)->comment('نسبة التقدم في الكورس');
                $table->text('notes')->nullable()->comment('ملاحظات إدارية');
                $table->timestamps();
                
                $table->unique(['user_id', 'advanced_course_id'], 'unique_user_course');
                $table->index(['status', 'enrolled_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_enrollments');
    }
};
