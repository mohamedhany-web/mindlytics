<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offline_lectures') && ! Schema::hasColumn('offline_lectures', 'meeting_url')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->string('meeting_url')->nullable()->after('scheduled_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offline_lectures') && Schema::hasColumn('offline_lectures', 'meeting_url')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->dropColumn('meeting_url');
            });
        }
    }
};
