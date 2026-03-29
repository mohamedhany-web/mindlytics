<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalesDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /** @var Builder $base */
        $base = SalesLead::query()->forAssignee($user->id);

        $open = fn (): Builder => (clone $base)->openPipeline();

        $stats = [
            'total' => (clone $base)->count(),
            'new' => (clone $base)->where('stage', 'new')->count(),
            'active' => $open()->count(),
            'won' => (clone $base)->where('stage', 'won')->count(),
            'lost' => (clone $base)->where('stage', 'lost')->count(),
            'followups_today' => $open()
                ->whereNotNull('next_follow_up_at')
                ->whereDate('next_follow_up_at', today())
                ->count(),
            'followups_overdue' => $open()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count(),
            'urgent_open' => $open()->where('priority', 'urgent')->count(),
            'stale' => $this->staleQuery(clone $base)->count(),
            'pipeline_value' => (float) $open()->whereNotNull('expected_value')->sum('expected_value'),
            'won_month_value' => (float) (clone $base)->where('stage', 'won')
                ->whereNotNull('closed_at')
                ->whereBetween('closed_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('expected_value'),
        ];

        $funnel = [];
        foreach (array_keys(SalesLead::STAGES) as $stageKey) {
            $funnel[$stageKey] = (clone $base)->where('stage', $stageKey)->count();
        }

        $followupsToday = $open()
            ->whereNotNull('next_follow_up_at')
            ->whereDate('next_follow_up_at', today())
            ->orderBy('next_follow_up_at')
            ->limit(8)
            ->get();

        $recentLeads = (clone $base)->with('assignee')->latest('updated_at')->limit(8)->get();

        $overdueLeads = $open()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->orderBy('next_follow_up_at')
            ->limit(6)
            ->get();

        $staleLeads = $this->staleQuery(clone $base)
            ->orderByRaw('last_contacted_at IS NULL DESC')
            ->orderBy('last_contacted_at')
            ->limit(6)
            ->get();

        return view('employee.sales.dashboard', compact(
            'stats',
            'funnel',
            'followupsToday',
            'recentLeads',
            'overdueLeads',
            'staleLeads'
        ));
    }

    private function staleQuery(Builder $assigneeBase): Builder
    {
        $d = SalesLead::STALE_CONTACT_DAYS;

        return $assigneeBase->openPipeline()->where(function ($q) use ($d) {
            $q->where(function ($q2) use ($d) {
                $q2->whereNull('last_contacted_at')
                    ->where('created_at', '<', now()->subDays($d));
            })->orWhere('last_contacted_at', '<', now()->subDays($d));
        });
    }
}
