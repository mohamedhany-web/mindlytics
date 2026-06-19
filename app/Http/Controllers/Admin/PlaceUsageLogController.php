<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineLocation;
use App\Models\PlaceMonthlySettlement;
use App\Models\PlaceDailyExpense;
use App\Models\PlaceUsageLog;
use App\Models\Wallet;
use App\Services\PlaceSettlementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlaceUsageLogController extends Controller
{
    public function __construct(protected PlaceSettlementService $settlementService)
    {
    }

    public function index(Request $request)
    {
        $query = PlaceUsageLog::query()->with(['location', 'logger', 'reviewer', 'offlineCourse']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('location_id')) {
            $query->where('offline_location_id', $request->location_id);
        }
        if ($request->filled('month')) {
            [$y, $m] = explode('-', $request->month);
            $query->whereYear('usage_date', $y)->whereMonth('usage_date', $m);
        }

        $logs = $query->latest('usage_date')->paginate(25)->withQueryString();
        $locations = OfflineLocation::query()->orderBy('name')->get(['id', 'name']);

        $expenseQuery = PlaceDailyExpense::query()->with(['location', 'logger', 'reviewer', 'usageLog']);

        if ($request->filled('status')) {
            $expenseQuery->where('status', $request->status);
        }
        if ($request->filled('location_id')) {
            $expenseQuery->where('offline_location_id', $request->location_id);
        }
        if ($request->filled('month')) {
            [$y, $m] = explode('-', $request->month);
            $expenseQuery->whereYear('expense_date', $y)->whereMonth('expense_date', $m);
        }

        $dailyExpenses = $expenseQuery->latest('expense_date')->paginate(25, ['*'], 'expenses_page')->withQueryString();

        return view('admin.place-usage-logs.index', compact('logs', 'locations', 'dailyExpenses'));
    }

    public function approve(PlaceUsageLog $placeUsageLog)
    {
        if ($placeUsageLog->status !== PlaceUsageLog::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن الموافقة على هذا السجل.');
        }

        $placeUsageLog->update([
            'status' => PlaceUsageLog::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        if ($placeUsageLog->settlement) {
            $this->settlementService->recalculateSettlement($placeUsageLog->settlement);
        }

        return back()->with('success', 'تمت الموافقة على سجل الساعات.');
    }

    public function reject(Request $request, PlaceUsageLog $placeUsageLog)
    {
        if ($placeUsageLog->status !== PlaceUsageLog::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن رفض هذا السجل.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $placeUsageLog->update([
            'status' => PlaceUsageLog::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'تم رفض سجل الساعات.');
    }
}
