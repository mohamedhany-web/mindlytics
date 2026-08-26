<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesKpiTarget;
use App\Models\User;
use App\Services\SalesKpiService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
        $memberIds = $this->teamService->memberUserIds($team, Auth::user());

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
            ->with('employeeJob:id,code,name')
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

    public function targets(Request $request, SalesKpiService $kpi): View
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team, $manager);

        $salesReps = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $yearMonth = (string) $request->get('year_month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rep = $salesReps->first();
        if ($salesReps->isNotEmpty()) {
            $uid = (int) $request->get('user_id', $salesReps->first()->id);
            $rep = $salesReps->firstWhere('id', $uid) ?? $salesReps->first();
        }

        $monthCarbon = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
        $merged = $rep
            ? $kpi->mergedTargets($rep, $monthCarbon)
            : config('sales_kpi.defaults');

        $savedRow = $rep
            ? SalesKpiTarget::query()
                ->where('user_id', $rep->id)
                ->where('year_month', $yearMonth)
                ->first()
            : null;

        $achievement = null;
        $dailyResults = null;
        if ($rep) {
            $achievement = $kpi->buildReport(
                $rep,
                $monthCarbon->isCurrentMonth() ? now() : $monthCarbon->copy()->endOfMonth()
            );
            $dailyResults = app(\App\Services\SalesDailyResultService::class)->comparisonFor(
                $rep,
                $monthCarbon->isCurrentMonth() ? today() : $monthCarbon
            );
        }

        $configuredIds = SalesKpiTarget::query()
            ->where('year_month', $yearMonth)
            ->whereIn('user_id', $salesReps->pluck('id'))
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $teamTargetStatus = $salesReps->map(function (User $r) use ($configuredIds, $kpi, $monthCarbon) {
            $configured = in_array((int) $r->id, $configuredIds, true);
            $report = $kpi->buildReport(
                $r,
                $monthCarbon->isCurrentMonth() ? now() : $monthCarbon->copy()->endOfMonth()
            );

            return [
                'user' => $r,
                'configured' => $configured,
                'composite' => $report['composite_month'] ?? $report['composite'] ?? 0,
            ];
        })->values();

        return view('employee.sales-manager.kpi.targets', [
            'team' => $team,
            'salesReps' => $salesReps,
            'userId' => $rep?->id,
            'yearMonth' => $yearMonth,
            'targets' => $merged,
            'rep' => $rep,
            'hasCustomTargets' => (bool) $savedRow,
            'achievement' => $achievement,
            'dailyResults' => $dailyResults,
            'teamTargetStatus' => $teamTargetStatus,
            'requiredKeys' => config('sales_kpi.required_on_save', array_keys(config('sales_kpi.defaults', []))),
            'labels' => $this->targetLabels(),
            'groups' => $this->targetGroups(),
        ]);
    }

    public function updateTargets(Request $request): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team, $manager);

        $requiredKeys = config('sales_kpi.required_on_save', array_keys(config('sales_kpi.defaults', [])));
        $targetRules = [];
        foreach ($requiredKeys as $key) {
            $targetRules[$key] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate(array_merge([
            'user_id' => ['required', 'integer', Rule::in($memberIds)],
            'year_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'apply_to_all_team' => ['nullable', 'boolean'],
        ], $targetRules), collect($requiredKeys)->mapWithKeys(
            fn ($key) => [$key.'.required' => 'هذا الهدف مطلوب — الأرقام ملزمة للموظف.']
        )->all());

        $rep = User::query()->findOrFail($validated['user_id']);
        if (! $rep->isSalesEmployee()) {
            return back()->withErrors(['user_id' => 'المستخدم ليس موظف مبيعات.'])->withInput();
        }

        $keys = array_keys(config('sales_kpi.defaults', []));
        $payload = [];
        foreach ($keys as $key) {
            if ($request->has($key) && $request->input($key) !== '' && $request->input($key) !== null) {
                $payload[$key] = (float) $request->input($key);
            }
        }

        if (isset($payload['call_attempts_daily'])) {
            $payload['calls_daily'] = $payload['call_attempts_daily'];
        }
        if (isset($payload['discovery_sessions_daily'])) {
            $payload['meetings_daily'] = $payload['discovery_sessions_daily'];
        }

        $saveFor = [$rep];
        if ($request->boolean('apply_to_all_team')) {
            $saveFor = User::query()
                ->whereIn('id', $memberIds)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->filter(fn (User $u) => $u->isSalesEmployee())
                ->all();
        }

        foreach ($saveFor as $employee) {
            SalesKpiTarget::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'year_month' => $validated['year_month'],
                ],
                ['targets' => $payload]
            );
        }

        $count = count($saveFor);

        return redirect()
            ->route('employee.sales-manager.kpi.targets', [
                'user_id' => $rep->id,
                'year_month' => $validated['year_month'],
            ])
            ->with('success', $count > 1
                ? "تم حفظ أهداف KPI لـ {$count} موظفين."
                : 'تم حفظ أهداف KPI للموظف.');
    }

    /**
     * @return array<string, string>
     */
    private function targetLabels(): array
    {
        return [
            'leads_daily' => 'Leads جديدة مسنودة/اليوم',
            'leads_weekly' => 'Leads جديدة / أسبوع',
            'deals_weekly' => 'صفقات فوز مؤكدة / أسبوع',
            'revenue_monthly' => 'إيراد شهري أدنى (ج.م)',
            'calls_daily' => 'مكالمات موثّقة / يوم',
            'meetings_daily' => 'اجتماعات (اترك 0 إن غير محاسب)',
            'followups_daily' => 'متابعات موثّقة / يوم',
            'people_worked_daily' => 'أشخاص تم العمل عليهم / يوم',
            'call_attempts_daily' => 'محاولات اتصال / يوم',
            'calls_answered_daily' => 'مكالمات تم الرد / يوم',
            'qualified_conversations_daily' => 'محادثات مؤهلة / يوم',
            'discovery_sessions_daily' => 'جلسات اكتشاف (اترك 0)',
            'proposals_daily' => 'عروض سعر / يوم',
            'paid_enrollments_daily' => 'تسجيلات مدفوعة / يوم',
            'response_minutes_max' => 'أقصى متوسط أول رد (دقيقة)',
            'closing_ratio_pct_min' => 'أدنى نسبة إغلاق %',
            'csat_min' => 'أدنى متوسط CSAT',
            'loss_ratio_max_pct' => 'أقصى نسبة خسارة %',
            'open_opportunities_min' => 'أدنى فرص مفتوحة',
            'sales_cycle_max_days' => 'أقصى دورة بيع (يوم)',
            'crm_activities_daily_min' => 'أدنى أنشطة CRM / يوم',
            'data_fresh_open_pct_min' => 'أدنى % فرص محدّثة خلال 7 أيام',
            'engagement_days_pct_min' => 'أدنى % أيام بتفاعل',
            'conversion_pct_target' => 'هدف تحويل %',
        ];
    }

    /**
     * @return array<string, array{icon: string, keys: list<string>}>
     */
    private function targetGroups(): array
    {
        return [
            'قمع النتائج اليومي (SOS)' => [
                'icon' => 'fas fa-phone-volume text-teal-600',
                'keys' => ['people_worked_daily', 'call_attempts_daily', 'calls_answered_daily', 'qualified_conversations_daily', 'proposals_daily', 'paid_enrollments_daily'],
            ],
            'النشاط' => [
                'icon' => 'fas fa-bolt text-amber-600',
                'keys' => ['leads_daily', 'leads_weekly', 'calls_daily', 'followups_daily', 'crm_activities_daily_min'],
            ],
            'النتائج والإيراد' => [
                'icon' => 'fas fa-coins text-emerald-600',
                'keys' => ['deals_weekly', 'revenue_monthly', 'closing_ratio_pct_min', 'conversion_pct_target'],
            ],
            'الجودة والالتزام' => [
                'icon' => 'fas fa-star text-sky-600',
                'keys' => ['response_minutes_max', 'csat_min', 'loss_ratio_max_pct', 'sales_cycle_max_days', 'engagement_days_pct_min'],
            ],
            'الأنبوب' => [
                'icon' => 'fas fa-filter text-violet-600',
                'keys' => ['open_opportunities_min', 'data_fresh_open_pct_min', 'meetings_daily', 'discovery_sessions_daily'],
            ],
        ];
    }
}
