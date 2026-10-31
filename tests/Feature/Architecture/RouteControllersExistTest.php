<?php

namespace Tests\Feature\Architecture;

use Tests\TestCase;

/**
 * كل route يشير إلى controller و method موجودين فعلاً.
 * يكشف أي namespace خاطئ بعد نقل controllers إلى app/Domains دون فتح كل صفحة.
 */
class RouteControllersExistTest extends TestCase
{
    /**
     * routes معروف أنها مكسورة (مسجّلة بدون method في الـ controller) ولم تُصلح بعد.
     * أُفرغت في 2026-10-22 بعد إصلاح الـ 10 routes الأصلية؛ لا تضف أسطراً جديدة هنا.
     */
    private const KNOWN_BROKEN = [];

    public function test_every_route_action_resolves(): void
    {
        $missing = [];

        foreach ($this->app['router']->getRoutes()->getRoutes() as $route) {
            $uses = $route->getAction('uses');
            if (! is_string($uses)) {
                continue; // Closure
            }

            [$class, $method] = str_contains($uses, '@') ? explode('@', $uses, 2) : [$uses, '__invoke'];

            if (! class_exists($class)) {
                $missing[] = "{$route->uri()} → class {$class}";
            } elseif (! method_exists($class, $method)) {
                $missing[] = "{$route->uri()} → {$class}@{$method}";
            }
        }

        $new = array_values(array_diff($missing, self::KNOWN_BROKEN));
        $this->assertSame([], $new, "Routes point to missing controllers/methods:\n".implode("\n", $new));

        $fixed = array_values(array_diff(self::KNOWN_BROKEN, $missing));
        $this->assertSame([], $fixed, "These routes are fixed now; remove them from KNOWN_BROKEN:\n".implode("\n", $fixed));
    }
}
