<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_leads')) {
            return;
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'age_range')) {
                $table->string('age_range', 32)->nullable()->after('age');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_leads') || ! Schema::hasColumn('sales_leads', 'age_range')) {
            return;
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            $table->dropColumn('age_range');
        });
    }
};
