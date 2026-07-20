<?php

/**
 * Smoke test: course commission agreements (all calc_modes) + pages.
 * Usage: php scripts/test-sales-course-commission.php
 */

use App\Http\Controllers\Admin\SalesCommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\SalesKpiController;
use App\Http\Controllers\Employee\SalesManagerCommissionController;
use App\Models\AdvancedCourse;
use App\Models\EmployeeJob;
use App\Models\Notification;
use App\Models\OfflineCourse;
use App\Models\SalesCourseCommissionAgreement;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesCommissionTierService;
use App\Services\SalesCourseCommissionResolver;
use App\Services\SalesWinCommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

$marker = 'ccagr_'.Str::lower(Str::random(8));
$salesJob = EmployeeJob::query()->whereRaw('LOWER(code) = ?', ['sales'])->first()
    ?? $fail('no sales job');
$managerJob = EmployeeJob::query()->whereRaw('LOWER(code) = ?', ['sales_manager'])->first();

$advanced = AdvancedCourse::query()->where('is_active', true)->orderByDesc('id')->first();
$offline = OfflineCourse::query()->where('is_active', true)->orderByDesc('id')->first();
if (! $advanced) {
    $fail('need at least one active advanced course');
}
$ok('using advanced course #'.$advanced->id.' price='.$advanced->price);
if ($offline) {
    $ok('using offline course #'.$offline->id.' price='.$offline->price);
}

$admin = User::query()
    ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
    ->first()
    ?? User::query()->orderBy('id')->first()
    ?? $fail('no admin user');

$resolver = app(SalesCourseCommissionResolver::class);
$svc = app(SalesWinCommissionService::class);

$createdUserIds = [];
$leadIds = [];
$agrIds = [];
$teamId = null;

