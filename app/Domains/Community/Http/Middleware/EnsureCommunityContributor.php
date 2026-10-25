<?php

namespace App\Domains\Community\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommunityContributor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('community.login');
        }

        if (!auth()->user()->is_community_contributor) {
            return redirect()->route('community.dashboard')
                ->with('warning', 'ليس لديك صلاحية مساهم. للوصول إلى لوحة المساهمين يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}
