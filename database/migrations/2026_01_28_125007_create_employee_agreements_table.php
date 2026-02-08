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
        Schema::create('employee_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade')->comment('الموظف');
            $table->string('agreement_number')->unique()->comment('رقم الاتفاقية');
            $table->string('title')->comment('عنوان الاتفاقية');
            $table->text('description')->nullable()->comment('وصف الاتفاقية');
            $table->decimal('salary', 10, 2)->comment('الراتب الأساسي');
            $table->date('start_date')->comment('تاريخ البدء');
            $table->date('end_date')->nullable()->comment('تاريخ الانتهاء');
            $table->enum('status', ['draft', 'active', 'suspended', 'terminated', 'completed'])->default('draft')->comment('حالة الاتفاقية');
            $table->text('contract_terms')->nullable()->comment('شروط العقد');
            $table->text('agreement_terms')->nullable()->comment('بنود الاتفاقية');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('منشئ الاتفاقية');
            $table->timestamps();
            
            $table->index(['employee_id', 'status']);
            $table->index('agreement_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_agreements');
    }
};
