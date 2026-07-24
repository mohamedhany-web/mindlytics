<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'admin_unlock_all_videos')) {
                $table->boolean('admin_unlock_all_videos')->default(false)->after('is_scholarship_only');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'admin_unlock_all_videos')) {
                $table->dropColumn('admin_unlock_all_videos');
            }
        });
    }
};
