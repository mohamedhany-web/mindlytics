<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->foreignId('requested_group_id')->nullable()->after('offline_course_id')->constrained('offline_course_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('requested_group_id');
        });
    }
};
