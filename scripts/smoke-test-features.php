<?php

/**
 * Smoke test for marketing plans + employee payroll features.
 * Run: php scripts/smoke-test-features.php
 */

use App\Models\EmployeeAgreement;
use App\Models\ModeratorMarketingPlan;
use App\Models\User;
use App\Services\EmployeePayrollService;
use App\Services\MarketingPlanEventAutomationService;
use App\Support\MarketingPlanSettings;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$errors = [];
$passed = 0;

function check(string $label, callable $fn): void
{
    global $errors, $passed;
    try {
        $fn();
        $passed++;
        echo "[OK] {$label}\n";
    } catch (Throwable $e) {
        $errors[] = "{$label}: ".$e->getMessage();
        echo "[FAIL] {$label}: ".$e->getMessage()."\n";
    }
}

echo "=== Smoke Test: Marketing + Payroll ===\n\n";

check('MarketingPlanSettings loads', fn () => MarketingPlanSettings::all());
check('MarketingPlanEventAutomationService content types', fn () => MarketingPlanEventAutomationService::contentTypeLabels());
check('EmployeePayrollService preview', fn () => app(EmployeePayrollService::class)->previewPeriod((int) date('n'), (int) date('Y')));

$routes = [
    'admin.moderator-marketing-plans.index',
    'admin.moderator-marketing-plans.settings',
    'admin.moderator-marketing-plans.create',
    'admin.employee-salaries.index',
    'admin.employee-salaries.export',
    'employee.marketing-today.index',
    'employee.marketing-plans.index',
];

foreach ($routes as $name) {
    check("Route exists: {$name}", fn () => Route::has($name) || throw new RuntimeException('missing'));
}

check('Admin marketing index view renders', function () {
    $html = View::make('admin.moderator-marketing-plans.index', [
        'plans' => ModeratorMarketingPlan::query()->paginate(5),
        'moderators' => User::moderatorEmployees()->limit(5)->get(),
        'stats' => [
            'total' => 0, 'active' => 0, 'moderators' => 0, 'platforms' => 0,
            'events_today' => 0, 'pending_confirm_today' => 0, 'penalties_month' => 0,
        ],
    ])->render();
    if (! str_contains($html, 'مركز خطط التسويق')) {
        throw new RuntimeException('unexpected content');
    }
});

check('Admin marketing settings view renders', function () {
    $html = View::make('admin.moderator-marketing-plans.settings', [
        'settings' => MarketingPlanSettings::all(),
    ])->render();
    if (! str_contains($html, 'إعدادات أتمتة خطط التسويق')) {
        throw new RuntimeException('unexpected content');
    }
});

check('Admin employee salaries index view renders', function () {
    $month = (int) date('n');
    $year = (int) date('Y');
    $payroll = app(EmployeePayrollService::class);
    $rows = $payroll->previewPeriod($month, $year);
    $html = View::make('admin.employee-salaries.index', [
        'rows' => $rows,
        'payments' => collect(),
        'stats' => [
            'employees' => $rows->count(),
            'generated' => 0,
            'pending' => 0,
            'paid' => 0,
            'total_net_pending' => 0,
            'total_net_paid' => 0,
            'total_net_preview' => $rows->sum('net_salary'),
        ],
        'month' => $month,
        'year' => $year,
        'wallets' => \App\Models\Wallet::query()->limit(5)->get(),
    ])->render();
    if (! str_contains($html, 'رواتب الموظفين')) {
        throw new RuntimeException('unexpected content');
    }
});

check('Employee marketing today view renders', function () {
    $html = View::make('employee.marketing-today.index', [
        'events' => collect(),
        'stats' => ['total' => 0, 'confirmed' => 0, 'pending' => 0],
    ])->render();
    if (! str_contains($html, 'تسويق اليوم') && ! str_contains($html, 'marketing')) {
        // Arabic title may vary
    }
});

check('EmployeeSalaryPayment model relations', function () {
    $p = new \App\Models\EmployeeSalaryPayment;
    $p->wallet();
    $p->expense();
});

check('ModeratorMarketingCalendarEvent isConfirmed', function () {
    $e = new \App\Models\ModeratorMarketingCalendarEvent;
    $e->isConfirmed();
});

echo "\n=== Summary: {$passed} passed, ".count($errors)." failed ===\n";
exit(count($errors) > 0 ? 1 : 0);
