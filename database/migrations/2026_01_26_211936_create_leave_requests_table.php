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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['annual', 'sick', 'emergency', 'unpaid', 'other'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'status']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
