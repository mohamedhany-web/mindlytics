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
        Schema::create('employee_salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade')->comment('الموظف');
            $table->foreignId('agreement_id')->nullable()->constrained('employee_agreements')->onDelete('set null')->comment('الاتفاقية');
            $table->string('deduction_number')->unique()->comment('رقم الخصم');
            $table->string('title')->comment('عنوان الخصم');
            $table->text('description')->nullable()->comment('وصف الخصم');
            $table->decimal('amount', 10, 2)->comment('مبلغ الخصم');
            $table->enum('type', ['tax', 'insurance', 'loan', 'penalty', 'other'])->default('other')->comment('نوع الخصم');
            $table->date('deduction_date')->comment('تاريخ الخصم');
            $table->enum('status', ['pending', 'applied', 'cancelled'])->default('pending')->comment('حالة الخصم');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('منشئ الخصم');
            $table->timestamps();
            
            $table->index(['employee_id', 'status']);
            $table->index('deduction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_deductions');
    }
};
