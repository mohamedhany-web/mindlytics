<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CertificateCourseForeignKey
{
    /**
     * Drop legacy FK to `courses` and point course_id at `advanced_courses`.
     */
    public function fix(): bool
    {
        if (! Schema::hasTable('certificates') || ! Schema::hasColumn('certificates', 'course_id')) {
            return false;
        }

        if (! Schema::hasTable('advanced_courses')) {
            return false;
        }

        try {
            $this->dropAllCourseIdForeignKeys();
            $this->makeCourseIdNullable();
            $this->nullOrphanCourseIds();
            $this->addAdvancedCoursesForeignKey();

            return $this->referencesAdvancedCourses();
        } catch (\Throwable $e) {
            Log::error('CertificateCourseForeignKey fix failed: '.$e->getMessage());

            return false;
        }
    }

    public function referencesAdvancedCourses(): bool
    {
        $rows = DB::select("
            SELECT REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'certificates'
              AND COLUMN_NAME = 'course_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");

        return isset($rows[0]) && ($rows[0]->REFERENCED_TABLE_NAME ?? null) === 'advanced_courses';
    }

    public function referencesLegacyCourses(): bool
    {
        $rows = DB::select("
            SELECT REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'certificates'
              AND COLUMN_NAME = 'course_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");

        return isset($rows[0]) && ($rows[0]->REFERENCED_TABLE_NAME ?? null) === 'courses';
    }

    private function dropAllCourseIdForeignKeys(): void
    {
        // Explicit known names first (production + Laravel defaults).
        foreach ([
            'certificates_course_id_foreign',
            'certificates_ibfk_1',
            'certificates_course_id_advanced_foreign',
        ] as $name) {
            try {
                DB::statement("ALTER TABLE `certificates` DROP FOREIGN KEY `{$name}`");
            } catch (\Throwable) {
                // ignore
            }
        }

        $constraints = DB::select("
            SELECT DISTINCT CONSTRAINT_NAME
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
                DB::statement("ALTER TABLE `certificates` DROP FOREIGN KEY `{$name}`");
            } catch (\Throwable) {
                // ignore
            }
        }
    }

    private function makeCourseIdNullable(): void
    {
        try {
            DB::statement('ALTER TABLE `certificates` MODIFY `course_id` BIGINT UNSIGNED NULL');
        } catch (\Throwable) {
            // ignore
        }
    }

    private function nullOrphanCourseIds(): void
    {
        try {
            DB::statement('
                UPDATE `certificates` c
                LEFT JOIN `advanced_courses` ac ON ac.id = c.course_id
                SET c.course_id = NULL
                WHERE c.course_id IS NOT NULL AND ac.id IS NULL
            ');
        } catch (\Throwable) {
            // ignore
        }
    }

    private function addAdvancedCoursesForeignKey(): void
    {
        if ($this->referencesAdvancedCourses()) {
            return;
        }

        DB::statement('
            ALTER TABLE `certificates`
            ADD CONSTRAINT `certificates_course_id_advanced_foreign`
            FOREIGN KEY (`course_id`) REFERENCES `advanced_courses` (`id`)
            ON DELETE SET NULL
        ');
    }
}
