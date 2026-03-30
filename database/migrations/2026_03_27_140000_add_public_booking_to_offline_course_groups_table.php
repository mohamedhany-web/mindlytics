<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->string('public_slug', 120)->nullable()->unique()->after('is_active');
            $table->boolean('public_booking_enabled')->default(false)->after('public_slug');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_groups', function (Blueprint $table) {
            $table->dropColumn(['public_slug', 'public_booking_enabled']);
        });
    }
};
