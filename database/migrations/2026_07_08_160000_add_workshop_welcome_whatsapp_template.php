<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workshops') && ! Schema::hasColumn('workshops', 'welcome_meta_template_id')) {
            Schema::table('workshops', function (Blueprint $table) {
                if (Schema::hasTable('whatsapp_meta_templates')) {
                    $table->foreignId('welcome_meta_template_id')
                        ->nullable()
                        ->after('created_by')
                        ->constrained('whatsapp_meta_templates')
                        ->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('welcome_meta_template_id')->nullable()->after('created_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('workshops', 'welcome_meta_template_id')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->dropConstrainedForeignId('welcome_meta_template_id');
            });
        }
    }
};
