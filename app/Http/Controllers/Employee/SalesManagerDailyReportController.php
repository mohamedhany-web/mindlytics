<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CampaignDailyReport;
use App\Models\SalesDailyReport;
use App\Models\SalesTeamDailyReport;
use App\Services\CampaignReportService;
use App\Services\SalesTeamService;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesManagerDailyReportController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team, $user);

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(14)->startOfDay();
        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $reports = SalesDailyReport::query()
            ->with(['user:id,name'])
            ->whereIn('user_id', $memberIds)
            ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'submitted' => SalesDailyReport::query()
                ->whereIn('user_id', $memberIds)
                ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', SalesDailyReport::STATUS_SUBMITTED)
                ->count(),
            'pending_review' => SalesDailyReport::query()
                ->whereIn('user_id', $memberIds)
                ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
                ->where('status', SalesDailyReport::STATUS_SUBMITTED)
                ->whereNull('manager_reviewed_at')
                ->count(),
        ];

        return view('employee.sales-manager.daily-reports.index', compact('reports', 'team', 'from', 'to', 'stats'));
    }

    public function show(SalesDailyReport $report, CampaignReportService $campaigns)
    {
        $this->authorizeTeamReport($report);
        $report->load(['user', 'contacts.lead']);

        $report->forceFill([
            'manager_reviewed_at' => now(),
            'manager_reviewed_by' => Auth::id(),
        ])->save();

        $campaignEntries = collect();
        if ($campaigns->tablesReady() && Schema::hasTable('campaign_daily_reports')) {
            $campaignEntries = CampaignDailyReport::query()
                ->with(['campaign:id,name,platform'])
                ->where('user_id', $report->user_id)
                ->whereDate('report_date', $report->report_date?->toDateString())
                ->orderBy('id')
                ->get();
        }

        return view('employee.sales-manager.daily-reports.show', compact('report', 'campaignEntries'));
    }

    public function teamReports(Request $request)
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        if (! $team->exists) {
            return redirect()
                ->route('employee.sales-manager.dashboard')
                ->with('error', 'لا يوجد فريق مبيعات بعد لرفع تقرير الفريق.');
        }

        $reports = SalesTeamDailyReport::query()
            ->where('sales_team_id', $team->id)
            ->orderByDesc('report_date')
            ->paginate(15)
            ->withQueryString();

        return view('employee.sales-manager.team-reports.index', compact('reports', 'team'));
    }

    public function editTeamReport(Request $request)
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        if (! $team->exists) {
            return redirect()
                ->route('employee.sales-manager.dashboard')
                ->with('error', 'لا يوجد فريق مبيعات بعد لرفع تقرير الفريق.');
        }
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        $report = SalesTeamDailyReport::firstOrNew([
            'sales_team_id' => $team->id,
            'report_date' => $date->toDateString(),
        ]);

        if ($report->exists && $report->isSubmitted()) {
            return redirect()
                ->route('employee.sales-manager.team-reports.index')
                ->with('error', 'تم تسليم تقرير هذا اليوم مسبقاً.');
        }

        $memberIds = $this->teamService->memberUserIds($team, $user);
        $memberReports = SalesDailyReport::query()
            ->with('user:id,name')
            ->whereIn('user_id', $memberIds)
            ->whereDate('report_date', $date->toDateString())
            ->where('status', SalesDailyReport::STATUS_SUBMITTED)
            ->get();

        if (! $report->exists) {
            $report->manager_id = Auth::id();
            $report->team_members_count = count($memberIds);
            $report->reports_received = $memberReports->count();
            $report->total_calls = (int) $memberReports->sum('calls_made');
            $report->total_leads_qualified = (int) $memberReports->sum('leads_qualified');
            $report->total_bookings = (int) $memberReports->sum('bookings_from_leads');
        }

        return view('employee.sales-manager.team-reports.edit', compact('report', 'team', 'date', 'memberReports'));
    }

    public function storeTeamReport(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        if (! $team->exists) {
            return redirect()
                ->route('employee.sales-manager.dashboard')
                ->with('error', 'لا يوجد فريق مبيعات بعد لرفع تقرير الفريق.');
        }
        $date = Carbon::parse($request->input('report_date', today()->toDateString()))->startOfDay();

        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'team_summary' => ['required', 'string', 'max:5000'],
            'performance_notes' => ['nullable', 'string', 'max:5000'],
            'challenges' => ['nullable', 'string', 'max:5000'],
            'recommendations' => ['nullable', 'string', 'max:5000'],
            'submit' => ['nullable', 'boolean'],
        ]);

        $report = SalesTeamDailyReport::firstOrNew([
            'sales_team_id' => $team->id,
            'report_date' => $date->toDateString(),
        ]);

        if ($report->exists && $report->isSubmitted()) {
            throw ValidationException::withMessages([
                'report_date' => 'تم تسليم تقرير هذا اليوم مسبقاً.',
            ]);
        }

        $memberIds = $this->teamService->memberUserIds($team, $user);
        $memberReports = SalesDailyReport::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('report_date', $date->toDateString())
            ->where('status', SalesDailyReport::STATUS_SUBMITTED)
            ->get();

        $submit = $request->boolean('submit');

        $report->fill([
            'manager_id' => Auth::id(),
            'team_members_count' => count($memberIds),
            'reports_received' => $memberReports->count(),
            'total_calls' => (int) $memberReports->sum('calls_made'),
            'total_leads_qualified' => (int) $memberReports->sum('leads_qualified'),
            'total_bookings' => (int) $memberReports->sum('bookings_from_leads'),
            'team_summary' => $validated['team_summary'],
            'performance_notes' => $validated['performance_notes'] ?? null,
            'challenges' => $validated['challenges'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'status' => $submit ? SalesTeamDailyReport::STATUS_SUBMITTED : SalesTeamDailyReport::STATUS_DRAFT,
            'submitted_at' => $submit ? now() : null,
        ]);
        $report->save();

        $message = $submit
            ? 'تم تسليم تقرير الفريق للإدارة.'
            : 'تم حفظ مسودة تقرير الفريق.';

        return redirect()
            ->route('employee.sales-manager.team-reports.index')
            ->with('success', $message);
    }

    private function authorizeTeamReport(SalesDailyReport $report): void
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team, $user);

        if (! in_array((int) $report->user_id, $memberIds, true)) {
            abort(403);
        }
    }
}
