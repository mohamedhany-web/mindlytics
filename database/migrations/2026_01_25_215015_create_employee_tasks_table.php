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
        Schema::create('employee_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade')->comment('الموظف');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade')->comment('المكلف بالمهمة');
            $table->string('title')->comment('عنوان المهمة');
            $table->text('description')->nullable()->comment('وصف المهمة');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('الأولوية');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('pending')->comment('الحالة');
            $table->date('deadline')->nullable()->comment('الموعد النهائي');
            $table->dateTime('started_at')->nullable()->comment('تاريخ البدء');
            $table->dateTime('completed_at')->nullable()->comment('تاريخ الإكمال');
            $table->integer('progress')->default(0)->comment('التقدم (0-100)');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
            
            $table->index('employee_id');
            $table->index('assigned_by');
            $table->index('status');
            $table->index('priority');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_tasks');
    }
};
