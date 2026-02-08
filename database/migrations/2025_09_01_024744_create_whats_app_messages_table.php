<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whats_app_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('phone_number');
            $table->text('message');
            $table->string('type')->default('text'); // text, template, document, image
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->json('response_data')->nullable();
            $table->string('whatsapp_message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('template_name')->nullable();
            $table->json('template_params')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'status']);
            $table->index(['user_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_messages');
    }
};
