<?php

namespace App\Http\Controllers\Place;

use App\Http\Controllers\Controller;
use App\Models\PlaceMonthlySettlement;
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
        $location = view()->shared('resolvedPlaceLocation');

        $settlements = PlaceMonthlySettlement::query()
            ->where('offline_location_id', $location->id)
            ->with(['expense', 'invoice'])
            ->latest('period_month')
            ->paginate(12);

        return view('place-office.settlements.index', compact('location', 'settlements'));
    }

    public function show(PlaceMonthlySettlement $settlement)
    {
        $location = view()->shared('resolvedPlaceLocation');
        abort_unless((int) $settlement->offline_location_id === (int) $location->id, 403);

        $settlement->load(['usageLogs.logger', 'expense', 'invoice']);

        $this->settlementService->recalculateSettlement($settlement);

        return view('place-office.settlements.show', compact('location', 'settlement'));
    }

    public function submit(Request $request, PlaceMonthlySettlement $settlement)
    {
        $location = view()->shared('resolvedPlaceLocation');
        abort_unless((int) $settlement->offline_location_id === (int) $location->id, 403);

        try {
            $this->settlementService->submitForReview($settlement, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم إرسال المخالصة الشهرية للمراجعة.');
    }
}
