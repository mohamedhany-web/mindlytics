<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_reviews') || ! Schema::hasTable('advanced_courses')) {
            return;
        }

        // احذف أي مراجعات تشير لكورسات غير موجودة في advanced_courses
        try {
            DB::statement('
                DELETE cr FROM course_reviews cr
                LEFT JOIN advanced_courses ac ON ac.id = cr.course_id
                WHERE ac.id IS NULL
            ');
        } catch (\Throwable $e) {
            report($e);
        }

        $this->dropForeignKeysOnColumn('course_reviews', 'course_id');

        Schema::table('course_reviews', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')
                ->on('advanced_courses')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_reviews')) {
            return;
        }

        $this->dropForeignKeysOnColumn('course_reviews', 'course_id');

        if (Schema::hasTable('courses')) {
            Schema::table('course_reviews', function (Blueprint $table) {
                $table->foreign('course_id')
                    ->references('id')
                    ->on('courses')
                    ->cascadeOnDelete();
            });
        }
    }

    private function dropForeignKeysOnColumn(string $table, string $column): void
    {
        $constraints = DB::select('
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ', [$table, $column]);

        foreach ($constraints as $row) {
            $name = $row->CONSTRAINT_NAME ?? null;
            if (! is_string($name) || $name === '') {
                continue;
            }

            try {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
};
