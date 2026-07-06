<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('meta_social_pages')) {
            return;
        }

        Schema::table('meta_social_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('meta_social_pages', 'meta_social_connection_id')) {
                $table->foreignId('meta_social_connection_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('meta_social_connections')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('meta_social_pages')) {
            return;
        }

        Schema::table('meta_social_pages', function (Blueprint $table) {
            if (Schema::hasColumn('meta_social_pages', 'meta_social_connection_id')) {
                $table->dropConstrainedForeignId('meta_social_connection_id');
            }
        });
    }
};