try {
    $rep = User::create([
        'name' => 'CC Agr Rep '.$marker,
        'email' => $marker.'@example.test',
        'phone' => '010'.substr((string) random_int(100000000, 999999999), 0, 8),
        'password' => Hash::make('Password123!'),
        'is_employee' => true,
        'is_active' => true,
        'employee_job_id' => $salesJob->id,
        'sales_commission_mode' => 'fixed',
        'sales_commission_value' => 25,
    ]);
    $createdUserIds[] = $rep->id;
    if (! $rep->isSalesEmployee()) {
        $fail('rep not sales');
    }

    // Agreement: fixed on advanced course
    $agrFixed = new SalesCourseCommissionAgreement([
        'user_id' => $rep->id,
        'calc_mode' => 'fixed',
        'commission_value' => 120,
        'tier_period' => 'month',
        'is_active' => true,
    ]);
    SalesCourseCommissionAgreement::applyCourseSelection($agrFixed, 'advanced', (int) $advanced->id);
    $agrFixed->save();
    $agrIds[] = $agrFixed->id;

    Auth::login($admin);

    $leadFixed = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "CC Fixed {$marker}",
        'phone' => '01100000001',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'course_type' => 'advanced',
        'advanced_course_id' => $advanced->id,
        'expected_value' => (float) $advanced->price,
        'closed_at' => now(),
    ]);
    $leadIds[] = $leadFixed->id;

    $q = $resolver->quoteForLead($rep->fresh(), $leadFixed->fresh());
    if ($q['calc_mode'] !== 'fixed' || abs($q['total'] - 120) > 0.001) {
        $fail('fixed quote: '.json_encode($q));
    }
    $r = $svc->approveAndPayCommission($leadFixed->fresh());
    if (! ($r['success'] ?? false) || abs(($r['commission'] ?? 0) - 120) > 0.001) {
        $fail('fixed approve: '.json_encode($r));
    }
    $ok('fixed agreement pays 120');

    // percent agreement on same course — update mode
    $agrFixed->forceFill(['calc_mode' => 'percent', 'commission_value' => 10])->save();
    $leadPct = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "CC Pct {$marker}",
        'phone' => '01100000002',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'course_type' => 'advanced',
        'advanced_course_id' => $advanced->id,
        'expected_value' => 1000,
        'closed_at' => now(),
    ]);
    $leadIds[] = $leadPct->id;
    $q2 = $resolver->quoteForLead($rep->fresh(), $leadPct->fresh());
    if (abs($q2['total'] - 100) > 0.001) {
        $fail('percent quote expected 100 got '.$q2['total']);
    }
    $r2 = $svc->approveAndPayCommission($leadPct->fresh());
    if (! ($r2['success'] ?? false) || abs(($r2['commission'] ?? 0) - 100) > 0.001) {
        $fail('percent approve: '.json_encode($r2));
    }
    $ok('percent agreement pays 10% of 1000 = 100');

    // tier_course with separate count — switch agreement
    $agrFixed->forceFill([
        'calc_mode' => 'tier_course',
        'commission_value' => null,
        'tiers' => SalesCommissionTierService::defaultTiers(),
        'tier_period' => 'month',
    ])->save();

    // Create 9 wins on this course → next is #10 with bonus
    // We already have 2 confirmed on this course — so next sale numbers start at 3
    $confirmedOnCourse = SalesLead::query()
        ->where('assigned_to', $rep->id)
        ->where('course_type', 'advanced')
        ->where('advanced_course_id', $advanced->id)
        ->whereNotNull('won_confirmed_at')
        ->count();
    $ok("confirmed on course before tier fills: {$confirmedOnCourse}");

    $need = max(0, 9 - $confirmedOnCourse);
    for ($i = 0; $i < $need; $i++) {
        $l = SalesLead::create([
            'assigned_to' => $rep->id,
            'created_by' => $admin->id,
            'name' => "CC TierFill {$marker} {$i}",
            'phone' => '0111'.str_pad((string) $i, 7, '0', STR_PAD_LEFT),
            'stage' => 'won',
            'source' => 'other',
            'priority' => 'normal',
            'course_type' => 'advanced',
            'advanced_course_id' => $advanced->id,
            'expected_value' => 500,
            'closed_at' => now(),
        ]);
        $leadIds[] = $l->id;
        $rr = $svc->approveAndPayCommission($l->fresh());
        if (! ($rr['success'] ?? false)) {
            $fail('tier fill approve #'.$i.': '.($rr['error'] ?? ''));
        }
    }

    $lead10 = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "CC Tier10 {$marker}",
        'phone' => '01100000010',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'course_type' => 'advanced',
        'advanced_course_id' => $advanced->id,
        'expected_value' => 500,
        'closed_at' => now(),
    ]);
    $leadIds[] = $lead10->id;
    $q10 = $resolver->quoteForLead($rep->fresh(), $lead10->fresh());
    if (($q10['sale_number'] ?? 0) !== 10 || abs(($q10['total'] ?? 0) - 560) > 0.001) {
        $fail('tier_course #10 quote: '.json_encode($q10));
    }
    $r10 = $svc->approveAndPayCommission($lead10->fresh());
    if (! ($r10['success'] ?? false) || abs(($r10['commission'] ?? 0) - 60) > 0.001 || abs(($r10['bonus'] ?? 0) - 500) > 0.001) {
        $fail('tier_course #10 pay: '.json_encode($r10));
    }
    $ok('tier_course sale #10 = 60 + 500 bonus');

    // Lead without course → fallback to user fixed 25
    $leadFb = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => "CC Fallback {$marker}",
        'phone' => '01100000099',
        'stage' => 'won',
        'source' => 'other',
        'priority' => 'normal',
        'expected_value' => 800,
        'closed_at' => now(),
    ]);
    $leadIds[] = $leadFb->id;
    $qFb = $resolver->quoteForLead($rep->fresh(), $leadFb->fresh());
    if (($qFb['source'] ?? '') !== 'user' || abs($qFb['total'] - 25) > 0.001) {
        $fail('fallback quote: '.json_encode($qFb));
    }
    $rFb = $svc->approveAndPayCommission($leadFb->fresh());
    if (! ($rFb['success'] ?? false) || abs(($rFb['commission'] ?? 0) - 25) > 0.001) {
        $fail('fallback approve: '.json_encode($rFb));
    }
    $ok('fallback to user fixed 25 without course');

    // price resolve helper
    $price = SalesCourseCommissionResolver::resolveCoursePrice('advanced', (int) $advanced->id);
    if ($price === null) {
        $fail('resolveCoursePrice null');
    }
    $ok('resolveCoursePrice works: '.$price);

    // catalog endpoint data
    $list = SalesCourseCommissionResolver::listCourses('advanced');
    if ($list === []) {
        $fail('listCourses advanced empty');
    }
    $ok('listCourses advanced count='.count($list));

    // Render admin pages
    View::share('errors', new ViewErrorBag);
    $targetsReq = Request::create(route('admin.sales.kpi.targets', absolute: false), 'GET', ['user_id' => $rep->id]);
    $targetsReq->setUserResolver(fn () => $admin);
    $targetsHtml = app(SalesKpiController::class)->targets($targetsReq)->render();
    if (! str_contains($targetsHtml, 'اتفاقيات كوميشن حسب الكورس')) {
        $fail('KPI targets missing agreements section');
    }
    $ok('admin KPI targets shows agreements UI');

    $showReq = Request::create(route('admin.sales.commissions.show', $rep, false), 'GET', ['view' => 'all']);
    $showReq->setUserResolver(fn () => $admin);
    $showHtml = app(AdminCommissionController::class)->show($showReq, $rep->fresh())->render();
    if (! str_contains($showHtml, $lead10->name) && ! str_contains($showHtml, 'CC Tier10')) {
        $fail('admin commissions show missing lead');
    }
    $ok('admin commissions show renders');

    // Sales manager pages if manager job exists
    if ($managerJob) {
        $manager = User::create([
            'name' => 'CC Mgr '.$marker,
            'email' => 'mgr_'.$marker.'@example.test',
            'phone' => '012'.substr((string) random_int(100000000, 999999999), 0, 8),
            'password' => Hash::make('Password123!'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $managerJob->id,
        ]);
        $createdUserIds[] = $manager->id;

        $team = SalesTeam::create([
            'name' => 'CC Team '.$marker,
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);
        $teamId = $team->id;
        SalesTeamMember::create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
        ]);

        Auth::login($manager);
        $idxReq = Request::create(route('employee.sales-manager.commissions.index', absolute: false), 'GET');
        $idxReq->setUserResolver(fn () => $manager);
        $idxHtml = app(SalesManagerCommissionController::class)->index($idxReq)->render();
        if (! str_contains($idxHtml, $rep->name)) {
            $fail('sales manager commissions index missing rep');
        }
        $ok('sales manager commissions index');

        $smShowReq = Request::create(route('employee.sales-manager.commissions.show', $rep, false), 'GET');
        $smShowReq->setUserResolver(fn () => $manager);
        $smShowHtml = app(SalesManagerCommissionController::class)->show($smShowReq, $rep->fresh())->render();
        if (! str_contains($smShowHtml, 'اتفاقيات الكورسات')) {
            $fail('sales manager show missing agreements');
        }
        $ok('sales manager commissions show');
    } else {
        echo "SKIP: no sales_manager job — manager pages not tested\n";
    }

    echo "\nALL CHECKS PASSED\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: '.$e->getMessage()."\n".$e->getTraceAsString()."\n");
    exit(1);
} finally {
    Auth::logout();
    Notification::query()->whereIn('user_id', $createdUserIds)->delete();
    Transaction::query()->whereIn('user_id', $createdUserIds)->where('category', 'commission')->delete();
    SalesLead::withTrashed()->whereIn('id', $leadIds)->forceDelete();
    SalesCourseCommissionAgreement::query()->whereIn('id', $agrIds)->delete();
    if ($teamId) {
        SalesTeamMember::query()->where('sales_team_id', $teamId)->delete();
        SalesTeam::query()->whereKey($teamId)->delete();
    }
    User::query()->whereIn('id', $createdUserIds)->delete();
    echo "OK: cleaned temporary test data\n";
}
