<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesShiftSwapRequest;
use App\Services\SalesTeamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerShiftSwapController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $pending = SalesShiftSwapRequest::query()
            ->where('status', SalesShiftSwapRequest::STATUS_PENDING)
            ->where(function ($q) use ($memberIds) {
                $q->whereIn('requester_id', $memberIds)->orWhereIn('partner_id', $memberIds);
            })
            ->with(['requester:id,name', 'partner:id,name', 'segment'])
            ->orderBy('work_date')
            ->get();

        $recent = SalesShiftSwapRequest::query()
            ->whereIn('status', [SalesShiftSwapRequest::STATUS_APPROVED, SalesShiftSwapRequest::STATUS_REJECTED])
            ->where(function ($q) use ($memberIds) {
                $q->whereIn('requester_id', $memberIds)->orWhereIn('partner_id', $memberIds);
            })
            ->with(['requester:id,name', 'partner:id,name', 'reviewer:id,name'])
            ->orderByDesc('reviewed_at')
            ->limit(30)
            ->get();

        return view('employee.sales-manager.shift-swaps.index', compact('pending', 'recent', 'team'));
    }

    public function review(Request $request, SalesShiftSwapRequest $swap): RedirectResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(
            in_array((int) $swap->requester_id, $memberIds, true) || in_array((int) $swap->partner_id, $memberIds, true),
            404
        );

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'manager_notes' => 'nullable|string|max:500',
        ]);

        abort_if($swap->status !== SalesShiftSwapRequest::STATUS_PENDING, 422);

        $swap->update([
            'status' => $validated['action'] === 'approve'
                ? SalesShiftSwapRequest::STATUS_APPROVED
                : SalesShiftSwapRequest::STATUS_REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'manager_notes' => $validated['manager_notes'] ?? null,
        ]);

        return back()->with('success', $validated['action'] === 'approve' ? 'تم اعتماد التبديل.' : 'تم رفض الطلب.');
    }
}
