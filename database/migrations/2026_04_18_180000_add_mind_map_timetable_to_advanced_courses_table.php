<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->text('mind_map_timetable')->nullable()->after('mind_map_published');
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->dropColumn('mind_map_timetable');
        });
    }
};
