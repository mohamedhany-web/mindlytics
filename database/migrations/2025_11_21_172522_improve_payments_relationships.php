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
        Schema::table('payments', function (Blueprint $table) {
            // إضافة ربط بـ Wallet (عند الدفع من المحفظة)
            if (!Schema::hasColumn('payments', 'wallet_id')) {
                $table->unsignedBigInteger('wallet_id')->nullable()->after('payment_method');
                $table->index('wallet_id');
            }
            
            // إضافة ربط بـ InstallmentPayment (عند الدفع من التقسيط)
            if (!Schema::hasColumn('payments', 'installment_payment_id')) {
                $table->unsignedBigInteger('installment_payment_id')->nullable()->after('wallet_id');
                $table->index('installment_payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'installment_payment_id')) {
                $table->dropIndex(['installment_payment_id']);
                $table->dropColumn('installment_payment_id');
            }
            if (Schema::hasColumn('payments', 'wallet_id')) {
                $table->dropIndex(['wallet_id']);
                $table->dropColumn('wallet_id');
            }
        });
    }
};
