<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_task_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete()->comment('المشرف الذي أنشأ الطلب');
            $table->foreignId('designer_employee_id')->constrained('users')->cascadeOnDelete()->comment('المصمم');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('specifications')->nullable()->comment('تفاصيل التصميم المطلوب');
            $table->string('priority', 20)->default('medium');
            $table->dateTime('deadline_at')->comment('حد أقصى لتسليم المصمم');
            $table->string('status', 40)->default('pending_design')->index();
            // يُربط بـ employee_tasks بعد إضافة design_cycle_id في جدول المهام (هجرة لاحقة)
            $table->unsignedBigInteger('designer_task_id')->nullable()->index();
            $table->unsignedBigInteger('moderator_delivery_task_id')->nullable()->index();
            $table->timestamp('designer_submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable()->comment('اكتمال الدورة بعد تسليم المشرف');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['moderator_id', 'status']);
            $table->index(['designer_employee_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_task_cycles');
    }
};
