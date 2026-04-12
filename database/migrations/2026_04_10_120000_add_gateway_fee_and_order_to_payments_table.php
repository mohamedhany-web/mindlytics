<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'gateway_fee_amount')) {
                $table->decimal('gateway_fee_amount', 12, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('payments', 'gateway_fee_detail')) {
                $table->json('gateway_fee_detail')->nullable()->after('gateway_fee_amount');
            }
            if (! Schema::hasColumn('payments', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('invoice_id')->constrained('orders')->nullOnDelete();
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            try {
                DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('manual','moyasar','stripe','paypal','other','kashier','fawaterak') NULL");
            } catch (\Throwable $e) {
                // تجاهل إن كان العمود غير ENUM أو تم تعديله مسبقاً
            }
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'order_id')) {
                $table->dropForeign(['order_id']);
            }
        });
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'order_id')) {
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('payments', 'gateway_fee_detail')) {
                $table->dropColumn('gateway_fee_detail');
            }
            if (Schema::hasColumn('payments', 'gateway_fee_amount')) {
                $table->dropColumn('gateway_fee_amount');
            }
        });
    }
};
