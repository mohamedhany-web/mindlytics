<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Queue;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * كل صفحات GET في موديول المبيعات التي تأخذ معاملات ({lead}، {group}، {employee}…)
 * تُفتح بأول سجل مناسب من النسخة، بالمستخدم المناسب لها، ولا ترجع 500.
 * (الصفحات بلا معاملات يغطيها PagesSmokeTest.)
 */
class SalesPagesSmokeTest extends SmokeTestCase
{
    public function test_every_sales_page_with_parameters_renders(): void
    {
        Queue::fake();
        $admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $employee = $this->byJob('sales');
        $manager = $this->byJob('sales_manager');
        $this->assertNotNull($employee, 'the smoke database needs an active sales employee');

        $failures = [];
        $report = [];
        $checked = 0;
        foreach ($this->app['router']->getRoutes()->getRoutes() as $route) {
            /** @var Route $route */
            $action = (string) $route->getActionName();
            if (! str_contains($action, 'Domains\\Sales\\') || ! in_array('GET', $route->methods(), true) || ! str_contains($route->uri(), '{')) {
                continue;
            }
            $uri = $route->uri();
            $actor = match (true) {
                str_starts_with($uri, 'admin') => $admin,
                str_starts_with($uri, 'employee/sales-manager') => $manager,
                str_starts_with($uri, 'employee') => $employee,
                default => null,
            };
            if (str_starts_with($uri, 'employee/sales-manager') && ! $manager) {
                continue;
            }

            $url = $this->fillParameters($route, $actor ?? $employee);
            if ($url === null) {
                $report[] = "SKIP\t{$uri}\t(no record for a parameter)";

                continue;
            }

            $this->app['auth']->forgetGuards();
            $response = ($actor ? $this->actingAs($actor) : $this)->get($url);
            $checked++;
            $report[] = $response->getStatusCode()."\t{$url}";
            if ($response->getStatusCode() >= 500) {
                $e = $response->exception;
                $failures[] = "{$response->getStatusCode()} {$url} ".($e ? get_class($e).': '.mb_substr($e->getMessage(), 0, 220).' @ '.str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine() : '');
            }
        }

        file_put_contents(storage_path('logs/smoke-sales-pages.txt'), implode("\n", $report)."\n");
        $this->assertGreaterThan(20, $checked);
        $this->assertSame([], $failures, "Sales pages returning server errors:\n".implode("\n", $failures));
    }

    private function fillParameters(Route $route, User $actor): ?string
    {
        $types = $this->parameterTypes($route);
        $values = [];
        foreach ($route->parameterNames() as $name) {
            $class = $types[$name] ?? null;
            $value = match (true) {
                $class === SalesLead::class => SalesLead::where('assigned_to', $actor->id)->value('id') ?? SalesLead::value('id'),
                $class === User::class => $this->byJob('sales')?->id,
                $class !== null && is_a($class, Model::class, true) => $class::query()->value((new $class)->getRouteKeyName()),
                default => $this->guessScalar($name),
            };
            if ($value === null) {
                return null;
            }
            $values[$name] = $value;
        }

        return '/'.ltrim(preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => rawurlencode((string) $values[$m[1]]), $route->uri()), '/');
    }

    /** @return array<string, class-string> */
    private function parameterTypes(Route $route): array
    {
        $uses = $route->getAction('uses');
        if (! is_string($uses)) {
            return [];
        }
        [$class, $method] = str_contains($uses, '@') ? explode('@', $uses, 2) : [$uses, '__invoke'];
        $types = [];
        foreach ((new ReflectionMethod($class, $method))->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $types[$param->getName()] = $type->getName();
            }
        }

        return $types;
    }

    private function guessScalar(string $name): ?string
    {
        return match (true) {
            str_contains($name, 'date') || $name === 'day' => now()->toDateString(),
            str_contains($name, 'month') => now()->format('Y-m'),
            str_contains($name, 'slug') || str_contains($name, 'token') => null,
            default => '1',
        };
    }

    private function byJob(string $code): ?User
    {
        return User::query()->where('is_active', true)
            ->whereIn('employee_job_id', EmployeeJob::query()->where('code', $code)->pluck('id'))->first();
    }
}
