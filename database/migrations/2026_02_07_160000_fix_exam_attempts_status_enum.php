<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة 'completed' إلى ENUM status في exam_attempts لأن التطبيق يستخدمها.
     */
    public function up(): void
    {
        $tableName = 'exam_attempts';
        if (!Schema::hasTable($tableName)) {
            return;
        }

        // MySQL: تعديل العمود ليقبل 'completed' أيضاً
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN status ENUM('in_progress', 'submitted', 'auto_submitted', 'completed') NOT NULL DEFAULT 'in_progress'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = 'exam_attempts';
        if (!Schema::hasTable($tableName)) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            // تحويل المحاولات المكتملة إلى submitted قبل الرجوع
            DB::table($tableName)->where('status', 'completed')->update(['status' => 'submitted']);
            DB::statement("ALTER TABLE {$tableName} MODIFY COLUMN status ENUM('in_progress', 'submitted', 'auto_submitted') NOT NULL DEFAULT 'in_progress'");
        }
    }
};
