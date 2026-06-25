<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_batch_items', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_batch_items', 'sales_lead_id')) {
                $table->unsignedBigInteger('sales_lead_id')->nullable()->after('workshop_registration_id');
                $table->index('sales_lead_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_batch_items', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_batch_items', 'sales_lead_id')) {
                $table->dropIndex(['sales_lead_id']);
                $table->dropColumn('sales_lead_id');
            }
        });
    }
};
