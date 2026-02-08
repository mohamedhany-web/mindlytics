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
        Schema::table('expenses', function (Blueprint $table) {
            // إضافة ربط بـ Transaction (عند الموافقة على المصروف)
            if (!Schema::hasColumn('expenses', 'transaction_id')) {
                $table->unsignedBigInteger('transaction_id')->nullable()->after('approved_at');
                $table->index('transaction_id');
            }
            
            // إضافة ربط بـ Invoice (إذا كان هناك فاتورة للمصروف)
            if (!Schema::hasColumn('expenses', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('transaction_id');
                $table->index('invoice_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'invoice_id')) {
                $table->dropIndex(['invoice_id']);
                $table->dropColumn('invoice_id');
            }
            if (Schema::hasColumn('expenses', 'transaction_id')) {
                $table->dropIndex(['transaction_id']);
                $table->dropColumn('transaction_id');
            }
        });
    }
};
