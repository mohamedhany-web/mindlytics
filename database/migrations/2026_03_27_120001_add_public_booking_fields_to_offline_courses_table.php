<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_courses', function (Blueprint $table) {
            $table->boolean('public_booking_enabled')->default(false)->after('is_active');
            $table->dateTime('booking_opens_at')->nullable()->after('public_booking_enabled');
            $table->dateTime('booking_closes_at')->nullable()->after('booking_opens_at');
        });
    }

    public function down(): void
    {
        Schema::table('offline_courses', function (Blueprint $table) {
            $table->dropColumn(['public_booking_enabled', 'booking_opens_at', 'booking_closes_at']);
        });
    }
};
