<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModeratorEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasModeratorPortalAccess()) {
            abort(403, 'هذه المنطقة مخصصة لمشرفي المحتوى و Business Developer فقط.');
        }

        return $next($request);
    }
}
