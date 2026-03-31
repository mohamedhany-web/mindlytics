<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->string('booking_channel', 16)->default('offline')->after('offline_course_id');
            $table->index(['requested_group_id', 'booking_channel', 'status'], 'idx_group_channel_status');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->dropIndex('idx_group_channel_status');
            $table->dropColumn('booking_channel');
        });
    }
};

