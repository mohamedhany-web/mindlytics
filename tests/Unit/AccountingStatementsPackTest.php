<?php

namespace Tests\Unit;

use App\Support\AccountingAnalytics;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountingStatementsPackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'payments',
            'expenses',
            'withdrawal_requests',
            'wallets',
            'invoices',
            'offline_course_enrollments',
            'installment_payments',
            'orders',
            'accounting_debts',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('gateway_fee_amount', 12, 2)->default(0);
            $table->string('status')->default('completed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->nullable();
            $table->string('title')->nullable();
            $table->string('category')->default('other');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('expense_date');
            $table->string('status')->default('approved');
            $table->string('funding_source')->nullable();
            $table->timestamps();
        });

        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('offline_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function test_horizontal_and_vertical_helpers(): void
    {
        $this->assertSame(93.8, AccountingAnalytics::pctChange(2033495675.0, 3940712296.0));
        $this->assertSame(0.0, AccountingAnalytics::pctChange(0.0, 0.0));
        $this->assertNull(AccountingAnalytics::pctChange(0.0, 100.0));
        $this->assertSame(25.2, AccountingAnalytics::verticalPct(991524404.0, 3940712296.0));
        $this->assertNull(AccountingAnalytics::verticalPct(10.0, 0.0));
    }

    public function test_income_statement_maps_cogs_fees_and_net_income(): void
    {
        $start = Carbon::parse('2024-01-01')->startOfDay();
        $end = Carbon::parse('2024-12-31')->endOfDay();

        DB::table('payments')->insert([
            ['amount' => 10000, 'gateway_fee_amount' => 150, 'status' => 'completed', 'paid_at' => '2024-06-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['amount' => 5000, 'gateway_fee_amount' => 0, 'status' => 'pending', 'paid_at' => '2024-06-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['amount' => 2000, 'gateway_fee_amount' => 20, 'status' => 'completed', 'paid_at' => '2023-06-01 10:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('expenses')->insert([
            ['expense_number' => 'E1', 'title' => 'راتب', 'category' => 'salaries', 'amount' => 2000, 'expense_date' => '2024-03-01', 'status' => 'approved', 'funding_source' => 'revenue', 'created_at' => now(), 'updated_at' => now()],
            ['expense_number' => 'E2', 'title' => 'إعلان', 'category' => 'marketing', 'amount' => 500, 'expense_date' => '2024-03-01', 'status' => 'approved', 'funding_source' => 'out_of_pocket', 'created_at' => now(), 'updated_at' => now()],
            ['expense_number' => 'E3', 'title' => 'كهرباء', 'category' => 'utilities', 'amount' => 300, 'expense_date' => '2024-03-01', 'status' => 'approved', 'funding_source' => 'revenue', 'created_at' => now(), 'updated_at' => now()],
            ['expense_number' => 'E4', 'title' => 'مرفوض', 'category' => 'operational', 'amount' => 999, 'expense_date' => '2024-03-01', 'status' => 'pending', 'funding_source' => 'revenue', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('withdrawal_requests')->insert([
            ['amount' => 800, 'status' => 'completed', 'processed_at' => '2024-04-01 12:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['amount' => 400, 'status' => 'pending', 'processed_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $is = AccountingAnalytics::incomeStatement($start, $end);

        $this->assertSame(10000.0, $is['net_sales']);
        $this->assertSame(150.0, $is['gateway_fees']);
        $this->assertSame(2800.0, $is['cogs']);
        $this->assertSame(7200.0, $is['gross_profit']);
        $this->assertSame(650.0, $is['selling']);
        $this->assertSame(300.0, $is['opex']);
        $this->assertSame(6250.0, $is['operating_profit']);
        $this->assertSame(6250.0, $is['net_income']);
        $this->assertSame(6250.0, $is['ebitda']);
        $this->assertSame(2800.0, $is['expenses_recorded']);

        $cf = AccountingAnalytics::cashFlowStatement($is, $start, $end);
        $this->assertSame(6250.0, $cf['cfo']);
        $this->assertSame(0.0, $cf['cfi']);
        $this->assertSame(500.0, $cf['cff']);
        $this->assertSame(6750.0, $cf['net_change']);
    }

    public function test_founder_funding_is_equity_not_payable(): void
    {
        DB::table('expenses')->insert([
            'expense_number' => 'P1',
            'title' => 'من الجيب',
            'category' => 'operational',
            'amount' => 1500,
            'expense_date' => '2024-01-01',
            'status' => 'approved',
            'funding_source' => AccountingAnalytics::FUNDING_OUT_OF_POCKET,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('withdrawal_requests')->insert([
            'amount' => 200,
            'status' => 'pending',
            'processed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $snap = AccountingAnalytics::receivablesSnapshot();

        $this->assertSame(200.0, $snap['payables']['total']);
        $this->assertSame(0.0, $snap['payables']['founder_injections']);
        $this->assertSame(1500.0, $snap['equity']['founder_capital']);
        $this->assertSame(200.0, $snap['payables']['withdrawals_pending']);
    }

    public function test_statements_pack_compares_equal_length_prior_period(): void
    {
        DB::table('payments')->insert([
            ['amount' => 4000, 'gateway_fee_amount' => 0, 'status' => 'completed', 'paid_at' => '2024-06-15 10:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['amount' => 1000, 'gateway_fee_amount' => 0, 'status' => 'completed', 'paid_at' => '2024-01-15 10:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $start = Carbon::parse('2024-04-01')->startOfDay();
        $end = Carbon::parse('2024-06-30')->endOfDay();
        $pack = AccountingAnalytics::statementsPack($start, $end);

        $this->assertSame('2024-01-01', $pack['prior_period']['start']);
        $this->assertSame(4000.0, $pack['current']['net_sales']);
        $this->assertSame(1000.0, $pack['prior']['net_sales']);
        $this->assertSame(300.0, $pack['income_compare'][0]['change_pct']);
        $this->assertSame(100.0, $pack['income_compare'][0]['vertical_current']);
        $this->assertArrayHasKey('profitability', $pack['ratios']);
        $this->assertArrayHasKey('coverage', $pack['ratios']);
        $this->assertSame(4000.0, $pack['current']['net_income']);
        $this->assertTrue($pack['position']['balances']);
        $this->assertSame($pack['position']['total_assets'], $pack['position']['total_liabilities_and_equity']);
        $this->assertNotEmpty($pack['executive']['purpose']);
        $this->assertCount(6, $pack['recommendations']);
        $this->assertArrayHasKey('cfo', $pack['cash_flow']);
        $dupont = $pack['dupont']['current'];
        $this->assertEqualsWithDelta(
            $dupont['roe'],
            round(($dupont['npm'] / 100) * $dupont['asset_turnover'] * $dupont['equity_multiplier'] * 100, 1),
            0.15
        );
    }
}
