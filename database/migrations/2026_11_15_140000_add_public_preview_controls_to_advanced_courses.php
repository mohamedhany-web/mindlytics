<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('advanced_courses', 'public_lectures_count')) {
                $table->unsignedInteger('public_lectures_count')->nullable()->after('duration_minutes');
            }
            if (! Schema::hasColumn('advanced_courses', 'preview_lecture_ids')) {
                $table->json('preview_lecture_ids')->nullable()->after('public_lectures_count');
            }
            if (! Schema::hasColumn('advanced_courses', 'preview_lesson_ids')) {
                $table->json('preview_lesson_ids')->nullable()->after('preview_lecture_ids');
            }
            if (! Schema::hasColumn('advanced_courses', 'preview_unlocked_count')) {
                $table->unsignedTinyInteger('preview_unlocked_count')->nullable()->after('preview_lesson_ids');
            }
        });
    }

    public function down(): void
    {
        Schema::table('advanced_courses', function (Blueprint $table) {
            foreach (['public_lectures_count', 'preview_lecture_ids', 'preview_lesson_ids', 'preview_unlocked_count'] as $column) {
                if (Schema::hasColumn('advanced_courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
