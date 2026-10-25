<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Services\SalesManagerOpsBoardService;
use App\Domains\Sales\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerOpsBoardController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesManagerOpsBoardService $opsBoard,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        $filters = [
            'employee_id' => $request->employee_id,
            'work_mode' => $request->work_mode,
            'attendance' => $request->attendance,
            'presence' => $request->presence,
        ];

        $board = $this->opsBoard->build($memberIds, $date, $filters);

        $members = \App\Models\User::query()
            ->whereIn('id', $memberIds)
            ->orderBy('name')
            ->get(['id', 'name', 'work_mode']);

        $view = $request->boolean('partial')
            ? 'sales::manager.ops-board-partial'
            : 'sales::manager.ops-board';

        return view($view, [
            'team' => $team,
            'members' => $members,
            'date' => $date,
            'filters' => $filters,
            'rows' => $board['rows'],
            'stats' => $board['stats'],
            'pendingApprovals' => $board['pendingApprovals'],
            'latenessLabels' => \App\Domains\Hr\Models\EmployeeAttendanceRecord::latenessDecisionLabels(),
        ]);
    }
}
