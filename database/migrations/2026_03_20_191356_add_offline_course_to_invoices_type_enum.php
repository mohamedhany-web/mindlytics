<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `invoices` MODIFY COLUMN `type` ENUM('course', 'subscription', 'membership', 'learning_path', 'offline_course', 'other') DEFAULT 'course'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `invoices` MODIFY COLUMN `type` ENUM('course', 'subscription', 'membership', 'learning_path', 'other') DEFAULT 'course'");
    }
};
