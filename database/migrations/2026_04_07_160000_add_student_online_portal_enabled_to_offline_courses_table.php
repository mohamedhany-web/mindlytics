<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_courses', function (Blueprint $table) {
            $table->boolean('student_online_portal_enabled')->default(false)->after('public_booking_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('offline_courses', function (Blueprint $table) {
            $table->dropColumn('student_online_portal_enabled');
        });
    }
};
