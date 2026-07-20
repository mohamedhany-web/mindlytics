<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesCourseCommissionResolver;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerCommissionController extends Controller
{
    public function __construct(private SalesTeamService $teamService)
    {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);

        $view = $request->query('view', 'month');
        if (! in_array($view, ['month', 'all'], true)) {
            $view = 'month';
        }
        $yearMonth = (string) $request->query('year_month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rangeStart = null;
        $rangeEnd = null;
        if ($view === 'month') {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->endOfMonth();
        }

        $reps = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rows = [];
        $totals = [
            'confirmed_wins' => 0,
            'commission_from_leads' => 0.0,
            'pending_wins' => 0,
            'agreements' => 0,
        ];

        foreach ($reps as $rep) {
            $confirmedQ = SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->whereNotNull('won_confirmed_at');
            if ($rangeStart && $rangeEnd) {
                $confirmedQ->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]);
            }
            $confirmedWins = (int) (clone $confirmedQ)->count();
            $commission = (float) (clone $confirmedQ)->sum('commission_amount');

            $pending = SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->where('stage', 'won')
                ->whereNull('won_confirmed_at')
                ->count();

            $agrCount = $rep->salesCourseCommissionAgreements()->where('is_active', true)->count();

            $rows[] = [
                'rep' => $rep,
                'confirmed_wins' => $confirmedWins,
                'commission_from_leads' => $commission,
                'pending_wins' => $pending,
                'agreements' => $agrCount,
                'label' => $rep->salesCommissionLabel(),
            ];

            $totals['confirmed_wins'] += $confirmedWins;
            $totals['commission_from_leads'] += $commission;
            $totals['pending_wins'] += $pending;
            $totals['agreements'] += $agrCount;
        }

        $periodLabel = $view === 'all'
            ? 'كل الفترات'
            : ($rangeStart ? $rangeStart->copy()->locale('ar')->translatedFormat('F Y') : '');

        return view('employee.sales-manager.commissions.index', compact(
            'team', 'rows', 'totals', 'view', 'yearMonth', 'periodLabel'
        ));
    }

    public function show(Request $request, User $employee): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);
        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $view = $request->query('view', 'all');
        if (! in_array($view, ['month', 'all'], true)) {
            $view = 'all';
        }
        $yearMonth = (string) $request->query('year_month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rangeStart = null;
        $rangeEnd = null;
        if ($view === 'month') {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->endOfMonth();
        }

        $agreements = $employee->salesCourseCommissionAgreements()
            ->with(['advancedCourse:id,title,price', 'offlineCourse:id,title,price', 'legacyCourse:id,title,price'])
            ->orderByDesc('id')
            ->get();

        $confirmedQ = SalesLead::query()
            ->where('assigned_to', $employee->id)
            ->where('stage', 'won')
            ->whereNotNull('won_confirmed_at')
            ->with(['advancedCourse:id,title', 'offlineCourse:id,title', 'legacyCourse:id,title']);
        if ($rangeStart && $rangeEnd) {
            $confirmedQ->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]);
        }
        $confirmedLeads = (clone $confirmedQ)->orderByDesc('won_confirmed_at')->get();

        $pendingLeads = SalesLead::query()
            ->where('assigned_to', $employee->id)
            ->where('stage', 'won')
            ->whereNull('won_confirmed_at')
            ->with(['advancedCourse:id,title', 'offlineCourse:id,title', 'legacyCourse:id,title'])
            ->orderByDesc('closed_at')
            ->get();

        $resolver = app(SalesCourseCommissionResolver::class);
        $pendingEstimates = [];
        foreach ($pendingLeads as $pl) {
            $pendingEstimates[$pl->id] = $resolver->quoteForLead($employee, $pl)['total'];
        }

        $stats = [
            'confirmed_wins' => $confirmedLeads->count(),
            'commission_from_leads' => (float) $confirmedLeads->sum('commission_amount'),
            'pending_wins' => $pendingLeads->count(),
            'pending_estimated' => round(array_sum($pendingEstimates), 2),
            'agreements' => $agreements->where('is_active', true)->count(),
        ];

        $periodLabel = $view === 'all'
            ? 'كل الفترات'
            : ($rangeStart ? $rangeStart->copy()->locale('ar')->translatedFormat('F Y') : '');

        return view('employee.sales-manager.commissions.show', compact(
            'team',
            'employee',
            'agreements',
            'confirmedLeads',
            'pendingLeads',
            'pendingEstimates',
            'stats',
            'view',
            'yearMonth',
            'periodLabel'
        ));
    }
}
