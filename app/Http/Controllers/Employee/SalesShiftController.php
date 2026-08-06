<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesShiftSwapRequest;
use App\Models\User;
use App\Services\SalesShiftScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesShiftController extends Controller
{
    public function index(Request $request, SalesShiftScheduleService $shifts): View
    {
        $user = Auth::user();
        $weekStart = $shifts->resolveWeekStart($request->query('week'));
        $board = $shifts->buildWeekBoard(null, $weekStart, $user->id, [(int) $user->id]);

        $swaps = SalesShiftSwapRequest::query()
            ->where(function ($q) use ($user) {
                $q->where('requester_id', $user->id)->orWhere('partner_id', $user->id);
            })
            ->with(['requester:id,name', 'partner:id,name', 'segment'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $colleagues = User::salesEmployees()
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('employee.sales.shifts.index', [
            'board' => $board,
            'weekStart' => $weekStart,
            'swaps' => $swaps,
            'colleagues' => $colleagues,
        ]);
    }

    public function storeSwap(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'partner_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'work_date' => 'required|date',
            'segment_id' => 'nullable|integer|exists:sales_shift_segments,id',
            'reason' => 'required|string|max:500',
        ]);

        $partner = User::query()->findOrFail((int) $validated['partner_id']);
        abort_unless($partner->isSalesEmployee(), 422);

        SalesShiftSwapRequest::create([
            'requester_id' => $user->id,
            'partner_id' => $partner->id,
            'segment_id' => $validated['segment_id'] ?? null,
            'work_date' => $validated['work_date'],
            'day_of_week' => app(SalesShiftScheduleService::class)->salesDayIndex(\Carbon\Carbon::parse($validated['work_date'])),
            'reason' => $validated['reason'],
            'status' => SalesShiftSwapRequest::STATUS_PENDING,
        ]);

        return back()->with('success', 'تم إرسال طلب تبديل الشيفت — بانتظار اعتماد المدير.');
    }

    public function cancelSwap(SalesShiftSwapRequest $swap): RedirectResponse
    {
        abort_unless((int) $swap->requester_id === (int) Auth::id(), 403);
        abort_unless($swap->status === SalesShiftSwapRequest::STATUS_PENDING, 422);

        $swap->update(['status' => SalesShiftSwapRequest::STATUS_CANCELLED]);

        return back()->with('success', 'تم إلغاء الطلب.');
    }
}
