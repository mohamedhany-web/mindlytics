<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesShiftSwapRequest;
use App\Models\User;
use App\Services\SalesShiftScheduleService;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerShiftController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesShiftScheduleService $shifts,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);
        $weekStart = $this->shifts->resolveWeekStart($request->query('week'));

        $board = $this->shifts->buildWeekBoard(null, $weekStart, null, $memberIds);
        $live = $this->shifts->buildTeamLivePanel($memberIds);
        $plan = $this->shifts->activePlan();

        $pendingSwaps = SalesShiftSwapRequest::query()
            ->where('status', SalesShiftSwapRequest::STATUS_PENDING)
            ->where(function ($q) use ($memberIds) {
                $q->whereIn('requester_id', $memberIds)->orWhereIn('partner_id', $memberIds);
            })
            ->with(['requester:id,name', 'partner:id,name'])
            ->orderBy('work_date')
            ->get();

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('employee.sales-manager.shifts.index', [
            'team' => $team,
            'board' => $board,
            'live' => $live,
            'plan' => $plan,
            'weekStart' => $weekStart,
            'pendingSwaps' => $pendingSwaps,
            'members' => $members,
            'filterUserId' => $request->integer('user_id') ?: null,
        ]);
    }

    public function show(Request $request, User $employee): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);
        abort_unless(in_array((int) $employee->id, $memberIds, true), 404);

        $weekStart = $this->shifts->resolveWeekStart($request->query('week'));
        $board = $this->shifts->buildWeekBoard(null, $weekStart, (int) $employee->id, $memberIds);
        $today = $this->shifts->memberShiftToday((int) $employee->id);

        return view('employee.sales-manager.shifts.show', [
            'team' => $team,
            'employee' => $employee,
            'board' => $board,
            'today' => $today,
            'weekStart' => $weekStart,
        ]);
    }
}
