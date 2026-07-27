<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advertising_campaigns')) {
            Schema::create('advertising_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('platform')->nullable();
                $table->text('description')->nullable();
                $table->decimal('cost', 12, 2)->default(0);
                $table->string('currency', 3)->default('EGP');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['is_active', 'start_date']);
            });
        }

        if (! Schema::hasTable('advertising_campaign_sales_user')) {
            Schema::create('advertising_campaign_sales_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advertising_campaign_id')->constrained('advertising_campaigns')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['advertising_campaign_id', 'user_id'], 'ad_campaign_sales_user_unique');
            });
        }

        if (! Schema::hasTable('campaign_daily_reports')) {
            Schema::create('campaign_daily_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advertising_campaign_id')->constrained('advertising_campaigns')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('sales_daily_report_id')->nullable()->constrained('sales_daily_reports')->nullOnDelete();
                $table->date('report_date');

                $table->unsignedInteger('new_messages')->default(0);
                $table->unsignedInteger('whatsapp_messages')->default(0);
                $table->unsignedInteger('messenger_messages')->default(0);
                $table->unsignedInteger('instagram_messages')->default(0);
                $table->unsignedInteger('qualified')->default(0);
                $table->unsignedInteger('unqualified')->default(0);
                $table->unsignedInteger('converted')->default(0);
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->unique(['advertising_campaign_id', 'user_id', 'report_date'], 'campaign_daily_unique');
                $table->index(['report_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_daily_reports');
        Schema::dropIfExists('advertising_campaign_sales_user');
        Schema::dropIfExists('advertising_campaigns');
    }
};
