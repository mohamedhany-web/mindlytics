<?php

/**
 * Integration test against the real database (uses .env).
 * Run: php scripts/integration-test-pages.php
 */

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$errors = [];
$passed = 0;

function testController(string $label, callable $fn): void
{
    global $errors, $passed;

    try {
        $result = $fn();
        if ($result instanceof \Illuminate\View\View) {
            $html = $result->render();
            if ($html === '') {
                throw new RuntimeException('empty view');
            }
        } elseif ($result instanceof \Symfony\Component\HttpFoundation\Response) {
            if ($result->getStatusCode() >= 500) {
                throw new RuntimeException('HTTP '.$result->getStatusCode());
            }
        }
        $passed++;
        echo "[OK] {$label}\n";
    } catch (Throwable $e) {
        $errors[] = "{$label}: ".$e->getMessage();
        echo "[FAIL] {$label}: ".$e->getMessage()."\n";
    }
}

function hit(string $label, string $method, string $uri, ?User $user = null): void
{
    global $errors, $passed;

    Auth::logout();
    if ($user) {
        Auth::guard('web')->login($user);
    }

    $request = Request::create($uri, $method);
    $session = app('session.store');
    $request->setLaravelSession($session);
    if ($user) {
        $request->setUserResolver(fn () => $user);
    }

    try {
        $response = app()->handle($request);
        $code = $response->getStatusCode();

        if ($code >= 500) {
            $body = substr((string) $response->getContent(), 0, 500);
            throw new RuntimeException("HTTP {$code} — ".strip_tags($body));
        }

        if (! $user && ! in_array($code, [302, 301], true)) {
            throw new RuntimeException("Expected redirect for guest, got {$code}");
        }

        if ($user && $code !== 200) {
            throw new RuntimeException("Expected 200 for authenticated user, got {$code}");
        }

        $passed++;
        echo "[OK] {$label} ({$code})\n";
    } catch (Throwable $e) {
        $errors[] = "{$label}: ".$e->getMessage();
        echo "[FAIL] {$label}: ".$e->getMessage()."\n";
    } finally {
        Auth::logout();
    }
}

echo "=== Integration Page Test (real DB) ===\n\n";

$admin = User::query()->where('role', 'super_admin')->where('is_active', true)->first();
$employee = User::query()->employees()->where('is_active', true)->first();

if (! $admin) {
    echo "[WARN] No super_admin user found — skipping authenticated admin tests\n";
}
if (! $employee) {
    echo "[WARN] No active employee found — skipping employee tests\n";
}

$guestUrls = [
    ['GET', '/admin/moderator-marketing-plans'],
    ['GET', '/admin/moderator-marketing-plans/settings'],
    ['GET', '/admin/moderator-marketing-plans/create'],
    ['GET', '/admin/employee-salaries'],
    ['GET', '/admin/employee-salaries/export?month=6&year=2026'],
    ['GET', '/employee/marketing-today'],
];

foreach ($guestUrls as [$method, $uri]) {
    hit("Guest {$method} {$uri}", $method, $uri);
}

if ($admin) {
    Auth::guard('web')->login($admin);

    testController('Admin marketing plans index', fn () => app(\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class)->index(
        Request::create('/admin/moderator-marketing-plans', 'GET')
    ));

    testController('Admin marketing settings', fn () => app(\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class)->settings());

    testController('Admin employee salaries', fn () => app(\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class)->index(
        Request::create('/admin/employee-salaries', 'GET')
    ));

    testController('Admin employee salaries export', function () {
        return app(\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class)->export(
            Request::create('/admin/employee-salaries/export?month='.date('n').'&year='.date('Y'), 'GET')
        );
    });

    $plan = \App\Models\ModeratorMarketingPlan::query()->first();
    if ($plan) {
        testController('Admin marketing plan show #'.$plan->id, fn () => app(\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class)->show($plan));
        testController('Admin marketing plan edit #'.$plan->id, fn () => app(\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class)->edit($plan));
    } else {
        echo "[SKIP] No marketing plan in DB for show test\n";
    }

    testController('Admin marketing plan create', fn () => app(\App\Http\Controllers\Admin\ModeratorMarketingPlanController::class)->create());

    $pendingPay = \App\Models\EmployeeSalaryPayment::query()->whereIn('status', ['pending', 'overdue'])->first();
    if ($pendingPay) {
        testController('Admin employee salary pay form', fn () => app(\App\Http\Controllers\Admin\EmployeeSalaryPayrollController::class)->pay($pendingPay));
    }

    Auth::logout();
}

if ($employee) {
    Auth::guard('web')->login($employee);

    testController('Employee marketing today', fn () => app(\App\Http\Controllers\Employee\MarketingTodayController::class)->index(
        app(\App\Services\MarketingPlanEventAutomationService::class)
    ));

    testController('Employee marketing plans index', fn () => app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)->index());

    $empPlan = \App\Models\ModeratorMarketingPlan::query()
        ->where('moderator_id', $employee->id)
        ->first() ?? \App\Models\ModeratorMarketingPlan::query()->first();
    if ($empPlan) {
        testController('Employee marketing plan show', fn () => app(\App\Http\Controllers\Employee\ModeratorMarketingPlanController::class)->show($empPlan));
    }

    Auth::logout();
}

// Service smoke
try {
    app(\App\Services\EmployeePayrollService::class)->previewPeriod((int) date('n'), (int) date('Y'));
    echo "[OK] EmployeePayrollService previewPeriod\n";
    $passed++;
} catch (Throwable $e) {
    $errors[] = 'EmployeePayrollService: '.$e->getMessage();
    echo "[FAIL] EmployeePayrollService: {$e->getMessage()}\n";
}

try {
    \Illuminate\Support\Facades\Artisan::call('marketing:remind-today-events');
    \Illuminate\Support\Facades\Artisan::call('marketing:apply-execution-penalties');
    echo "[OK] Marketing artisan commands\n";
    $passed++;
} catch (Throwable $e) {
    $errors[] = 'Marketing commands: '.$e->getMessage();
    echo "[FAIL] Marketing commands: {$e->getMessage()}\n";
}

echo "\n=== Summary: {$passed} passed, ".count($errors)." failed ===\n";
if ($errors) {
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
}
exit(count($errors) > 0 ? 1 : 0);
