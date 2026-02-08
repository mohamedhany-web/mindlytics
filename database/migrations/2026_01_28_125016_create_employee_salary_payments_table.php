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
        Schema::create('employee_salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade')->comment('الموظف');
            $table->foreignId('agreement_id')->nullable()->constrained('employee_agreements')->onDelete('set null')->comment('الاتفاقية');
            $table->string('payment_number')->unique()->comment('رقم الدفعة');
            $table->decimal('base_salary', 10, 2)->comment('الراتب الأساسي');
            $table->decimal('total_deductions', 10, 2)->default(0)->comment('إجمالي الخصومات');
            $table->decimal('net_salary', 10, 2)->comment('صافي الراتب');
            $table->date('payment_date')->comment('تاريخ الاستحقاق');
            $table->date('paid_at')->nullable()->comment('تاريخ الدفع الفعلي');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending')->comment('حالة الدفعة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('منشئ الدفعة');
            $table->timestamps();
            
            $table->index(['employee_id', 'status']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_payments');
    }
};
