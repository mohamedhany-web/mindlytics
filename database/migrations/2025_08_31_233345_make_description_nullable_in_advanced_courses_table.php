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
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        // Skip for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->text('objectives')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->text('objectives')->nullable(false)->change();
        });
    }
};
