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
        Schema::create('employee_task_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('employee_tasks')->onDelete('cascade')->comment('المهمة');
            $table->string('title')->comment('عنوان التسليم');
            $table->text('description')->nullable()->comment('وصف التسليم');
            $table->string('file_path')->nullable()->comment('مسار الملف');
            $table->string('file_name')->nullable()->comment('اسم الملف');
            $table->string('file_type')->nullable()->comment('نوع الملف');
            $table->integer('file_size')->nullable()->comment('حجم الملف');
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected', 'needs_revision'])->default('pending')->comment('حالة التسليم');
            $table->text('feedback')->nullable()->comment('ملاحظات المراجع');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->comment('المراجع');
            $table->dateTime('reviewed_at')->nullable()->comment('تاريخ المراجعة');
            $table->dateTime('submitted_at')->nullable()->comment('تاريخ التسليم');
            $table->timestamps();
            
            $table->index('task_id');
            $table->index('status');
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_task_deliverables');
    }
};
