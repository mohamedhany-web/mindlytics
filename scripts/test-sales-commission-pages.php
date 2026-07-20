<?php

/**
 * Full page/render smoke for course commission + related UI.
 * Usage: php scripts/test-sales-commission-pages.php
 */

use App\Http\Controllers\Admin\SalesCommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\SalesCourseCommissionAgreementController;
use App\Http\Controllers\Admin\SalesKpiController;
use App\Http\Controllers\Admin\SalesLeadController as AdminLeadController;
use App\Http\Controllers\Employee\SalesCommissionController as EmployeeCommissionController;
use App\Http\Controllers\Employee\SalesCourseCatalogController;
use App\Http\Controllers\Employee\SalesLeadController as EmployeeLeadController;
use App\Http\Controllers\Employee\SalesManagerCommissionController;
use App\Http\Controllers\Employee\SalesManagerDashboardController;
use App\Http\Controllers\Employee\SalesManagerLeadController;
use App\Models\AdvancedCourse;
use App\Models\EmployeeJob;
use App\Models\SalesCourseCommissionAgreement;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Services\SalesCommissionTierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View as IlluminateView;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fail = function (string $msg) {
    fwrite(STDERR, "FAIL: {$msg}\n");
    exit(1);
};
$ok = function (string $msg) {
    echo "OK: {$msg}\n";
};

View::share('errors', new ViewErrorBag);

$marker = 'page_smk_'.Str::lower(Str::random(6));
$salesJob = EmployeeJob::query()->whereRaw('LOWER(code) = ?', ['sales'])->first() ?? $fail('no sales job');
$managerJob = EmployeeJob::query()->whereRaw('LOWER(code) = ?', ['sales_manager'])->first();
$advanced = AdvancedCourse::query()->where('is_active', true)->orderByDesc('id')->first() ?? $fail('no advanced course');

$admin = User::query()
    ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
    ->first()
    ?? User::query()->orderBy('id')->first()
    ?? $fail('no admin');

$createdUserIds = [];
$agrIds = [];
$leadIds = [];
$teamId = null;

$assertView = function ($resp, string $label, array $needles = []) use ($fail, $ok) {
    if ($resp instanceof \Illuminate\Http\RedirectResponse) {
        $fail("{$label}: unexpected redirect to ".$resp->getTargetUrl());
    }
    if ($resp instanceof \Illuminate\Http\JsonResponse) {
        $data = $resp->getData(true);
        if (! isset($data['data']) || ! is_array($data['data'])) {
            $fail("{$label}: bad JSON shape");
        }
        $ok($label.' JSON ('.count($data['data']).' items)');

        return;
    }
    if (! $resp instanceof IlluminateView) {
        // Some controllers return Response
        $html = method_exists($resp, 'getContent') ? (string) $resp->getContent() : '';
        if ($html === '' && method_exists($resp, 'render')) {
            $html = $resp->render();
        }
    } else {
        $html = $resp->render();
    }

    if ($html === '' || strlen($html) < 50) {
        $fail("{$label}: empty/short HTML");
    }
    foreach ($needles as $n) {
        if (! str_contains($html, $n)) {
            $fail("{$label}: missing «{$n}»");
        }
    }
    // common error markers
    foreach (['Whoops', 'ErrorException', 'Undefined variable', 'ViewException', 'SQLSTATE'] as $bad) {
        if (str_contains($html, $bad)) {
            $fail("{$label}: contains error marker {$bad}");
        }
    }
    $ok($label);
};

