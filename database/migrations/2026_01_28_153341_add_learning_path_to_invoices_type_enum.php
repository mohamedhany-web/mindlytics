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
        // إضافة 'learning_path' إلى enum type في جدول invoices
        DB::statement("ALTER TABLE `invoices` MODIFY COLUMN `type` ENUM('course', 'subscription', 'membership', 'learning_path', 'other') DEFAULT 'course'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة 'learning_path' من enum type (إرجاع للحالة السابقة)
        DB::statement("ALTER TABLE `invoices` MODIFY COLUMN `type` ENUM('course', 'subscription', 'membership', 'other') DEFAULT 'course'");
    }
};
