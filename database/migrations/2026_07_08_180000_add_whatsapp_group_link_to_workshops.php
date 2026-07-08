<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workshops') && ! Schema::hasColumn('workshops', 'whatsapp_group_link')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->string('whatsapp_group_link', 500)->nullable()->after('location');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('workshops', 'whatsapp_group_link')) {
            Schema::table('workshops', function (Blueprint $table) {
                $table->dropColumn('whatsapp_group_link');
            });
        }
    }
};
