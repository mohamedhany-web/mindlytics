<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalesStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isSalesStaff()) {
            abort(403, 'هذه المنطقة مخصصة لموظفي المبيعات فقط.');
        }

        return $next($request);
    }
}
