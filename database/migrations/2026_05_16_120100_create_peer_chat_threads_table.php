<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('peer_chat_threads')) {
            return;
        }

        Schema::create('peer_chat_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_low_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_high_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['user_low_id', 'user_high_id'], 'peer_chat_threads_pair_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_chat_threads');
    }
};
