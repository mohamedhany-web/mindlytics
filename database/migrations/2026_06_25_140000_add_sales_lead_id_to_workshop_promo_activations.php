<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshop_promo_activations', function (Blueprint $table) {
            if (! Schema::hasColumn('workshop_promo_activations', 'sales_lead_id')) {
                $table->foreignId('sales_lead_id')->nullable()->after('user_id')
                    ->constrained('sales_leads')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('workshop_promo_activations', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_promo_activations', 'sales_lead_id')) {
                $table->dropConstrainedForeignId('sales_lead_id');
            }
        });
    }
};
