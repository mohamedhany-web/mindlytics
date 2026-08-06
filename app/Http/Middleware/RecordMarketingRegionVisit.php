<?php

namespace App\Http\Middleware;

use App\Services\MarketingRegionsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Count one public page visit per browser session per day (by country).
 */
class RecordMarketingRegionVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if (! $request->isMethod('GET') || $request->ajax() || $request->expectsJson()) {
                return $response;
            }

            $path = ltrim($request->path(), '/');
            if ($this->shouldSkip($path)) {
                return $response;
            }

            if ($request->session()->get('ml_region_visit_day') === now()->toDateString()) {
                return $response;
            }

            app(MarketingRegionsService::class)->recordPublicVisit($request->ip());
            $request->session()->put('ml_region_visit_day', now()->toDateString());
        } catch (\Throwable) {
            // never break the page
        }

        return $response;
    }

    protected function shouldSkip(string $path): bool
    {
        if ($path === '' || $path === '/') {
            return false;
        }

        $prefixes = [
            'admin', 'employee', 'instructor', 'branch-office', 'place-office',
            'api', 'sanctum', 'livewire', 'telescope', 'horizon',
            'webhooks', 'up', 'storage', 'build', 'vendor',
            'my-courses', 'student', 'cashier', 'fawaterak',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }
}
