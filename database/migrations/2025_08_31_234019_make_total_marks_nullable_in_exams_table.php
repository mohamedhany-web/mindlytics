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
        if (!Schema::hasTable('exams')) {
            return;
        }

        // Skip for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            // جعل total_marks nullable مع قيمة افتراضية
            if (Schema::hasColumn('exams', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->nullable()->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('exams')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->nullable(false)->change();
            }
        });
    }
};
