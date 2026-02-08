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
        Schema::table('transactions', function (Blueprint $table) {
            // إضافة ربط مباشر بـ Invoice
            if (!Schema::hasColumn('transactions', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('payment_id');
                $table->index('invoice_id');
            }
            
            // إضافة ربط بـ Expense
            if (!Schema::hasColumn('transactions', 'expense_id')) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('invoice_id');
                $table->index('expense_id');
            }
            
            // إضافة ربط بـ Subscription
            if (!Schema::hasColumn('transactions', 'subscription_id')) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('expense_id');
                $table->index('subscription_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'subscription_id')) {
                $table->dropIndex(['subscription_id']);
                $table->dropColumn('subscription_id');
            }
            if (Schema::hasColumn('transactions', 'expense_id')) {
                $table->dropIndex(['expense_id']);
                $table->dropColumn('expense_id');
            }
            if (Schema::hasColumn('transactions', 'invoice_id')) {
                $table->dropIndex(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
        });
    }
};
