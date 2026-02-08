<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (!Schema::hasColumn('advanced_courses', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->after('id');
                $table->foreign('academic_year_id')
                    ->references('id')
                    ->on('academic_years')
                    ->onDelete('set null');
            }

            if (!Schema::hasColumn('advanced_courses', 'academic_subject_id')) {
                $table->unsignedBigInteger('academic_subject_id')->nullable()->after('academic_year_id');
                $table->foreign('academic_subject_id')
                    ->references('id')
                    ->on('academic_subjects')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (Schema::hasColumn('advanced_courses', 'academic_subject_id')) {
                $table->dropForeign(['academic_subject_id']);
                $table->dropColumn('academic_subject_id');
            }

            if (Schema::hasColumn('advanced_courses', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });
    }
};
