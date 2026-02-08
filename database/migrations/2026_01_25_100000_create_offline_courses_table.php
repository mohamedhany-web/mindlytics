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
        Schema::create('offline_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade')->comment('المدرب المسؤول');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->foreignId('academic_subject_id')->nullable()->constrained('academic_subjects')->onDelete('set null');
            $table->string('location')->nullable()->comment('مكان الكورس');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('class_time')->nullable()->comment('وقت الحصة');
            $table->integer('duration_hours')->default(0)->comment('عدد الساعات');
            $table->integer('sessions_count')->default(0)->comment('عدد الجلسات');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('max_students')->default(0)->comment('الحد الأقصى للطلاب');
            $table->integer('current_students')->default(0)->comment('عدد الطلاب الحالي');
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable()->comment('ملاحظات إدارية');
            $table->timestamps();
            
            $table->index(['instructor_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_courses');
    }
};
