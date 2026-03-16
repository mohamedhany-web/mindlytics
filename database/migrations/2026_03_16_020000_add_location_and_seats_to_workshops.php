<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('location')->nullable()->after('title');
            $table->unsignedInteger('seats_online')->default(0)->after('max_seats');
            $table->unsignedInteger('seats_offline')->default(0)->after('seats_online');
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn(['location', 'seats_online', 'seats_offline']);
        });
    }
};

