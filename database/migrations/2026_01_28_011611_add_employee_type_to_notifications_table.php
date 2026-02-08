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
        // إضافة 'employee' إلى enum type في جدول notifications
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee') DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة 'employee' من enum type (إرجاع للحالة السابقة)
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system') DEFAULT 'general'");
    }
};
