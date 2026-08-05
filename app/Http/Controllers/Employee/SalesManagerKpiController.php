<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesKpiService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerKpiController extends Controller
{
    private const PERIODS = ['day', 'week', 'month'];

    public function __construct(private SalesTeamService $teamService)
    {
        $this->middleware('sales.manager');
    }

    public function index(Request $request, SalesKpiService $kpi): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $period = (string) $request->get('period', 'day');
        if (! in_array($period, self::PERIODS, true)) {
            $period = 'day';
        }

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        if ($date->isFuture()) {
            $date = now()->startOfDay();
        }

        [$start, $end, $periodLabel] = match ($period) {
            'week' => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek(), 'الأسبوع'],
            'month' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth(), 'الشهر'],
            default => [$date->copy()->startOfDay(), $date->copy()->endOfDay(), 'اليوم'],
        };

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->with('employeeJob:id,code,title')
            ->orderBy('name')
            ->get();

        $selectedId = $request->filled('user_id') ? (int) $request->get('user_id') : null;
        if ($selectedId && ! in_array($selectedId, $memberIds, true)) {
            $selectedId = null;
        }

        $scoped = $selectedId
            ? $members->where('id', $selectedId)
            : $members;

        $rows = $kpi->teamOverview($scoped, $start, $end);

        $summary = [
            'members' => count($rows),
            'avg_composite' => $rows === []
                ? 0.0
                : round(collect($rows)->avg(fn ($r) => $r['report']['composite']), 1),
            'revenue' => (float) collect($rows)->sum(fn ($r) => $r['report']['metrics']['revenue_closed']),
            'won' => (int) collect($rows)->sum(fn ($r) => $r['report']['metrics']['won_closed']),
            'new_leads' => (int) collect($rows)->sum(fn ($r) => $r['report']['metrics']['new_leads']),
            'calls' => (int) collect($rows)->sum(fn ($r) => $r['report']['metrics']['calls']),
            'overdue_followups' => (int) collect($rows)->sum(fn ($r) => $r['report']['metrics']['overdue_followups']),
            'stale_open_leads' => (int) collect($rows)->sum(fn ($r) => $r['report']['metrics']['stale_open_leads']),
            'below_target' => collect($rows)->filter(fn ($r) => $r['report']['composite'] < 65)->count(),
        ];

        return view('employee.sales-manager.kpi.index', [
            'team' => $team,
            'members' => $members,
            'rows' => $rows,
            'summary' => $summary,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'date' => $date,
            'start' => $start,
            'end' => $end,
            'selectedId' => $selectedId,
        ]);
    }
}
