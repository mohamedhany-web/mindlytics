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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('wallet_id');
                $table->index('invoice_id');
            }
            if (!Schema::hasColumn('orders', 'payment_id')) {
                $table->unsignedBigInteger('payment_id')->nullable()->after('invoice_id');
                $table->index('payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_id')) {
                $table->dropIndex(['payment_id']);
                $table->dropColumn('payment_id');
            }
            if (Schema::hasColumn('orders', 'invoice_id')) {
                $table->dropIndex(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
        });
    }
};
