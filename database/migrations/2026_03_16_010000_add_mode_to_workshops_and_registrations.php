<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('mode', 20)->default('online')->after('description'); // online, offline, both
        });

        Schema::table('workshop_registrations', function (Blueprint $table) {
            $table->string('attendance_mode', 20)->nullable()->after('phone'); // online أو offline
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('mode');
        });

        Schema::table('workshop_registrations', function (Blueprint $table) {
            $table->dropColumn('attendance_mode');
        });
    }
};

