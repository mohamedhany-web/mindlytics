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
        if (! $user || ! $user->isModeratorEmployee()) {
            abort(403, 'هذه المنطقة مخصصة لموظفي المشرفين (moderator) فقط.');
        }

        return $next($request);
    }
}
