<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_batch_items', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_batch_items', 'send_attempts')) {
                $table->unsignedTinyInteger('send_attempts')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_batch_items', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_batch_items', 'send_attempts')) {
                $table->dropColumn('send_attempts');
            }
        });
    }
};
