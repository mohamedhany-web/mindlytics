<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates') || ! Schema::hasColumn('certificates', 'course_id')) {
            return;
        }

        // Drop any existing FK on certificates.course_id (may still point at legacy `courses`).
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'certificates'
              AND COLUMN_NAME = 'course_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($constraints as $row) {
            $name = $row->CONSTRAINT_NAME ?? null;
            if (! $name) {
                continue;
            }
            try {
                DB::statement("ALTER TABLE certificates DROP FOREIGN KEY `{$name}`");
            } catch (\Throwable) {
                // ignore
            }
        }

        // Clear orphan course_ids that are not in advanced_courses (keep course_name text).
        if (Schema::hasTable('advanced_courses')) {
            DB::statement('
                UPDATE certificates c
                LEFT JOIN advanced_courses ac ON ac.id = c.course_id
                SET c.course_id = NULL
                WHERE c.course_id IS NOT NULL AND ac.id IS NULL
            ');
        }

        if (! Schema::hasTable('advanced_courses')) {
            return;
        }

        try {
            DB::statement('
                ALTER TABLE certificates
                ADD CONSTRAINT certificates_course_id_advanced_foreign
                FOREIGN KEY (course_id) REFERENCES advanced_courses(id)
                ON DELETE SET NULL
            ');
        } catch (\Throwable) {
            // already correct / engine limitation
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_course_id_advanced_foreign');
        } catch (\Throwable) {
            // ignore
        }
    }
};
