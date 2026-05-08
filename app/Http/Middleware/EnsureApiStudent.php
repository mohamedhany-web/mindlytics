<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** مسارات API الخاصة بالطلاب فقط (تطبيق الموبايل). */
class EnsureApiStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->isStudent()) {
            return response()->json([
                'message' => 'هذا التطبيق مخصص للطلاب فقط.',
                'code' => 'students_only',
            ], 403);
        }

        return $next($request);
    }
}
