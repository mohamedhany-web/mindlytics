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
        // حذف الـ foreign key constraint القديم إذا كان موجوداً
        try {
            DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_ibfk_1');
        } catch (\Exception $e) {
            // الـ constraint غير موجود
        }

        // إضافة الـ foreign key constraint الصحيح
        try {
            DB::statement('ALTER TABLE certificates ADD CONSTRAINT certificates_course_id_foreign FOREIGN KEY (course_id) REFERENCES advanced_courses(id) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // الـ constraint موجود بالفعل أو هناك مشكلة أخرى
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE certificates DROP FOREIGN KEY certificates_course_id_foreign');
        } catch (\Exception $e) {
            // الـ constraint غير موجود
        }
    }
};
