<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_commission_settlements')) {
            Schema::create('sales_commission_settlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('settled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('leads_count')->default(0);
                $table->decimal('amount_total', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('settled_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'settled_at']);
            });
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'commission_settled_at')) {
                $table->timestamp('commission_settled_at')->nullable()->after('commission_notes')->index();
            }
            if (! Schema::hasColumn('sales_leads', 'commission_settled_by')) {
                $table->foreignId('commission_settled_by')->nullable()->after('commission_settled_at')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('sales_leads', 'commission_settlement_id')) {
                $table->foreignId('commission_settlement_id')->nullable()->after('commission_settled_by')
                    ->constrained('sales_commission_settlements')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            foreach (['commission_settlement_id', 'commission_settled_by', 'commission_settled_at'] as $col) {
                if (Schema::hasColumn('sales_leads', $col)) {
                    try {
                        $table->dropConstrainedForeignId($col);
                    } catch (\Throwable) {
                        $table->dropColumn($col);
                    }
                }
            }
        });

        Schema::dropIfExists('sales_commission_settlements');
    }
};
