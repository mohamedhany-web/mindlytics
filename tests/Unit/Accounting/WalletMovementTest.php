<?php

namespace Tests\Unit\Accounting;

use App\Domains\Accounting\Models\Wallet;
use App\Domains\Accounting\Models\WalletTransaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * حركات المحفظة: الرصيد والحركة معاً أو لا شيء، والفحص على الرصيد الحالي (docs/ACCOUNTING_AUDIT.md → M1).
 * الجداول مبسّطة لأن بعض الـ migrations خاصة بـ MySQL.
 */
class WalletMovementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->default('vodafone_cash');
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->nullable();
            $table->decimal('balance_after', 12, 2);
            $table->string('description')->nullable();
            $table->string('status')->default('completed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function test_deposit_updates_balance_and_records_before_and_after(): void
    {
        $wallet = $this->wallet(100);

        $movement = $wallet->deposit(50, 7, null, 'order #1');

        $this->assertSame(150.0, (float) $wallet->fresh()->balance);
        $this->assertSame(150.0, (float) $wallet->balance, 'in-memory balance follows the database');
        $this->assertSame('deposit', $movement->type);
        $this->assertSame(100.0, (float) $movement->balance_before);
        $this->assertSame(150.0, (float) $movement->balance_after);
        $this->assertSame(7, (int) $movement->payment_id);
        $this->assertSame('order #1', $movement->description);
    }

    public function test_withdraw_and_refund_reduce_balance(): void
    {
        $wallet = $this->wallet(100);

        $wallet->withdraw(30, 'expense');
        $refund = $wallet->refund(20, 3, 4, 'refund', 99);

        $this->assertSame(50.0, (float) $wallet->fresh()->balance);
        $this->assertSame(['withdrawal', 'refund'], WalletTransaction::orderBy('id')->pluck('type')->all());
        $this->assertSame(99, (int) $refund->created_by);
        $this->assertSame(4, (int) $refund->transaction_id);
    }

    public function test_withdraw_more_than_balance_fails_without_side_effects(): void
    {
        $wallet = $this->wallet(40);

        try {
            $wallet->withdraw(50, 'too much');
            $this->fail('expected insufficient balance');
        } catch (\Exception $e) {
            $this->assertSame('رصيد المحفظة غير كافي', $e->getMessage());
        }

        $this->assertSame(40.0, (float) $wallet->fresh()->balance);
        $this->assertSame(0, WalletTransaction::count());
    }

    public function test_withdraw_checks_current_database_balance_not_a_stale_copy(): void
    {
        $wallet = $this->wallet(100);
        $staleCopy = Wallet::find($wallet->id);

        $wallet->withdraw(70, 'first');

        // النسخة القديمة ما زالت ترى 100؛ كان السحب يمر ويصبح الرصيد -50
        $this->expectExceptionMessage('رصيد المحفظة غير كافي');
        try {
            $staleCopy->withdraw(80, 'second');
        } finally {
            $this->assertSame(30.0, (float) $wallet->fresh()->balance);
            $this->assertSame(1, WalletTransaction::count());
        }
    }

    public function test_balance_change_is_undone_when_the_movement_cannot_be_recorded(): void
    {
        $wallet = $this->wallet(100);
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('required_without_default');
        });

        try {
            $wallet->deposit(50, null, null, 'will fail');
            $this->fail('expected the movement insert to fail');
        } catch (\Illuminate\Database\QueryException) {
        }

        $this->assertSame(100.0, (float) $wallet->fresh()->balance);
        $this->assertSame(0, WalletTransaction::count());
    }

    public function test_movement_inside_an_outer_transaction_rolls_back_with_it(): void
    {
        $wallet = $this->wallet(100);

        DB::beginTransaction();
        $wallet->deposit(25);
        DB::rollBack();

        $this->assertSame(100.0, (float) $wallet->fresh()->balance);
        $this->assertSame(0, WalletTransaction::count());
    }

    private function wallet(float $balance): Wallet
    {
        return Wallet::create(['branch_id' => 1, 'name' => 'Test wallet', 'type' => 'vodafone_cash', 'balance' => $balance, 'is_active' => true]);
    }
}
