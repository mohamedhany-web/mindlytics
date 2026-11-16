<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_leads', 'meta_leadgen_id')) {
                    $table->string('meta_leadgen_id', 64)->nullable()->after('import_batch');
                    $table->unique('meta_leadgen_id');
                }
                if (! Schema::hasColumn('sales_leads', 'meta_campaign_id')) {
                    $table->string('meta_campaign_id', 64)->nullable()->after('meta_leadgen_id');
                    $table->index('meta_campaign_id');
                }
                if (! Schema::hasColumn('sales_leads', 'meta_ad_id')) {
                    $table->string('meta_ad_id', 64)->nullable()->after('meta_campaign_id');
                    $table->index('meta_ad_id');
                }
                if (! Schema::hasColumn('sales_leads', 'advertising_campaign_id')) {
                    $table->foreignId('advertising_campaign_id')
                        ->nullable()
                        ->after('meta_ad_id')
                        ->constrained('advertising_campaigns')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('meta_social_conversations')) {
            Schema::table('meta_social_conversations', function (Blueprint $table) {
                if (! Schema::hasColumn('meta_social_conversations', 'meta_campaign_id')) {
                    $table->string('meta_campaign_id', 64)->nullable()->after('sales_lead_id');
                    $table->index('meta_campaign_id');
                }
                if (! Schema::hasColumn('meta_social_conversations', 'meta_ad_id')) {
                    $table->string('meta_ad_id', 64)->nullable()->after('meta_campaign_id');
                    $table->index('meta_ad_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (Schema::hasColumn('sales_leads', 'advertising_campaign_id')) {
                    $table->dropConstrainedForeignId('advertising_campaign_id');
                }
                foreach (['meta_ad_id', 'meta_campaign_id', 'meta_leadgen_id'] as $column) {
                    if (Schema::hasColumn('sales_leads', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('meta_social_conversations')) {
            Schema::table('meta_social_conversations', function (Blueprint $table) {
                foreach (['meta_ad_id', 'meta_campaign_id'] as $column) {
                    if (Schema::hasColumn('meta_social_conversations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
