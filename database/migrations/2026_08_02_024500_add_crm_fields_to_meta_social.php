<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meta_social_conversations')) {
            Schema::table('meta_social_conversations', function (Blueprint $table) {
                if (! Schema::hasColumn('meta_social_conversations', 'sales_lead_id')) {
                    $table->foreignId('sales_lead_id')->nullable()->after('assigned_to')
                        ->constrained('sales_leads')->nullOnDelete();
                }
                if (! Schema::hasColumn('meta_social_conversations', 'phone')) {
                    $table->string('phone', 40)->nullable()->after('participant_profile_pic');
                }
                if (! Schema::hasColumn('meta_social_conversations', 'email')) {
                    $table->string('email')->nullable()->after('phone');
                }
                if (! Schema::hasColumn('meta_social_conversations', 'notes')) {
                    $table->text('notes')->nullable()->after('email');
                }
            });
        }

        if (Schema::hasTable('meta_social_messages')) {
            // احذف التكرارات قبل unique (يبقي أصغر id)
            try {
                \Illuminate\Support\Facades\DB::statement('
                    DELETE m1 FROM meta_social_messages m1
                    INNER JOIN meta_social_messages m2
                      ON m1.meta_message_id = m2.meta_message_id
                     AND m1.meta_message_id IS NOT NULL
                     AND m1.id > m2.id
                ');
            } catch (\Throwable) {
                // sqlite / unsupported — ignore
            }

            Schema::table('meta_social_messages', function (Blueprint $table) {
                try {
                    $table->unique('meta_message_id', 'meta_social_messages_meta_message_id_unique');
                } catch (\Throwable) {
                    // index may already exist
                }
                try {
                    $table->index(
                        ['meta_social_conversation_id', 'sent_at', 'id'],
                        'meta_social_messages_conv_sent_id_index'
                    );
                } catch (\Throwable) {
                    // ignore
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('meta_social_conversations')) {
            Schema::table('meta_social_conversations', function (Blueprint $table) {
                if (Schema::hasColumn('meta_social_conversations', 'sales_lead_id')) {
                    $table->dropConstrainedForeignId('sales_lead_id');
                }
                foreach (['phone', 'email', 'notes'] as $col) {
                    if (Schema::hasColumn('meta_social_conversations', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
