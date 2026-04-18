<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offline_lectures') && ! Schema::hasColumn('offline_lectures', 'offline_group_session_id')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->foreignId('offline_group_session_id')
                    ->nullable()
                    ->after('group_id')
                    ->constrained('offline_group_sessions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offline_lectures') && Schema::hasColumn('offline_lectures', 'offline_group_session_id')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->dropConstrainedForeignId('offline_group_session_id');
            });
        }
    }
};
