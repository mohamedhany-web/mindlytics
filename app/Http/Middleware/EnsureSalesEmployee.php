<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSalesEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isSalesEmployee()) {
            abort(403, 'هذه المنطقة مخصصة لموظفي المبيعات فقط.');
        }

        return $next($request);
    }
}
