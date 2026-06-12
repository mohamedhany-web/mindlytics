<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('peer_connection_requests')) {
            return;
        }

        Schema::create('peer_connection_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending | accepted | declined
            $table->timestamps();

            $table->unique(['requester_id', 'recipient_id']);
            $table->index(['recipient_id', 'status']);
            $table->index(['requester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peer_connection_requests');
    }
};
