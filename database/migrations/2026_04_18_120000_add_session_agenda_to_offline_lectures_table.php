<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offline_lectures') && ! Schema::hasColumn('offline_lectures', 'session_agenda')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->text('session_agenda')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offline_lectures') && Schema::hasColumn('offline_lectures', 'session_agenda')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->dropColumn('session_agenda');
            });
        }
    }
};
