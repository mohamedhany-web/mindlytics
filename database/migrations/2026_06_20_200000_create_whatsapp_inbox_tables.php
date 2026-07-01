<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('contact_name')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_preview', 500)->nullable();
            $table->enum('last_message_direction', ['inbound', 'outbound'])->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('last_message_at');
            $table->index('unread_count');
        });

        Schema::create('whatsapp_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->text('body')->nullable();
            $table->string('message_type', 50)->default('text');
            $table->string('whatsapp_message_id')->nullable()->unique();
            $table->enum('status', ['received', 'pending', 'sent', 'delivered', 'read', 'failed'])->default('received');
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('template_name')->nullable();
            $table->json('template_params')->nullable();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversation_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
