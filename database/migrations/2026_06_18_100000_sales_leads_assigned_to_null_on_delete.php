<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_leads') || ! Schema::hasColumn('sales_leads', 'assigned_to')) {
            return;
        }

        $this->dropAssignedToForeignKey();

        DB::statement('ALTER TABLE sales_leads MODIFY assigned_to BIGINT UNSIGNED NULL');

        Schema::table('sales_leads', function (Blueprint $table) {
            $table->foreign('assigned_to', 'sales_leads_assigned_to_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_leads') || ! Schema::hasColumn('sales_leads', 'assigned_to')) {
            return;
        }

        $this->dropAssignedToForeignKey();

        DB::table('sales_leads')->whereNull('assigned_to')->delete();

        DB::statement('ALTER TABLE sales_leads MODIFY assigned_to BIGINT UNSIGNED NOT NULL');

        Schema::table('sales_leads', function (Blueprint $table) {
            $table->foreign('assigned_to', 'sales_leads_assigned_to_foreign')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    private function dropAssignedToForeignKey(): void
    {
        $conn = Schema::getConnection();
        $db = $conn->getDatabaseName();

        $rows = $conn->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$db, 'sales_leads', 'assigned_to']
        );

        foreach ($rows as $row) {
            Schema::table('sales_leads', function (Blueprint $table) use ($row) {
                $table->dropForeign($row->CONSTRAINT_NAME);
            });
        }
    }
};
