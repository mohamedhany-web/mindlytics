<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerFollowUpController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        $members = $team->members()->with('user:id,name')->get();

        $filter = $request->get('filter', 'overdue');
        if (! in_array($filter, ['overdue', 'today', 'week', 'none', 'stale', 'all'], true)) {
            $filter = 'overdue';
        }

        $staleDays = SalesLead::STALE_CONTACT_DAYS;
        $base = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->openPipeline()
            ->with(['assignee:id,name', 'category']);

        $counts = [
            'overdue' => (clone $base)->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())->count(),
            'today' => (clone $base)->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())->count(),
            'week' => (clone $base)->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '>=', now()->startOfDay())
                ->where('next_follow_up_at', '<=', now()->addDays(7)->endOfDay())
                ->count(),
            'none' => (clone $base)->whereNull('next_follow_up_at')->count(),
            'stale' => (clone $base)->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->count(),
            'all' => (clone $base)->count(),
        ];

        $query = clone $base;

        if ($request->filled('assignee') && in_array((int) $request->assignee, $memberIds, true)) {
            $query->where('assigned_to', (int) $request->assignee);
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        match ($filter) {
            'overdue' => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())
                ->orderBy('next_follow_up_at'),
            'today' => $query->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())
                ->orderBy('next_follow_up_at'),
            'week' => $query->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '>=', now()->startOfDay())
                ->where('next_follow_up_at', '<=', now()->addDays(7)->endOfDay())
                ->orderBy('next_follow_up_at'),
            'none' => $query->whereNull('next_follow_up_at')->orderByDesc('updated_at'),
            'stale' => $query->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->orderByRaw('last_contacted_at IS NULL DESC')->orderBy('last_contacted_at'),
            default => $query->orderByRaw('next_follow_up_at IS NULL ASC')->orderBy('next_follow_up_at'),
        };

        $leads = $query->paginate(40)->withQueryString();

        $byMember = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->openPipeline()
            ->selectRaw('assigned_to')
            ->selectRaw('SUM(CASE WHEN next_follow_up_at IS NOT NULL AND next_follow_up_at < ? THEN 1 ELSE 0 END) as overdue_count', [now()])
            ->selectRaw('SUM(CASE WHEN next_follow_up_at IS NOT NULL AND DATE(next_follow_up_at) = ? THEN 1 ELSE 0 END) as today_count', [today()->toDateString()])
            ->selectRaw('SUM(CASE WHEN next_follow_up_at IS NULL THEN 1 ELSE 0 END) as none_count')
            ->selectRaw('SUM(CASE WHEN (last_contacted_at IS NULL AND created_at < ?) OR last_contacted_at < ? THEN 1 ELSE 0 END) as stale_count', [
                now()->subDays($staleDays),
                now()->subDays($staleDays),
            ])
            ->groupBy('assigned_to')
            ->get()
            ->keyBy('assigned_to');

        return view('employee.sales-manager.follow-ups.index', compact(
            'leads',
            'counts',
            'filter',
            'members',
            'team',
            'staleDays',
            'byMember',
        ));
    }
}
