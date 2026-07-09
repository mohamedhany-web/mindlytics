<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_suggested_templates') || ! Schema::hasTable('whatsapp_meta_templates')) {
            return;
        }

        Schema::table('whatsapp_suggested_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_suggested_templates', 'meta_template_id')) {
                $table->foreignId('meta_template_id')
                    ->nullable()
                    ->after('sort_order')
                    ->constrained('whatsapp_meta_templates')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('whatsapp_suggested_templates')) {
            return;
        }

        Schema::table('whatsapp_suggested_templates', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_suggested_templates', 'meta_template_id')) {
                $table->dropConstrainedForeignId('meta_template_id');
            }
        });
    }
};
