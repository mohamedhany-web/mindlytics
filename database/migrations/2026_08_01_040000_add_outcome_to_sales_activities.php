<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_activities')) {
            return;
        }

        Schema::table('sales_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_activities', 'outcome')) {
                $table->string('outcome', 32)->nullable()->after('type');
                $table->index(['type', 'outcome']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_activities')) {
            return;
        }

        Schema::table('sales_activities', function (Blueprint $table) {
            if (Schema::hasColumn('sales_activities', 'outcome')) {
                $table->dropIndex(['type', 'outcome']);
                $table->dropColumn('outcome');
            }
        });
    }
};
