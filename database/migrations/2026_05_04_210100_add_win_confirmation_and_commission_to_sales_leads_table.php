<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'won_confirmed_at')) {
                $table->dateTime('won_confirmed_at')->nullable()->after('closed_at');
            }
            if (! Schema::hasColumn('sales_leads', 'won_confirmed_by')) {
                $table->unsignedBigInteger('won_confirmed_by')->nullable()->after('won_confirmed_at');
            }
            if (! Schema::hasColumn('sales_leads', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->nullable()->after('won_confirmed_by');
            }
            if (! Schema::hasColumn('sales_leads', 'commission_transaction_id')) {
                $table->unsignedBigInteger('commission_transaction_id')->nullable()->after('commission_amount');
            }
            if (! Schema::hasColumn('sales_leads', 'commission_notes')) {
                $table->text('commission_notes')->nullable()->after('commission_transaction_id');
            }
        });

        Schema::table('sales_leads', function (Blueprint $table) {
            // قيود خارجية (قد تفشل لو نوع الـ PK مختلف، لذلك نتحقق من وجود الأعمدة فقط)
            if (Schema::hasColumn('sales_leads', 'won_confirmed_by')) {
                try {
                    $table->foreign('won_confirmed_by')->references('id')->on('users')->nullOnDelete();
                } catch (\Throwable $e) {
                    // ignore: some environments already have the constraint or different engine
                }
            }
            if (Schema::hasColumn('sales_leads', 'commission_transaction_id')) {
                try {
                    $table->foreign('commission_transaction_id')->references('id')->on('transactions')->nullOnDelete();
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            // إسقاط القيود إن وجدت
            foreach (['sales_leads_won_confirmed_by_foreign', 'sales_leads_commission_transaction_id_foreign'] as $fk) {
                try {
                    $table->dropForeign($fk);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            foreach ([
                'commission_notes',
                'commission_transaction_id',
                'commission_amount',
                'won_confirmed_by',
                'won_confirmed_at',
            ] as $col) {
                if (Schema::hasColumn('sales_leads', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

