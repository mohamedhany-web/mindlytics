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
        // إضافة 'learning_path_payment' إلى enum category في جدول transactions
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `category` ENUM('course_payment', 'subscription', 'refund', 'commission', 'fee', 'learning_path_payment', 'other') DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة 'learning_path_payment' من enum category (إرجاع للحالة السابقة)
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `category` ENUM('course_payment', 'subscription', 'refund', 'commission', 'fee', 'other') DEFAULT 'other'");
    }
};
