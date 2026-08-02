<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('meta_social_conversations')) {
            return;
        }

        Schema::table('meta_social_conversations', function (Blueprint $table) {
            if (! Schema::hasColumn('meta_social_conversations', 'labels')) {
                $table->json('labels')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('meta_social_conversations', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('labels');
            }
            if (! Schema::hasColumn('meta_social_conversations', 'reminder_at')) {
                $table->timestamp('reminder_at')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('meta_social_conversations', 'lead_stage')) {
                $table->string('lead_stage', 40)->nullable()->index()->after('reminder_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('meta_social_conversations')) {
            return;
        }

        Schema::table('meta_social_conversations', function (Blueprint $table) {
            foreach (['labels', 'priority', 'reminder_at', 'lead_stage'] as $col) {
                if (Schema::hasColumn('meta_social_conversations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
