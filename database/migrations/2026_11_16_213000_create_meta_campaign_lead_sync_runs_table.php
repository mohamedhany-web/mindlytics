<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meta_campaign_lead_sync_runs')) {
            return;
        }

        Schema::create('meta_campaign_lead_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('meta_campaign_id', 64)->index();
            $table->string('meta_campaign_name')->nullable();
            $table->foreignId('advertising_campaign_id')->nullable()->constrained('advertising_campaigns')->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('queued')->index(); // queued|running|completed|failed
            $table->string('import_batch', 80)->nullable()->index();
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('conversations_linked')->default(0);
            $table->text('message')->nullable();
            $table->json('diagnostics')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_campaign_lead_sync_runs');
    }
};
