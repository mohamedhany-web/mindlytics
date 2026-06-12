<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches')) {
            return;
        }

        $defaultBranchId = DB::table('branches')->whereNull('deleted_at')->orderBy('id')->value('id');
        if (!$defaultBranchId) {
            return;
        }

        $this->addColumn('orders', $defaultBranchId);
        $this->addColumn('student_course_enrollments', $defaultBranchId);
        $this->addColumn('invoices', $defaultBranchId);
        $this->addColumn('payments', $defaultBranchId);
    }

    public function down(): void
    {
        foreach (['payments', 'invoices', 'student_course_enrollments', 'orders'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'branch_id')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['branch_id']);
                $blueprint->dropColumn('branch_id');
            });
        }
    }

    private function addColumn(string $table, mixed $defaultBranchId): void
    {
        if (!Schema::hasTable($table) || Schema::hasColumn($table, 'branch_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();
        });

        if (Schema::hasColumn($table, 'user_id')) {
            $this->backfillBranchFromUsers($table);
        }

        if ($table === 'payments') {
            $this->backfillPaymentsFromInvoices();
            $this->backfillPaymentsFromOrders();
        }

        DB::table($table)->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    private function backfillBranchFromUsers(string $table): void
    {
        DB::table($table)
            ->whereNull('branch_id')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    $bid = DB::table('users')->where('id', $row->user_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table($table)->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });
    }

    private function backfillPaymentsFromInvoices(): void
    {
        DB::table('payments')
            ->whereNull('branch_id')
            ->whereNotNull('invoice_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $bid = DB::table('invoices')->where('id', $row->invoice_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table('payments')->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });
    }

    private function backfillPaymentsFromOrders(): void
    {
        DB::table('payments')
            ->whereNull('branch_id')
            ->whereNotNull('order_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $bid = DB::table('orders')->where('id', $row->order_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table('payments')->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });
    }
};
