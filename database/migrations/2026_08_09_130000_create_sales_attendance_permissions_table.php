<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_attendance_permissions')) {
            return;
        }

        Schema::create('sales_attendance_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('granted_by')->constrained('users')->cascadeOnDelete();
            $table->string('type', 32); // day_absence | early_departure
            $table->date('work_date');
            $table->time('early_departure_time')->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('status', 20)->default('approved'); // approved | revoked
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revoke_reason', 500)->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'work_date', 'type', 'status'], 'sales_att_perm_emp_date_type_status');
            $table->index(['granted_by', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_attendance_permissions');
    }
};
