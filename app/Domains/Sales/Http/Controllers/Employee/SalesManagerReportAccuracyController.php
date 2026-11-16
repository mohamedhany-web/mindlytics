<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Domains\Sales\Models\SalesDailyReport;
use App\Domains\Sales\Services\SalesReportActualsService;
use App\Domains\Sales\Services\SalesTeamService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerReportAccuracyController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesReportActualsService $actuals,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team, $manager);

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(14)->startOfDay();
        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $employeeId = $request->filled('user_id') ? (int) $request->user_id : null;
        if ($employeeId && ! in_array($employeeId, $memberIds, true)) {
            $employeeId = null;
        }

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $scopeUsers = $employeeId
            ? $members->where('id', $employeeId)->values()
            : $members;

        $board = $this->actuals->periodBoard($scopeUsers, $from, $to);

        return view('sales::manager.report-accuracy.index', [
            'team' => $team,
            'members' => $members,
            'board' => $board,
            'from' => $from,
            'to' => $to,
            'userId' => $employeeId,
        ]);
    }

    public function show(SalesDailyReport $report): View
    {
        $this->authorizeTeamMember((int) $report->user_id);
        $report->load(['user', 'contacts.lead']);
        $diff = $this->actuals->compareReportToActuals($report);

        return view('sales::manager.report-accuracy.show', [
            'report' => $report,
            'diff' => $diff,
        ]);
    }

    private function authorizeTeamMember(int $userId): void
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team, $manager);
        abort_unless(in_array($userId, $memberIds, true), 403);
    }
}
