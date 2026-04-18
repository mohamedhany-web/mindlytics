<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offline_lectures') && ! Schema::hasColumn('offline_lectures', 'offline_attendee_mindmap')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->text('offline_attendee_mindmap')->nullable()->after('session_agenda');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offline_lectures') && Schema::hasColumn('offline_lectures', 'offline_attendee_mindmap')) {
            Schema::table('offline_lectures', function (Blueprint $table) {
                $table->dropColumn('offline_attendee_mindmap');
            });
        }
    }
};
