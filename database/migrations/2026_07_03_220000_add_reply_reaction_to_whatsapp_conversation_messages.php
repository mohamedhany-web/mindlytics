<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_conversation_messages')) {
            return;
        }

        Schema::table('whatsapp_conversation_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_conversation_messages', 'context_wa_message_id')) {
                $table->string('context_wa_message_id')->nullable()->after('whatsapp_message_id');
            }
            if (! Schema::hasColumn('whatsapp_conversation_messages', 'context_preview')) {
                $table->string('context_preview', 500)->nullable()->after('context_wa_message_id');
            }
            if (! Schema::hasColumn('whatsapp_conversation_messages', 'reaction_emoji')) {
                $table->string('reaction_emoji', 16)->nullable()->after('context_preview');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('whatsapp_conversation_messages')) {
            return;
        }

        Schema::table('whatsapp_conversation_messages', function (Blueprint $table) {
            foreach (['context_wa_message_id', 'context_preview', 'reaction_emoji'] as $col) {
                if (Schema::hasColumn('whatsapp_conversation_messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
