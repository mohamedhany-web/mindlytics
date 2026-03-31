<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->boolean('online_booking_enabled')->default(false)->after('public_booking_enabled');
            $table->string('online_slug', 120)->nullable()->unique()->after('online_booking_enabled');
            $table->integer('max_students_online')->default(0)->after('max_students');
            $table->integer('current_students_online')->default(0)->after('current_students');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->dropUnique(['online_slug']);
            $table->dropColumn(['online_booking_enabled', 'online_slug', 'max_students_online', 'current_students_online']);
        });
    }
};

