<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_debts', function (Blueprint $table) {
            $table->id();
            $table->string('debt_number', 32)->unique();
            $table->enum('direction', ['payable', 'receivable'])->default('payable');
            $table->string('party_name');
            $table->string('party_phone')->nullable();
            $table->string('party_relation')->nullable();
            $table->string('title')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->boolean('deposited_to_wallet')->default(false);
            $table->date('debt_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['active', 'partial', 'settled', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['direction', 'status']);
            $table->index('debt_date');
        });

        Schema::create('accounting_debt_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_debt_id')->constrained('accounting_debts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('paid_at');
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_debt_repayments');
        Schema::dropIfExists('accounting_debts');
    }
};
