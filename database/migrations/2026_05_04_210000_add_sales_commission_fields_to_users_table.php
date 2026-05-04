<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'sales_commission_mode')) {
                $table->enum('sales_commission_mode', ['none', 'percent', 'fixed'])->default('none')->after('employee_notes');
            }
            if (! Schema::hasColumn('users', 'sales_commission_value')) {
                $table->decimal('sales_commission_value', 10, 2)->nullable()->after('sales_commission_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sales_commission_value')) {
                $table->dropColumn('sales_commission_value');
            }
            if (Schema::hasColumn('users', 'sales_commission_mode')) {
                $table->dropColumn('sales_commission_mode');
            }
        });
    }
};

