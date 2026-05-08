<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** مسارات API الخاصة بالمدربين (داخل التطبيق). */
class EnsureApiInstructor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isInstructor()) {
            return response()->json([
                'message' => 'هذه الواجهة مخصصة للمدربين فقط.',
                'code' => 'instructors_only',
            ], 403);
        }

        return $next($request);
    }
}

