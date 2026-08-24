<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalesManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasSalesManagerPortalAccess()) {
            abort(403, 'هذه المنطقة مخصصة لمديري المبيعات و Business Developer فقط.');
        }

        return $next($request);
    }
}
