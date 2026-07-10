<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_team_conversations')) {
            Schema::create('sales_team_conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_team_id')->constrained('sales_teams')->cascadeOnDelete();
                $table->string('type', 20); // direct | team
                $table->string('title')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('last_message_at')->nullable()->index();
                $table->timestamps();

                $table->index(['sales_team_id', 'type']);
            });
        }

        if (! Schema::hasTable('sales_team_conversation_participants')) {
            Schema::create('sales_team_conversation_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('sales_team_conversations')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();

                $table->unique(['conversation_id', 'user_id'], 'stc_participants_unique');
            });
        }

        if (! Schema::hasTable('sales_team_messages')) {
            Schema::create('sales_team_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('sales_team_conversations')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('body');
                $table->foreignId('reply_to_id')->nullable()->constrained('sales_team_messages')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['conversation_id', 'id']);
            });
        }

        if (! Schema::hasTable('sales_team_message_reactions')) {
            Schema::create('sales_team_message_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('sales_team_messages')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('emoji', 16);
                $table->timestamps();

                $table->unique(['message_id', 'user_id', 'emoji'], 'stc_reactions_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_team_message_reactions');
        Schema::dropIfExists('sales_team_messages');
        Schema::dropIfExists('sales_team_conversation_participants');
        Schema::dropIfExists('sales_team_conversations');
    }
};
