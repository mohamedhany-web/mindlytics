<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlaceDailyExpense;
use App\Services\PlaceSettlementService;
use Illuminate\Http\Request;

class PlaceDailyExpenseController extends Controller
{
    public function __construct(protected PlaceSettlementService $settlementService)
    {
    }

    public function approve(PlaceDailyExpense $placeDailyExpense)
    {
        if ($placeDailyExpense->status !== PlaceDailyExpense::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن الموافقة على هذا المصروف.');
        }

        $placeDailyExpense->update([
            'status' => PlaceDailyExpense::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        if ($placeDailyExpense->settlement) {
            $this->settlementService->recalculateSettlement($placeDailyExpense->settlement);
        }

        return back()->with('success', 'تمت الموافقة على المصروف اليومي.');
    }

    public function reject(Request $request, PlaceDailyExpense $placeDailyExpense)
    {
        if ($placeDailyExpense->status !== PlaceDailyExpense::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن رفض هذا المصروف.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $placeDailyExpense->update([
            'status' => PlaceDailyExpense::STATUS_REJECTED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        if ($placeDailyExpense->settlement) {
            $this->settlementService->recalculateSettlement($placeDailyExpense->settlement);
        }

        return back()->with('success', 'تم رفض المصروف اليومي.');
    }
}
