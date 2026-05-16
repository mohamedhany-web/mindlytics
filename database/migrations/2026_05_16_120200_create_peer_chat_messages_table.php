<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('peer_chat_messages')) {
            return;
        }

        Schema::create('peer_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('peer_chat_threads')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained('peer_chat_messages')->nullOnDelete();
            $table->string('kind', 32)->default('text');
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['thread_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_chat_messages');
    }
};
