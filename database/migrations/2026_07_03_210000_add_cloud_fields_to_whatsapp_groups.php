<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_groups')) {
            return;
        }

        Schema::table('whatsapp_groups', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_groups', 'join_approval_mode')) {
                $table->string('join_approval_mode', 30)->default('auto_approve')->after('restrict_info');
            }
            if (! Schema::hasColumn('whatsapp_groups', 'invite_template_name')) {
                $table->string('invite_template_name')->nullable()->after('join_approval_mode');
            }
            if (! Schema::hasColumn('whatsapp_groups', 'invite_template_language')) {
                $table->string('invite_template_language', 20)->default('en')->after('invite_template_name');
            }
            if (! Schema::hasColumn('whatsapp_groups', 'api_provider')) {
                $table->string('api_provider', 20)->default('meta_cloud')->after('invite_template_language');
            }
        });

        if (Schema::hasTable('whatsapp_group_participants') && ! Schema::hasColumn('whatsapp_group_participants', 'invited_at')) {
            Schema::table('whatsapp_group_participants', function (Blueprint $table) {
                $table->timestamp('invited_at')->nullable()->after('error_message');
                $table->timestamp('joined_at')->nullable()->after('invited_at');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('whatsapp_groups')) {
            return;
        }

        Schema::table('whatsapp_groups', function (Blueprint $table) {
            foreach (['join_approval_mode', 'invite_template_name', 'invite_template_language', 'api_provider'] as $col) {
                if (Schema::hasColumn('whatsapp_groups', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (Schema::hasTable('whatsapp_group_participants')) {
            Schema::table('whatsapp_group_participants', function (Blueprint $table) {
                foreach (['invited_at', 'joined_at'] as $col) {
                    if (Schema::hasColumn('whatsapp_group_participants', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
