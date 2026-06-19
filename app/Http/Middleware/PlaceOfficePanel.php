<?php

namespace App\Http\Middleware;

use App\Models\OfflineLocation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يربط طلبات مسار /place-office بمكان المستخدم (مدير المكان).
 */
class PlaceOfficePanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== 'place_manager') {
            abort(403, 'هذه المنطقة مخصصة لمديري الأماكن الإدارية.');
        }

        $locationId = $user->offline_location_id;
        if (! $locationId) {
            abort(403, 'لم يُربط حسابك بمكان إداري. تواصل مع الإدارة المركزية.');
        }

        $location = OfflineLocation::query()->whereKey($locationId)->where('is_active', true)->first();
        if (! $location) {
            abort(403, 'المكان المرتبط بحسابك غير متاح.');
        }

        view()->share('resolvedPlaceLocation', $location);

        return $next($request);
    }
}
