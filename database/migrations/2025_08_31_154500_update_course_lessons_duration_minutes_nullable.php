<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('course_lessons')) {
            return;
        }

        // For SQLite, we need to handle column changes differently
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite doesn't support modifying columns easily
            // Skip this migration for SQLite as the column will be created properly in the main migration
            return;
        }

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->integer('duration_minutes')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('course_lessons')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->integer('duration_minutes')->nullable(false)->change();
        });
    }
};
