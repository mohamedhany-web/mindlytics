<?php

namespace Tests\Feature\Architecture;

use Illuminate\Routing\Route;
use Tests\TestCase;

/**
 * يحمي من تغيّر الـ URLs أو أسماء الـ routes أو الـ middleware أو ترتيب المطابقة
 * أثناء نقل الكود إلى app/Domains (docs/MODULARIZATION_PLAN.md).
 *
 * الـ controller نفسه غير محفوظ في الـ snapshot لأنه يتغير عمداً عند النقل؛
 * وجوده يتحقق منه RouteControllersExistTest.
 *
 * لتحديث الـ snapshot بعد تغيير مقصود في الـ routes:
 *   UPDATE_ROUTE_SNAPSHOT=1 php artisan test --filter=RouteSnapshotTest
 */
class RouteSnapshotTest extends TestCase
{
    private const SNAPSHOT = __DIR__.'/../../Snapshots/routes.txt';

    public function test_routes_match_snapshot(): void
    {
        $current = $this->lines($this->currentRoutes());

        if (getenv('UPDATE_ROUTE_SNAPSHOT') || ! file_exists(self::SNAPSHOT)) {
            if (! is_dir(dirname(self::SNAPSHOT))) {
                mkdir(dirname(self::SNAPSHOT), 0755, true);
            }
            file_put_contents(self::SNAPSHOT, implode("\n", $current)."\n");
            $this->markTestSkipped('Route snapshot written to tests/Snapshots/routes.txt');
        }

        $expected = file(self::SNAPSHOT, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $hint = 'If intended, run: UPDATE_ROUTE_SNAPSHOT=1 php artisan test --filter=RouteSnapshotTest';
        $this->assertSame([], array_values(array_diff($expected, $current)), "Routes removed or changed. {$hint}");
        $this->assertSame([], array_values(array_diff($current, $expected)), "Routes added or changed. {$hint}");
        // نفس الـ routes لكن بترتيب مختلف يغيّر أولوية المطابقة
        $this->assertSame($expected, $current, "Route order changed. {$hint}");
    }

    /** @return list<array{methods: string, uri: string, name: ?string, middleware: list<string>}> */
    private function currentRoutes(): array
    {
        return collect($this->app['router']->getRoutes()->getRoutes())
            ->map(fn (Route $route) => [
                'methods' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'middleware' => array_values(array_map('strval', $route->middleware())),
            ])
            ->values()
            ->all();
    }

    /** سطر لكل route حتى يكون الفرق في git وفي رسالة الفشل مقروءاً. */
    private function lines(array $routes): array
    {
        return array_map(
            fn (array $r) => sprintf('%s %s [%s] {%s}', $r['methods'], $r['uri'], $r['name'] ?? '', implode(',', $r['middleware'])),
            $routes
        );
    }
}
