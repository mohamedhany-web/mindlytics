<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertising_campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('advertising_campaigns', 'meta_campaign_id')) {
                $table->string('meta_campaign_id', 64)->nullable()->after('notes');
                $table->index('meta_campaign_id');
            }
            if (! Schema::hasColumn('advertising_campaigns', 'meta_campaign_name')) {
                $table->string('meta_campaign_name')->nullable()->after('meta_campaign_id');
            }
            if (! Schema::hasColumn('advertising_campaigns', 'meta_leads_synced_at')) {
                $table->timestamp('meta_leads_synced_at')->nullable()->after('meta_campaign_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advertising_campaigns', function (Blueprint $table) {
            foreach (['meta_leads_synced_at', 'meta_campaign_name', 'meta_campaign_id'] as $column) {
                if (Schema::hasColumn('advertising_campaigns', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
