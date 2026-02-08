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
        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('video_url')->comment('صورة مصغرة للمسار التعليمي');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            if (Schema::hasColumn('academic_years', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};
