<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CampaignDailyReport;
use App\Models\SalesDailyReport;
use App\Models\SalesKpiTarget;
use App\Services\CampaignReportService;
use App\Services\SalesManagerHubService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesManagerDashboardController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesManagerHubService $hub,
        private CampaignReportService $campaigns,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team, $user);

        $date = $request->filled('date') && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('date'))
            ? Carbon::parse($request->query('date'))->startOfDay()
            : today();

        $compareA = $request->integer('compare_a') ?: null;
        $compareB = $request->integer('compare_b') ?: null;
        if ($compareA && ! in_array($compareA, $memberIds, true)) {
            $compareA = null;
        }
        if ($compareB && ! in_array($compareB, $memberIds, true)) {
            $compareB = null;
        }

        $hub = $this->hub->build($team, $memberIds, $date, $compareA, $compareB);

        $reportsHub = $this->buildReportsHub($memberIds, $date);

        return view('employee.sales-manager.dashboard', [
            'team' => $team,
            'hub' => $hub,
            'date' => $date,
            'compareA' => $compareA,
            'compareB' => $compareB,
            'reportsHub' => $reportsHub,
        ]);
    }

    /**
     * @param  list<int>  $memberIds
     * @return array<string, mixed>
     */
    private function buildReportsHub(array $memberIds, Carbon $date): array
    {
        $today = $date->toDateString();
        $from7 = $date->copy()->subDays(6)->toDateString();
        $yearMonth = $date->format('Y-m');

        $pendingDaily = SalesDailyReport::query()
            ->whereIn('user_id', $memberIds)
            ->where('status', SalesDailyReport::STATUS_SUBMITTED)
            ->whereNull('manager_reviewed_at')
            ->count();

        $submittedToday = SalesDailyReport::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('report_date', $today)
            ->where('status', SalesDailyReport::STATUS_SUBMITTED)
            ->count();

        $campaignReady = $this->campaigns->tablesReady();
        $campaignTotals = [
            'messages' => 0,
            'qualified' => 0,
            'converted' => 0,
            'entries' => 0,
        ];

        if ($campaignReady && $memberIds !== []) {
            $rows = CampaignDailyReport::query()
                ->whereIn('user_id', $memberIds)
                ->whereBetween('report_date', [$from7, $today])
                ->get(['new_messages', 'qualified', 'converted']);

            $campaignTotals = [
                'messages' => (int) $rows->sum('new_messages'),
                'qualified' => (int) $rows->sum('qualified'),
                'converted' => (int) $rows->sum('converted'),
                'entries' => $rows->count(),
            ];
        }

        $targetsConfigured = $memberIds === []
            ? 0
            : SalesKpiTarget::query()
                ->where('year_month', $yearMonth)
                ->whereIn('user_id', $memberIds)
                ->pluck('user_id')
                ->unique()
                ->count();

        return [
            'pending_daily' => $pendingDaily,
            'submitted_today' => $submittedToday,
            'members' => count($memberIds),
            'campaign_ready' => $campaignReady,
            'campaign' => $campaignTotals,
            'targets_configured' => $targetsConfigured,
            'year_month' => $yearMonth,
        ];
    }
}
