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

        // Skip column modifications for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::table('exams', function (Blueprint $table) {
                // Only add new columns for SQLite
                if (Schema::hasColumn('exams', 'start_date') && !Schema::hasColumn('exams', 'start_time')) {
                    $table->datetime('start_time')->nullable();
                }
                if (Schema::hasColumn('exams', 'end_date') && !Schema::hasColumn('exams', 'end_time')) {
                    $table->datetime('end_time')->nullable();
                }
            });
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            // إصلاح تضارب أسماء الأعمدة
            if (Schema::hasColumn('exams', 'start_date')) {
                $table->datetime('start_date')->nullable()->change();
            }
            if (Schema::hasColumn('exams', 'end_date')) {
                $table->datetime('end_date')->nullable()->change();
            }
            
            // إضافة start_time و end_time إذا لم تكن موجودة وكان start_date موجود
            if (Schema::hasColumn('exams', 'start_date') && !Schema::hasColumn('exams', 'start_time')) {
                $table->datetime('start_time')->nullable();
            }
            if (Schema::hasColumn('exams', 'end_date') && !Schema::hasColumn('exams', 'end_time')) {
                $table->datetime('end_time')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // لا نحتاج عكس هذه التغييرات
        });
    }
};
