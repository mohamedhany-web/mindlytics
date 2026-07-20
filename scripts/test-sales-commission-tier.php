<?php

/**
 * Smoke test: Sales Commission Tier System (calc + approve + pages).
 * Usage: php scripts/test-sales-commission-tier.php
 *
 * Creates temporary data then deletes it. Safe for local DB.
 */

use App\Http\Controllers\Admin\SalesCommissionController;
use App\Http\Controllers\Admin\SalesKpiController;
use App\Models\EmployeeJob;
use App\Models\Notification;
use App\Models\SalesLead;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesCommissionTierService;
use App\Services\SalesWinCommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fail = function (string $msg) {
    fwrite(STDERR, "FAIL: {$msg}\n");
    exit(1);
};

$ok = function (string $msg) {
    echo "OK: {$msg}\n";
};

// --- 1) Pure calculation checks ---
$userStub = new User;
$userStub->forceFill([
    'sales_commission_mode' => 'tier',
    'sales_commission_tiers' => null,
    'sales_commission_tier_period' => 'month',
]);

$rateCases = [1 => 50, 9 => 50, 10 => 60, 20 => 70, 30 => 80, 40 => 100, 50 => 100];
foreach ($rateCases as $n => $rate) {
    $got = SalesCommissionTierService::rateForSaleNumber($userStub, $n);
    if (abs($got - $rate) > 0.001) {
        $fail("rate for sale #{$n}: expected {$rate}, got {$got}");
    }
}
$bonusCases = [9 => 0, 10 => 500, 20 => 1500, 30 => 3000, 40 => 5000, 41 => 0];
foreach ($bonusCases as $n => $bonus) {
    $got = SalesCommissionTierService::milestoneBonusForSaleNumber($userStub, $n);
    if (abs($got - $bonus) > 0.001) {
        $fail("bonus for sale #{$n}: expected {$bonus}, got {$got}");
    }
}
$ok('tier rate/bonus table matches proposal');

if ($userStub->salesCommissionLabel() !== 'Tier System') {
    $fail('salesCommissionLabel for tier');
}
$ok('User::salesCommissionLabel() for tier');

// --- 2) Integration: approve wins + pages ---
$salesJob = EmployeeJob::query()->whereRaw('LOWER(code) = ?', ['sales'])->first();
if (! $salesJob) {
    $fail('no employee job with code=sales');
}

$admin = User::query()
    ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
    ->first()
    ?? User::query()->orderBy('id')->first();

if (! $admin) {
    $fail('no user found to act as admin');
}

$marker = 'tier_test_'.Str::lower(Str::random(8));
$rep = null;
$leadIds = [];
$txnIds = [];
$notifIds = [];

