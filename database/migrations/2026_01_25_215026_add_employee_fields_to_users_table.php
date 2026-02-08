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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_job_id')->nullable()->after('role')->constrained('employee_jobs')->onDelete('set null')->comment('وظيفة الموظف');
            $table->string('employee_code')->nullable()->unique()->after('employee_job_id')->comment('رمز الموظف');
            $table->date('hire_date')->nullable()->after('employee_code')->comment('تاريخ التوظيف');
            $table->date('termination_date')->nullable()->after('hire_date')->comment('تاريخ إنهاء الخدمة');
            $table->decimal('salary', 10, 2)->nullable()->after('termination_date')->comment('الراتب');
            $table->text('employee_notes')->nullable()->after('salary')->comment('ملاحظات الموظف');
            $table->boolean('is_employee')->default(false)->after('employee_notes')->comment('هل هو موظف');
            
            $table->index('employee_job_id');
            $table->index('employee_code');
            $table->index('is_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_job_id']);
            $table->dropColumn([
                'employee_job_id',
                'employee_code',
                'hire_date',
                'termination_date',
                'salary',
                'employee_notes',
                'is_employee'
            ]);
        });
    }
};
