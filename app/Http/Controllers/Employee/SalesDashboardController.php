<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Services\SalesDailyResultService;
use App\Services\SalesDayBlockService;
use App\Services\SalesKpiService;
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
            'new' => (clone $base)->where('stage', 'new_lead')->count(),
            'active' => $open()->count(),
            'won' => (clone $base)->where('stage', SalesLead::WON_STAGE)->count(),
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
            'won_month_value' => (float) (clone $base)->where('stage', SalesLead::WON_STAGE)
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

        $slaCutoffHours = 24;
        $noFirstResponseLeads = $open()
            ->whereNull('last_contacted_at')
            ->where('created_at', '<=', now()->subHours($slaCutoffHours))
            ->orderBy('created_at')
            ->limit(6)
            ->get();

        $taskQueue = collect()
            ->merge($overdueLeads->map(fn ($lead) => [
                'lead' => $lead,
                'priority' => 1,
                'reason' => 'متابعة متأخرة',
                'next_action' => 'اتصال فوري وتحديث موعد المتابعة',
            ]))
            ->merge($noFirstResponseLeads->map(fn ($lead) => [
                'lead' => $lead,
                'priority' => 2,
                'reason' => 'تجاوز SLA لأول رد',
                'next_action' => 'أول تواصل الآن (مكالمة أو واتساب)',
            ]))
            ->merge($staleLeads->map(fn ($lead) => [
                'lead' => $lead,
                'priority' => 3,
                'reason' => 'Lead راكد بلا تفاعل',
                'next_action' => 'إعادة تنشيط العميل بعرض/قيمة جديدة',
            ]))
            ->sortBy('priority')
            ->unique(fn ($item) => $item['lead']->id)
            ->take(10)
            ->values();

        $kpiQuick = app(SalesKpiService::class)->buildReport($user);
        $dailyResults = app(SalesDailyResultService::class)->comparisonFor($user);
        $dayBlock = app(SalesDayBlockService::class)->snapshot();

        return view('employee.sales.dashboard', compact(
            'stats',
            'funnel',
            'followupsToday',
            'recentLeads',
            'overdueLeads',
            'staleLeads',
            'taskQueue',
            'noFirstResponseLeads',
            'slaCutoffHours',
            'kpiQuick',
            'dailyResults',
            'dayBlock'
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
