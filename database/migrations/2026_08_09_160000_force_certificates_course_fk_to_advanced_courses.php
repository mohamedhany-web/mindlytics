<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Force certificates.course_id to reference advanced_courses (not legacy courses).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates') || ! Schema::hasColumn('certificates', 'course_id')) {
            return;
        }

        app(\App\Support\CertificateCourseForeignKey::class)->fix();
    }

    public function down(): void
    {
        // keep advanced_courses FK
    }
};
