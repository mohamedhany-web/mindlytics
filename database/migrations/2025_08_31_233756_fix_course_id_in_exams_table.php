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

        Schema::table('exams', function (Blueprint $table) {
            // إضافة عمود advanced_course_id إذا لم يكن موجوداً
            if (!Schema::hasColumn('exams', 'advanced_course_id')) {
                $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->onDelete('cascade');
            }
        });

        // Skip column modifications for SQLite
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        
        Schema::table('exams', function (Blueprint $table) {
            // جعل course_id nullable إذا كان موجوداً
            if (Schema::hasColumn('exams', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->change();
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

        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'advanced_course_id')) {
                $table->dropForeign(['advanced_course_id']);
                $table->dropColumn('advanced_course_id');
            }
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }
        
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable(false)->change();
            }
        });
    }
};
