<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Learning\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Queue;

/**
 * كل صفحة GET بلا معاملات تُفتح بالمستخدم المناسب لها (حسب middleware الـ route) ولا ترجع 500.
 * الـ 500 الموجودة قبل النقل (نفس النتيجة على main) في KNOWN_500 – لا تضف إليها إلا بعد التأكد من main.
 */
class PagesSmokeTest extends SmokeTestCase
{
    /** صفحات ترجع 500 قبل النقل أيضاً (views مفقودة أو قوالب بها أخطاء) – انظر ModularityChecks. */
    private const KNOWN_500 = [
        'admin/blog', 'admin/attendance', 'admin/subjects/create', 'instructor/questions', 'tasks', 'tasks/create',
    ];

    /** routes تتصل بخدمات خارجية أو تسجّل الخروج أو تستقبل عودة بوابات الدفع. */
    private const SKIP = [
        'logout', 'admin/meta-social/oauth/redirect', 'admin/meta-social/oauth/callback', 'admin/meta-social/settings/test',
        'checkout/kashier/callback', 'webhooks/whatsapp', 'webhooks/meta-social', 'auth/google', 'auth/google/callback',
        'js/checkout-pay-widget.v1.js', // يجلب سكربت فواتيرك من خوادمهم
    ];

    public function test_every_parameterless_page_renders_without_server_error(): void
    {
        Queue::fake();
        $actors = $this->actors();
        $failures = [];
        $checked = 0;
        $report = [];

        foreach ($this->app['router']->getRoutes()->getRoutes() as $route) {
            /** @var Route $route */
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || str_contains($uri, '{') || in_array($uri, self::SKIP, true)
                || str_starts_with($uri, '_') || str_starts_with($uri, 'storage') || str_starts_with($uri, 'sanctum')) {
                continue;
            }

            [$actorKey, $guard] = $this->actorFor($route);
            $actor = $actors[$actorKey] ?? null;
            if ($actorKey !== 'guest' && $actor === null) {
                continue; // لا يوجد مستخدم بهذا الدور في النسخة
            }

            $this->app['auth']->forgetGuards();
            $request = $actor ? $this->actingAs($actor, $guard) : $this;
            $response = $request->get('/'.ltrim($uri, '/'), $guard === 'sanctum' ? ['Accept' => 'application/json'] : []);
            $checked++;
            $report[] = $response->getStatusCode()."\t{$actorKey}\t/{$uri}";

            if ($response->getStatusCode() >= 500 && ! in_array($uri, self::KNOWN_500, true)) {
                $message = $response->exception ? get_class($response->exception).': '.mb_substr($response->exception->getMessage(), 0, 200) : '';
                $failures[] = "{$response->getStatusCode()} /{$uri} as {$actorKey} {$message}";
            }
        }

        // تقرير بكل الصفحات وحالتها: يفيد للمقارنة قبل/بعد الرفع على staging
        sort($report);
        file_put_contents(storage_path('logs/smoke-pages.txt'), implode("\n", $report)."\n");

        $this->assertGreaterThan(300, $checked, 'Too few pages were checked; are the role users present in the smoke database?');
        $this->assertSame([], $failures, "Pages returning server errors:\n".implode("\n", $failures));
    }

    /** @return array{0: string, 1: string|null} */
    private function actorFor(Route $route): array
    {
        $mw = implode(' ', array_filter($route->gatherMiddleware(), 'is_string'));
        $uri = $route->uri();

        return match (true) {
            str_contains($mw, 'auth:sanctum') => [str_contains($uri, 'instructor') ? 'instructor' : 'student', 'sanctum'],
            str_starts_with($uri, 'admin') => ['super_admin', 'web'],
            str_contains($mw, 'branch.office') => ['branch_manager', 'web'],
            str_contains($mw, 'place.office') => ['place_manager', 'web'],
            str_contains($mw, 'sales.manager') || str_starts_with($uri, 'employee/sales-manager') => ['sales_manager', 'web'],
            str_contains($mw, 'sales.') || str_starts_with($uri, 'employee/sales') || str_starts_with($uri, 'employee/team-chat') => ['sales_employee', 'web'],
            str_starts_with($uri, 'employee') => ['employee', 'web'],
            str_starts_with($uri, 'instructor') || str_contains($mw, 'instructor') => ['instructor', 'web'],
            str_contains($mw, 'auth') => ['student', 'web'],
            default => ['guest', null],
        };
    }

    /** @return array<string, User|null> */
    private function actors(): array
    {
        $byJob = fn (string $code) => User::query()->where('is_active', true)
            ->whereIn('employee_job_id', EmployeeJob::query()->where('code', $code)->pluck('id'))->first();
        $student = User::query()->where('role', 'student')->where('is_active', true)
            ->whereIn('id', StudentCourseEnrollment::query()->where('status', 'active')->pluck('user_id'))->first()
            ?? User::query()->where('role', 'student')->where('is_active', true)->first();

        return [
            'super_admin' => User::query()->where('role', 'super_admin')->where('is_active', true)->first(),
            'instructor' => User::query()->whereIn('role', ['instructor', 'teacher'])->where('is_active', true)->orderByDesc('id')->first(),
            'student' => $student,
            'branch_manager' => User::query()->where('role', 'branch_manager')->where('is_active', true)->whereNotNull('branch_id')->first(),
            'place_manager' => User::query()->where('role', 'place_manager')->where('is_active', true)->whereNotNull('offline_location_id')->first(),
            'sales_employee' => $byJob('sales'),
            'sales_manager' => $byJob('sales_manager'),
            'employee' => User::query()->where('is_employee', true)->where('is_active', true)->first() ?? $byJob('designer'),
        ];
    }
}
