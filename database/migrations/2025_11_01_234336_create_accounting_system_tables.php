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
        // جدول الفواتير
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('type', ['course', 'subscription', 'membership', 'other'])->default('course');
                $table->string('description');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2);
                $table->enum('status', ['draft', 'pending', 'paid', 'partial', 'overdue', 'cancelled', 'refunded'])->default('pending');
                $table->date('due_date')->nullable();
                $table->date('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->json('items')->nullable(); // عناصر الفاتورة
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index('invoice_number');
                $table->index('due_date');
            });
        }
        
        // جدول المدفوعات
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'online', 'wallet', 'other'])->default('cash');
                $table->enum('payment_gateway', ['manual', 'moyasar', 'stripe', 'paypal', 'other'])->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('EGP');
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
                $table->string('transaction_id')->nullable();
                $table->string('reference_number')->nullable();
                $table->json('gateway_response')->nullable();
                $table->text('notes')->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['invoice_id', 'status']);
                $table->index(['user_id', 'status']);
                $table->index('payment_number');
                $table->index('transaction_id');
            });
        }
        
        // جدول المعاملات المالية
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_number')->unique();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
                $table->enum('type', ['debit', 'credit']); // مدين أو دائن
                $table->enum('category', ['course_payment', 'subscription', 'refund', 'commission', 'fee', 'other'])->default('other');
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('EGP');
                $table->text('description');
                $table->enum('status', ['pending', 'completed', 'cancelled', 'reversed'])->default('completed');
                $table->json('metadata')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['user_id', 'type', 'status']);
                $table->index('transaction_number');
                $table->index('category');
            });
        }
        
        // جدول محافظ العملاء
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                $table->decimal('balance', 10, 2)->default(0);
                $table->decimal('pending_balance', 10, 2)->default(0);
                $table->string('currency', 3)->default('EGP');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index('user_id');
            });
        }
        
        // جدول معاملات المحفظة
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
                $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
                $table->enum('type', ['deposit', 'withdrawal', 'refund', 'commission', 'bonus', 'deduction'])->default('deposit');
                $table->decimal('amount', 10, 2);
                $table->decimal('balance_before', 10, 2);
                $table->decimal('balance_after', 10, 2);
                $table->text('description');
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['wallet_id', 'type', 'status']);
            });
        }
        
        // جدول الاشتراكات
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('subscription_type'); // monthly, yearly, lifetime, etc
                $table->string('plan_name');
                $table->decimal('price', 10, 2);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');
                $table->boolean('auto_renew')->default(false);
                $table->integer('billing_cycle')->default(1); // عدد أشهر
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index('end_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
