<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'sales_commission_mode')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE users MODIFY sales_commission_mode ENUM('none','percent','fixed','tier') NOT NULL DEFAULT 'none'");
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'sales_commission_tiers')) {
                $table->json('sales_commission_tiers')->nullable()->after('sales_commission_value');
            }
            if (! Schema::hasColumn('users', 'sales_commission_tier_period')) {
                $table->string('sales_commission_tier_period', 16)->default('month')->after('sales_commission_tiers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sales_commission_tier_period')) {
                $table->dropColumn('sales_commission_tier_period');
            }
            if (Schema::hasColumn('users', 'sales_commission_tiers')) {
                $table->dropColumn('sales_commission_tiers');
            }
        });

        if (Schema::hasColumn('users', 'sales_commission_mode')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE users MODIFY sales_commission_mode ENUM('none','percent','fixed') NOT NULL DEFAULT 'none'");
            }
        }
    }
};
