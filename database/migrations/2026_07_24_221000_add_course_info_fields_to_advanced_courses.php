<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'suitable_for')) {
                $table->text('suitable_for')->nullable()->after('what_you_learn');
            }
            if (! Schema::hasColumn('advanced_courses', 'instructor_info')) {
                $table->text('instructor_info')->nullable()->after('suitable_for');
            }
            if (! Schema::hasColumn('advanced_courses', 'available_until_info')) {
                $table->text('available_until_info')->nullable()->after('instructor_info');
            }
            if (! Schema::hasColumn('advanced_courses', 'follow_up_info')) {
                $table->text('follow_up_info')->nullable()->after('available_until_info');
            }
            if (! Schema::hasColumn('advanced_courses', 'age_suitability')) {
                $table->text('age_suitability')->nullable()->after('follow_up_info');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        Schema::table('advanced_courses', function (Blueprint $table) {
            foreach (['suitable_for', 'instructor_info', 'available_until_info', 'follow_up_info', 'age_suitability'] as $col) {
                if (Schema::hasColumn('advanced_courses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
