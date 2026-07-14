<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_lead_groups') || ! Schema::hasColumn('sales_lead_groups', 'assigned_to')) {
            return;
        }

        Schema::table('sales_lead_groups', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `sales_lead_groups` MODIFY `assigned_to` BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sales_lead_groups ALTER COLUMN assigned_to DROP NOT NULL');
        } else {
            // sqlite / others: rebuild via doctrine-less nullable recreation is limited; skip if already null-capable
            Schema::table('sales_lead_groups', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_to')->nullable()->change();
            });
        }

        Schema::table('sales_lead_groups', function (Blueprint $table) {
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_lead_groups') || ! Schema::hasColumn('sales_lead_groups', 'assigned_to')) {
            return;
        }

        // لا نعيد الإلزام إن وُجدت صفوف بلا assigned_to
        $orphans = DB::table('sales_lead_groups')->whereNull('assigned_to')->count();
        if ($orphans > 0) {
            return;
        }

        Schema::table('sales_lead_groups', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `sales_lead_groups` MODIFY `assigned_to` BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE sales_lead_groups ALTER COLUMN assigned_to SET NOT NULL');
        }

        Schema::table('sales_lead_groups', function (Blueprint $table) {
            $table->foreign('assigned_to')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
