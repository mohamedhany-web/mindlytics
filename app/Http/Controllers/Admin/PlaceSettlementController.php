<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineLocation;
use App\Models\PlaceMonthlySettlement;
use App\Models\Wallet;
use App\Services\PlaceSettlementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlaceSettlementController extends Controller
{
    public function __construct(protected PlaceSettlementService $settlementService)
    {
    }

    public function index(Request $request)
    {
        $query = PlaceMonthlySettlement::query()->with(['location', 'expense', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('location_id')) {
            $query->where('offline_location_id', $request->location_id);
        }
        if ($request->filled('month')) {
            $query->where('period_month', $request->month);
        }

        $settlements = $query->latest('period_month')->paginate(20)->withQueryString();
        $locations = OfflineLocation::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.place-settlements.index', compact('settlements', 'locations'));
    }

    public function show(PlaceMonthlySettlement $placeSettlement)
    {
        $placeSettlement->load(['location', 'usageLogs.logger', 'expense', 'invoice', 'wallet']);
        $this->settlementService->recalculateSettlement($placeSettlement);

        $wallets = Wallet::query()->orderBy('name')->get(['id', 'name', 'balance']);

        return view('admin.place-settlements.show', compact('placeSettlement', 'wallets'));
    }

    public function approve(Request $request, PlaceMonthlySettlement $placeSettlement)
    {
        try {
            $this->settlementService->approveSettlement(
                $placeSettlement,
                $request->user(),
                $request->input('wallet_id') ? (int) $request->input('wallet_id') : null
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم اعتماد المخالصة وإنشاء مصروف وفاتورة للمكان.');
    }

    public function close(PlaceMonthlySettlement $placeSettlement)
    {
        try {
            $this->settlementService->closeMonth($placeSettlement, auth()->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم إقفال الشهر بنجاح.');
    }
}
