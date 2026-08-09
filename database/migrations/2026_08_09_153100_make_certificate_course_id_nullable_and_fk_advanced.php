<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates') || ! Schema::hasColumn('certificates', 'course_id')) {
            return;
        }

        // Drop any FK currently on course_id.
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

        // FK with ON DELETE SET NULL requires nullable column.
        Schema::table('certificates', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
        });

        if (Schema::hasTable('advanced_courses')) {
            DB::statement('
                UPDATE certificates c
                LEFT JOIN advanced_courses ac ON ac.id = c.course_id
                SET c.course_id = NULL
                WHERE c.course_id IS NOT NULL AND ac.id IS NULL
            ');

            $exists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'certificates'
                  AND CONSTRAINT_NAME = 'certificates_course_id_advanced_foreign'
            ");

            if ($exists === []) {
                DB::statement('
                    ALTER TABLE certificates
                    ADD CONSTRAINT certificates_course_id_advanced_foreign
                    FOREIGN KEY (course_id) REFERENCES advanced_courses(id)
                    ON DELETE SET NULL
                ');
            }
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
