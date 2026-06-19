<?php

namespace App\Http\Controllers\Place;

use App\Http\Controllers\Controller;
use App\Models\PlaceInvoice;
use App\Models\PlaceMonthlySettlement;
use App\Models\PlaceUsageLog;
use Illuminate\Http\Request;

class PlaceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $location = view()->shared('resolvedPlaceLocation');
        $period = now()->format('Y-m');

        $currentSettlement = PlaceMonthlySettlement::query()
            ->where('offline_location_id', $location->id)
            ->where('period_month', $period)
            ->first();

        $pendingLogs = PlaceUsageLog::query()
            ->where('offline_location_id', $location->id)
            ->pending()
            ->count();

        $approvedHoursThisMonth = PlaceUsageLog::query()
            ->where('offline_location_id', $location->id)
            ->approved()
            ->whereYear('usage_date', now()->year)
            ->whereMonth('usage_date', now()->month)
            ->sum('hours');

        $daysLeftInMonth = now()->endOfMonth()->diffInDays(now());
        $mustCloseSoon = $daysLeftInMonth <= 5
            && (! $currentSettlement || ! in_array($currentSettlement->status, ['closed', 'paid'], true));

        $user = $request->user();

        return view('place-office.dashboard', compact(
            'location',
            'currentSettlement',
            'pendingLogs',
            'approvedHoursThisMonth',
            'daysLeftInMonth',
            'mustCloseSoon',
            'period',
            'user'
        ));
    }
}
