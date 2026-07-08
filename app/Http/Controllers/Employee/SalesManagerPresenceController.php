<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeePresenceViolation;
use App\Services\EmployeePresenceService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesManagerPresenceController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private EmployeePresenceService $presence,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $board = $this->presence->teamPresenceBoard($memberIds);

        $stats = [
            'online' => $board->where('status', 'online')->count(),
            'away' => $board->where('status', 'away')->count(),
            'offline' => $board->whereIn('status', ['offline', 'logged_out'])->count(),
            'not_clocked_in' => $board->where('status', 'not_clocked_in')->count(),
            'completed' => $board->where('status', 'shift_completed')->count(),
        ];

        $date = $request->filled('date') ? Carbon::parse($request->date) : today();

        $violations = EmployeePresenceViolation::query()
            ->with('user:id,name')
            ->whereIn('user_id', $memberIds)
            ->whereDate('work_date', $date->toDateString())
            ->orderByDesc('started_at')
            ->paginate(25)
            ->withQueryString();

        $violationStats = [
            'total' => EmployeePresenceViolation::query()
                ->whereIn('user_id', $memberIds)
                ->whereDate('work_date', $date->toDateString())
                ->count(),
            'open' => EmployeePresenceViolation::query()
                ->whereIn('user_id', $memberIds)
                ->whereDate('work_date', $date->toDateString())
                ->open()
                ->count(),
        ];

        return view('employee.sales-manager.presence.index', compact(
            'team',
            'board',
            'stats',
            'violations',
            'violationStats',
            'date'
        ));
    }

    public function poll(Request $request)
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        $board = $this->presence->teamPresenceBoard($memberIds);

        return response()->json([
            'success' => true,
            'board' => $board->values(),
            'stats' => [
                'online' => $board->where('status', 'online')->count(),
                'away' => $board->where('status', 'away')->count(),
                'offline' => $board->whereIn('status', ['offline', 'logged_out'])->count(),
                'not_clocked_in' => $board->where('status', 'not_clocked_in')->count(),
            ],
            'checked_at' => now()->toIso8601String(),
        ]);
    }

    public function acknowledge(EmployeePresenceViolation $violation): RedirectResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        abort_unless(in_array((int) $violation->user_id, $memberIds, true), 403);

        $violation->update([
            'acknowledged_by' => Auth::id(),
            'acknowledged_at' => now(),
        ]);

        return back()->with('success', 'تم تسجيل مراجعة المخالفة.');
    }
}
