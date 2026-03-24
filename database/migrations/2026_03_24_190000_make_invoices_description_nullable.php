<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * السماح بقيمة NULL لوصف الفاتورة (الوسيط ConvertEmptyStringsToNull وواجهات بدون وصف).
     */
    public function up(): void
    {
        if (! Schema::hasTable('invoices') || ! Schema::hasColumn('invoices', 'description')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE `invoices` MODIFY `description` VARCHAR(1000) NULL');
        } catch (\Throwable $e) {
            // احتياطي لطول العمود الأصلي 255
            DB::statement('ALTER TABLE `invoices` MODIFY `description` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices') || ! Schema::hasColumn('invoices', 'description')) {
            return;
        }

        DB::table('invoices')->whereNull('description')->update(['description' => '']);

        try {
            DB::statement('ALTER TABLE `invoices` MODIFY `description` VARCHAR(1000) NOT NULL');
        } catch (\Throwable $e) {
            DB::statement('ALTER TABLE `invoices` MODIFY `description` VARCHAR(255) NOT NULL');
        }
    }
};
