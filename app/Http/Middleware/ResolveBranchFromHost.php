<?php

namespace App\Http\Middleware;

use App\Services\Branch\BranchResolver;
use App\Support\BranchContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يحدد الفرع من Host الطلب ويضعه في الحاوية ومشاركة العرض (بدون تقييد مسارات الإدارة).
 */
class ResolveBranchFromHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            App::instance(BranchContext::class, new BranchContext(null));

            return $next($request);
        }

        $branch = BranchResolver::fromHost($request->getHost());
        App::instance(BranchContext::class, new BranchContext($branch));

        view()->share('resolvedBranch', $branch);

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        $path = $request->path();

        $prefixes = [
            'admin',
            'branch-office',
            'api',
            'storage',
            'up',
            'livewire',
            'sanctum',
            'horizon',
            'pulse',
            '_debugbar',
            'telescope',
            'community',
            'employee',
            'webhooks',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }
}
