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
        if (!Schema::hasTable('questions')) {
            return;
        }

        // Skip for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('question_bank_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questions')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('question_bank_id')->nullable(false)->change();
        });
    }
};
