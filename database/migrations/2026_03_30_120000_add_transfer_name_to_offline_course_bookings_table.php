<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->string('transfer_name', 255)->nullable()->after('payment_proof');
        });
    }

    public function down(): void
    {
        Schema::table('offline_course_bookings', function (Blueprint $table) {
            $table->dropColumn('transfer_name');
        });
    }
};

