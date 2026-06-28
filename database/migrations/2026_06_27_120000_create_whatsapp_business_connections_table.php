<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_business_connections', function (Blueprint $table) {
            $table->id();
            $table->string('business_portfolio_id')->nullable();
            $table->string('waba_id')->nullable();
            $table->string('phone_number_id');
            $table->string('display_phone_number')->nullable();
            $table->string('verified_display_name')->nullable();
            $table->text('access_token');
            $table->string('status', 20)->default('connected');
            $table->timestamp('webhook_subscribed_at')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->foreignId('connected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('phone_number_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_business_connections');
    }
};
