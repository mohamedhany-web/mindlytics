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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'wallet', 'cash', 'other'])->default('bank_transfer');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('iban')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['instructor_id', 'status']);
            $table->index('request_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