try {
    $rep = User::create([
        'name' => 'Tier Test Rep '.$marker,
        'email' => $marker.'@example.test',
        'phone' => '010'.substr((string) random_int(100000000, 999999999), 0, 8),
        'password' => Hash::make('Password123!'),
        'is_employee' => true,
        'is_active' => true,
        'employee_job_id' => $salesJob->id,
        'sales_commission_mode' => 'tier',
        'sales_commission_value' => null,
        'sales_commission_tiers' => SalesCommissionTierService::defaultTiers(),
        'sales_commission_tier_period' => 'month',
    ]);

    if (! $rep->isSalesEmployee()) {
        $fail('created user is not detected as sales employee');
    }
    $ok('created temporary sales rep #'.$rep->id);

    Auth::login($admin);

    $svc = app(SalesWinCommissionService::class);

    // Approve 9 wins → each 50, no bonus
    for ($i = 1; $i <= 9; $i++) {
        $lead = SalesLead::create([
            'assigned_to' => $rep->id,
            'created_by' => $admin->id,
            'name' => "Tier Lead {$marker} #{$i}",
            'phone' => '010'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
            'stage' => 'won',
            'source' => 'other',
            'priority' => 'normal',
            'expected_value' => 1000,
            'closed_at' => now(),
        ]);
        $leadIds[] = $lead->id;

        $quote = SalesCommissionTierService::quoteNextWin($rep->fresh());
        if ($quote['sale_number'] !== $i || abs($quote['commission'] - 50) > 0.001 || abs($quote['bonus']) > 0.001) {
            $fail("quote before win #{$i}: ".json_encode($quote));
        }

        $result = $svc->approveAndPayCommission($lead->fresh());
        if (! ($result['success'] ?? false)) {
            $fail("approve win #{$i}: ".($result['error'] ?? 'unknown'));
        }
        if (abs(($result['commission'] ?? 0) - 50) > 0.001 || abs(($result['bonus'] ?? 0)) > 0.001) {
            $fail("pay amounts win #{$i}: ".json_encode($result));
        }

        $lead->refresh();
        if (! $lead->won_confirmed_at || abs((float) $lead->commission_amount - 50) > 0.001) {
            $fail("lead #{$i} commission_amount after approve");
        }
        $txnIds[] = $lead->commission_transaction_id;
    }
    $ok('approved wins 1–9 at 50 EGP each');

    // Win #10 → 60 + bonus 500
    $lead10 = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "Tier Lead {$marker} #10",
        'phone' => '01000000010',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'expected_value' => 1000,
        'closed_at' => now(),
    ]);
    $leadIds[] = $lead10->id;

    $quote10 = SalesCommissionTierService::quoteNextWin($rep->fresh());
    if ($quote10['sale_number'] !== 10 || abs($quote10['total'] - 560) > 0.001) {
        $fail('quote win #10 expected total 560: '.json_encode($quote10));
    }

    $result10 = $svc->approveAndPayCommission($lead10->fresh());
    if (! ($result10['success'] ?? false)) {
        $fail('approve win #10: '.($result10['error'] ?? 'unknown'));
    }
    if (abs(($result10['commission'] ?? 0) - 60) > 0.001 || abs(($result10['bonus'] ?? 0) - 500) > 0.001) {
        $fail('pay amounts win #10: '.json_encode($result10));
    }

    $lead10->refresh();
    if (abs((float) $lead10->commission_amount - 560) > 0.001) {
        $fail('lead #10 commission_amount should be 560, got '.$lead10->commission_amount);
    }
    $txnIds[] = $lead10->commission_transaction_id;

    $bonusTxn = Transaction::query()
        ->where('user_id', $rep->id)
        ->where('category', 'commission')
        ->where('metadata->kind', 'tier_milestone_bonus')
        ->where('metadata->sales_lead_id', $lead10->id)
        ->first();
    if (! $bonusTxn || abs((float) $bonusTxn->amount - 500) > 0.001) {
        $fail('milestone bonus transaction missing or wrong amount');
    }
    $txnIds[] = $bonusTxn->id;
    $ok('win #10 paid 60 + 500 milestone bonus');

    // Double-approve should fail
    $dup = $svc->approveAndPayCommission($lead10->fresh());
    if ($dup['success'] ?? true) {
        $fail('double approve should fail');
    }
    $ok('double-approve correctly rejected');

    // Breakdown
    $confirmed = SalesLead::query()
        ->whereIn('id', $leadIds)
        ->orderBy('won_confirmed_at')
        ->orderBy('id')
        ->get();
    $breakdown = SalesCommissionTierService::buildBreakdown($rep->fresh(), $confirmed);
    if ($breakdown['wins'] !== 10) {
        $fail('breakdown wins='.$breakdown['wins']);
    }
    if (abs($breakdown['progressive_commission'] - 510) > 0.001) {
        $fail('progressive_commission expected 510 got '.$breakdown['progressive_commission']);
    }
    if (abs($breakdown['milestones_bonus'] - 500) > 0.001) {
        $fail('milestones_bonus expected 500 got '.$breakdown['milestones_bonus']);
    }
    if (abs($breakdown['progressive_total'] - 1010) > 0.001) {
        $fail('progressive_total expected 1010 got '.$breakdown['progressive_total']);
    }
    $ok('buildBreakdown totals: 510 + 500 = 1010');

    // defaultCommissionForLead for next (11) = 60
    $pendingLead = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "Tier Lead {$marker} pending",
        'phone' => '01000000011',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'expected_value' => 1000,
        'closed_at' => now(),
    ]);
    $leadIds[] = $pendingLead->id;
    $est = SalesWinCommissionService::defaultCommissionForLead($pendingLead->fresh());
    if (abs($est - 60) > 0.001) {
        $fail("defaultCommissionForLead next win expected 60 got {$est}");
    }
    $ok('pending next-win estimate = 60');

    // Controller pages render
    View::share('errors', new ViewErrorBag);
    app('view')->share('errors', new ViewErrorBag);

    $showReq = Request::create(route('admin.sales.commissions.show', $rep, false), 'GET', ['view' => 'all']);
    $showReq->setUserResolver(fn () => $admin);
    $showResp = app(SalesCommissionController::class)->show($showReq, $rep->fresh());
    $showHtml = $showResp->render();
    if (! str_contains($showHtml, 'حسابات Tier System')) {
        $fail('commissions show missing Tier System section');
    }
    if (! str_contains($showHtml, '500')) {
        $fail('commissions show missing milestone amount');
    }
    $ok('admin commissions show renders tier breakdown');

    $indexReq = Request::create(route('admin.sales.commissions.index', absolute: false), 'GET', ['view' => 'month']);
    $indexReq->setUserResolver(fn () => $admin);
    $indexHtml = app(SalesCommissionController::class)->index($indexReq)->render();
    if (! str_contains($indexHtml, $rep->name)) {
        $fail('commissions index missing test rep');
    }
    $ok('admin commissions index lists rep');

    $targetsReq = Request::create(route('admin.sales.kpi.targets', absolute: false), 'GET', ['user_id' => $rep->id]);
    $targetsReq->setUserResolver(fn () => $admin);
    $targetsHtml = app(SalesKpiController::class)->targets($targetsReq)->render();
    if (! str_contains($targetsHtml, 'Tier System')) {
        $fail('KPI targets missing Tier System option');
    }
    if (! str_contains($targetsHtml, 'tier_min')) {
        $fail('KPI targets missing tier table inputs');
    }
    $ok('KPI targets UI includes tier config');

    // Save tier via updateTargets
    $updateReq = Request::create(route('admin.sales.kpi.targets.update', absolute: false), 'PUT', [
        'user_id' => $rep->id,
        'year_month' => now()->format('Y-m'),
        'sales_commission_mode' => 'tier',
        'sales_commission_tier_period' => 'all',
        'tier_min' => [1, 10, 20, 30, 40],
        'tier_max' => [9, 19, 29, 39, ''],
        'tier_rate' => [50, 60, 70, 80, 100],
        'tier_bonus' => [0, 500, 1500, 3000, 5000],
        'tier_bonus_at' => ['', 10, 20, 30, 40],
        'leads_daily' => 5,
    ]);
    $updateReq->setUserResolver(fn () => $admin);
    Auth::login($admin);
    $updateResp = app(SalesKpiController::class)->updateTargets($updateReq);
    $rep->refresh();
    if ($rep->sales_commission_mode !== 'tier') {
        $fail('updateTargets did not save mode=tier');
    }
    if ($rep->sales_commission_tier_period !== 'all') {
        $fail('updateTargets did not save period=all');
    }
    if (! is_array($rep->sales_commission_tiers) || count($rep->sales_commission_tiers) < 5) {
        $fail('updateTargets did not save tiers JSON');
    }
    $ok('KPI updateTargets persists tier config');

    // Enum accepts tier
    $modeCol = DB::selectOne("SHOW COLUMNS FROM users LIKE 'sales_commission_mode'");
    $type = (string) ($modeCol->Type ?? '');
    if (str_contains($type, 'enum') && ! str_contains($type, 'tier')) {
        $fail("sales_commission_mode enum missing tier: {$type}");
    }
    $ok('DB enum/column supports tier mode');

    echo "\nALL CHECKS PASSED\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: exception '.$e->getMessage()."\n".$e->getTraceAsString()."\n");
    exit(1);
} finally {
    Auth::logout();

    if ($rep) {
        $notifIds = Notification::query()
            ->where('user_id', $rep->id)
            ->pluck('id')
            ->all();
        Notification::query()->whereIn('id', $notifIds)->delete();

        Transaction::query()->where('user_id', $rep->id)->whereIn('id', array_filter($txnIds))->delete();
        Transaction::query()
            ->where('user_id', $rep->id)
            ->where('category', 'commission')
            ->where(function ($q) use ($marker) {
                $q->where('description', 'like', '%'.$marker.'%')
                    ->orWhere('metadata->kind', 'tier_milestone_bonus');
            })
            ->delete();

        // Soft-delete then force wipe test leads
        SalesLead::withTrashed()->whereIn('id', $leadIds)->forceDelete();
        SalesLead::withTrashed()->where('name', 'like', '%'.$marker.'%')->forceDelete();

        $rep->delete();
        echo "OK: cleaned temporary test data\n";
    }
}
