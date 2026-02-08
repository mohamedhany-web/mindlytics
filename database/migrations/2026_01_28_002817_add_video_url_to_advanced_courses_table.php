<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (!Schema::hasColumn('advanced_courses', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('description')->comment('رابط فيديو مقدمة الكورس');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
