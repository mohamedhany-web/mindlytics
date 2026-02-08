<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('contact_messages', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                if (Schema::hasColumn('contact_messages', 'read_at')) {
                    $table->dropColumn('read_at');
                }
            });
        }
    }
};
