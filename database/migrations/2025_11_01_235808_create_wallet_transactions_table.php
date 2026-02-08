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
        // جدول المحافظ الذكية
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // اسم المحفظة (مثل: فودافون كاش - رقم 01000000000)
                $table->enum('type', ['vodafone_cash', 'instapay', 'bank_transfer', 'cash', 'other'])->default('other');
                $table->string('account_number')->nullable(); // رقم الحساب أو المحفظة
                $table->string('bank_name')->nullable(); // اسم البنك (للتحويلات)
                $table->string('account_holder')->nullable(); // اسم صاحب الحساب
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->decimal('balance', 15, 2)->default(0); // الرصيد الحالي
                $table->timestamps();
                
                $table->index(['type', 'is_active']);
            });
        }

        // جدول معاملات المحافظ
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
                $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
                $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
                $table->enum('type', ['deposit', 'withdrawal'])->default('deposit');
                $table->decimal('amount', 15, 2);
                $table->decimal('balance_after', 15, 2); // الرصيد بعد العملية
                $table->string('reference_number')->nullable(); // رقم مرجعي
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['wallet_id', 'created_at']);
                $table->index(['payment_id']);
            });
        }

        // جدول تقارير المحافظ الشهرية
        if (!Schema::hasTable('wallet_reports')) {
            Schema::create('wallet_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
                $table->string('report_month'); // YYYY-MM
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('closing_balance', 15, 2)->default(0);
                $table->decimal('total_deposits', 15, 2)->default(0);
                $table->decimal('total_withdrawals', 15, 2)->default(0);
                $table->integer('transactions_count')->default(0);
                $table->json('expected_amounts')->nullable(); // المبالغ المتوقعة
                $table->json('actual_amounts')->nullable(); // المبالغ الفعلية
                $table->decimal('difference', 15, 2)->default(0); // الفرق
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['wallet_id', 'report_month']);
                $table->index(['report_month']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_reports');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