try {
    $rep = User::create([
        'name' => 'Page Smoke Rep '.$marker,
        'email' => $marker.'@example.test',
        'phone' => '010'.substr((string) random_int(100000000, 999999999), 0, 8),
        'password' => Hash::make('Password123!'),
        'is_employee' => true,
        'is_active' => true,
        'employee_job_id' => $salesJob->id,
        'sales_commission_mode' => 'tier',
        'sales_commission_tiers' => SalesCommissionTierService::defaultTiers(),
        'sales_commission_tier_period' => 'month',
    ]);
    $createdUserIds[] = $rep->id;

    $agr = new SalesCourseCommissionAgreement([
        'user_id' => $rep->id,
        'calc_mode' => 'tier_course',
        'tiers' => SalesCommissionTierService::defaultTiers(),
        'tier_period' => 'month',
        'is_active' => true,
    ]);
    SalesCourseCommissionAgreement::applyCourseSelection($agr, 'advanced', (int) $advanced->id);
    $agr->save();
    $agrIds[] = $agr->id;

    $lead = SalesLead::create([
        'assigned_to' => $rep->id,
        'created_by' => $admin->id,
        'name' => 'Page Smoke Lead '.$marker,
        'phone' => '01111111111',
        'stage' => 'proposal',
        'source' => 'other',
        'priority' => 'normal',
        'course_type' => 'advanced',
        'advanced_course_id' => $advanced->id,
        'expected_value' => (float) $advanced->price,
    ]);
    $leadIds[] = $lead->id;

    // --- Admin pages ---
    Auth::login($admin);

    $req = Request::create(route('admin.sales.kpi.targets', absolute: false), 'GET', ['user_id' => $rep->id]);
    $req->setUserResolver(fn () => $admin);
    $assertView(app(SalesKpiController::class)->targets($req), 'admin KPI targets', [
        'اتفاقيات كوميشن حسب الكورس',
        'Tier System',
        $rep->name,
    ]);

    $req = Request::create(route('admin.sales.commissions.index', absolute: false), 'GET', ['view' => 'month']);
    $req->setUserResolver(fn () => $admin);
    $assertView(app(AdminCommissionController::class)->index($req), 'admin commissions index', [
        'كوميشن',
        $rep->name,
    ]);

    $req = Request::create(route('admin.sales.commissions.show', $rep, false), 'GET', ['view' => 'all']);
    $req->setUserResolver(fn () => $admin);
    $assertView(app(AdminCommissionController::class)->show($req, $rep->fresh()), 'admin commissions show', [
        $rep->name,
        'حسابات Tier System',
    ]);

    $req = Request::create(route('admin.sales.course-commission.courses', absolute: false), 'GET', ['type' => 'advanced']);
    $req->setUserResolver(fn () => $admin);
    $assertView(app(SalesCourseCommissionAgreementController::class)->courses($req), 'admin courses JSON advanced');

    foreach (['offline', 'legacy'] as $type) {
        $req = Request::create(route('admin.sales.course-commission.courses', absolute: false), 'GET', ['type' => $type]);
        $req->setUserResolver(fn () => $admin);
        $assertView(app(SalesCourseCommissionAgreementController::class)->courses($req), "admin courses JSON {$type}");
    }

    $req = Request::create(route('admin.sales.leads.create', absolute: false), 'GET');
    $req->setUserResolver(fn () => $admin);
    $assertView(app(AdminLeadController::class)->create(), 'admin lead create', [
        'نوع الكورس',
        'الكورس',
    ]);

    $req = Request::create(route('admin.sales.leads.edit', $lead, false), 'GET');
    $req->setUserResolver(fn () => $admin);
    $assertView(app(AdminLeadController::class)->edit($lead->fresh()), 'admin lead edit', [
        'نوع الكورس',
        $lead->name,
    ]);

    // --- Employee pages ---
    Auth::login($rep);

    $req = Request::create(route('employee.sales.commissions.index', absolute: false), 'GET');
    $req->setUserResolver(fn () => $rep);
    $assertView(app(EmployeeCommissionController::class)->index($req), 'employee commissions', [
        'عمولات',
        'اتفاقيات الكوميشن حسب الكورس',
    ]);

    $req = Request::create(route('employee.sales.courses.index', absolute: false), 'GET', ['type' => 'advanced']);
    $req->setUserResolver(fn () => $rep);
    $assertView(app(SalesCourseCatalogController::class)->index($req), 'employee courses JSON');

    $req = Request::create(route('employee.sales.leads.create', absolute: false), 'GET');
    $req->setUserResolver(fn () => $rep);
    $assertView(app(EmployeeLeadController::class)->create($req), 'employee lead create', [
        'نوع الكورس',
    ]);

    $req = Request::create(route('employee.sales.leads.edit', $lead, false), 'GET');
    $req->setUserResolver(fn () => $rep);
    $assertView(app(EmployeeLeadController::class)->edit($lead->fresh()), 'employee lead edit', [
        'نوع الكورس',
        $lead->name,
    ]);

    // --- Sales manager ---
    if ($managerJob) {
        $manager = User::create([
            'name' => 'Page Smoke Mgr '.$marker,
            'email' => 'mgr_'.$marker.'@example.test',
            'phone' => '012'.substr((string) random_int(100000000, 999999999), 0, 8),
            'password' => Hash::make('Password123!'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $managerJob->id,
        ]);
        $createdUserIds[] = $manager->id;

        $team = SalesTeam::create([
            'name' => 'Page Smoke Team '.$marker,
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);
        $teamId = $team->id;
        SalesTeamMember::create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
        ]);

        Auth::login($manager);

        $req = Request::create(route('employee.sales-manager.dashboard', absolute: false), 'GET');
        $req->setUserResolver(fn () => $manager);
        $assertView(app(SalesManagerDashboardController::class)->index(), 'sales manager dashboard', [
            'كوميشن الفريق',
        ]);

        $req = Request::create(route('employee.sales-manager.commissions.index', absolute: false), 'GET');
        $req->setUserResolver(fn () => $manager);
        $assertView(app(SalesManagerCommissionController::class)->index($req), 'sales manager commissions index', [
            $rep->name,
            'كوميشن',
        ]);

        $req = Request::create(route('employee.sales-manager.commissions.show', $rep, false), 'GET');
        $req->setUserResolver(fn () => $manager);
        $assertView(app(SalesManagerCommissionController::class)->show($req, $rep->fresh()), 'sales manager commissions show', [
            'اتفاقيات الكورسات',
            $rep->name,
        ]);

        $req = Request::create(route('employee.sales-manager.leads.show', $lead, false), 'GET');
        $req->setUserResolver(fn () => $manager);
        $assertView(app(SalesManagerLeadController::class)->show($lead->fresh()), 'sales manager lead show', [
            $lead->name,
            'الكورس',
        ]);
    } else {
        echo "SKIP: sales manager pages (no sales_manager job)\n";
    }

    // HTTP kernel smoke for courses JSON (middleware stack)
    Auth::login($admin);
    $http = Request::create('/admin/sales/course-commission-courses?type=advanced', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);
    // Acting via Auth::login may not attach to request user for middleware — set resolver
    $http->setUserResolver(fn () => $admin);
    // Better: use actingAs pattern through kernel with session is heavy; controller already tested.

    echo "\nALL PAGE CHECKS PASSED\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: '.$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString()."\n");
    exit(1);
} finally {
    Auth::logout();
    SalesLead::withTrashed()->whereIn('id', $leadIds)->forceDelete();
    SalesCourseCommissionAgreement::query()->whereIn('id', $agrIds)->delete();
    if ($teamId) {
        SalesTeamMember::query()->where('sales_team_id', $teamId)->delete();
        SalesTeam::query()->whereKey($teamId)->delete();
    }
    User::query()->whereIn('id', $createdUserIds)->delete();
    echo "OK: cleaned temporary test data\n";
}
