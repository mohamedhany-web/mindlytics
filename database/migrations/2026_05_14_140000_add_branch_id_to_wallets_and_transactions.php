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

        $this->addWallets($defaultBranchId);
        $this->addTransactions($defaultBranchId);
        $this->addWalletTransactions($defaultBranchId);
    }

    public function down(): void
    {
        foreach (['wallet_transactions', 'transactions', 'wallets'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'branch_id')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['branch_id']);
                $blueprint->dropColumn('branch_id');
            });
        }
    }

    private function addWallets(mixed $defaultBranchId): void
    {
        if (!Schema::hasTable('wallets') || Schema::hasColumn('wallets', 'branch_id')) {
            return;
        }

        Schema::table('wallets', function (Blueprint $blueprint) {
            $blueprint->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();
        });

        $this->backfillBranchFromUsers('wallets');
        DB::table('wallets')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    private function addTransactions(mixed $defaultBranchId): void
    {
        if (!Schema::hasTable('transactions') || Schema::hasColumn('transactions', 'branch_id')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $blueprint) {
            $blueprint->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();
        });

        $this->backfillBranchFromUsers('transactions');

        DB::table('transactions')
            ->whereNull('branch_id')
            ->whereNotNull('payment_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $bid = DB::table('payments')->where('id', $row->payment_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table('transactions')->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });

        DB::table('transactions')
            ->whereNull('branch_id')
            ->whereNotNull('invoice_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $bid = DB::table('invoices')->where('id', $row->invoice_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table('transactions')->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });

        DB::table('transactions')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    private function addWalletTransactions(mixed $defaultBranchId): void
    {
        if (!Schema::hasTable('wallet_transactions') || Schema::hasColumn('wallet_transactions', 'branch_id')) {
            return;
        }

        Schema::table('wallet_transactions', function (Blueprint $blueprint) {
            $blueprint->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();
        });

        DB::table('wallet_transactions')
            ->whereNull('branch_id')
            ->whereNotNull('wallet_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $bid = DB::table('wallets')->where('id', $row->wallet_id)->value('branch_id');
                    if ($bid !== null) {
                        DB::table('wallet_transactions')->where('id', $row->id)->update(['branch_id' => $bid]);
                    }
                }
            });

        DB::table('wallet_transactions')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
    }

    private function backfillBranchFromUsers(string $table): void
    {
        if (!Schema::hasColumn($table, 'user_id')) {
            return;
        }

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
};
