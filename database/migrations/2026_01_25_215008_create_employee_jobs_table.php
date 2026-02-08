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
        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم الوظيفة');
            $table->string('code')->unique()->comment('رمز الوظيفة');
            $table->text('description')->nullable()->comment('وصف الوظيفة');
            $table->text('responsibilities')->nullable()->comment('المسؤوليات');
            $table->json('permissions')->nullable()->comment('الصلاحيات المخصصة للوظيفة');
            $table->integer('min_salary')->nullable()->comment('الحد الأدنى للراتب');
            $table->integer('max_salary')->nullable()->comment('الحد الأقصى للراتب');
            $table->boolean('is_active')->default(true)->comment('حالة الوظيفة');
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_jobs');
    }
};
