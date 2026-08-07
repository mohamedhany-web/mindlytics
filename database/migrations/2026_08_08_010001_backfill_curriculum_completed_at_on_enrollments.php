<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('student_course_enrollments', 'curriculum_completed_at')) {
            return;
        }

        DB::table('student_course_enrollments')
            ->whereNull('curriculum_completed_at')
            ->where('progress', '>=', 100)
            ->update(['curriculum_completed_at' => now()]);
    }

    public function down(): void
    {
        // لا نمسح التواريخ عند الرجوع — قد تكون صحيحة من التعلّم الفعلي
    }
};
