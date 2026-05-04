<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SalesLead;
use App\Models\SalesKpiTarget;
use App\Models\User;
use App\Services\SalesKpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesKpiController extends Controller
{
    public function index(Request $request, SalesKpiService $kpi)
    {
        $period = (string) $request->get('period', 'today');
        if (! in_array($period, ['today', '7d', 'month'], true)) {
            $period = 'today';
        }

        $rangeStart = match ($period) {
            '7d' => now()->subDays(6)->startOfDay(),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };
        $rangeEnd = match ($period) {
            '7d' => now()->endOfDay(),
            'month' => now()->endOfMonth(),
            default => now()->endOfDay(),
        };

        $periodLabel = match ($period) {
            '7d' => 'آخر 7 أيام',
            'month' => 'هذا الشهر',
            default => 'اليوم',
        };

        $rows = $kpi->adminOverview();
        $slaSummary = [
            'overdue_followups' => (int) collect($rows)->sum('overdue_followups'),
            'stale_open_leads' => (int) collect($rows)->sum('stale_open_leads'),
            'avg_response_minutes' => round((float) collect($rows)
                ->pluck('avg_response_minutes')
                ->filter(fn ($v) => $v !== null)
                ->avg(), 1),
        ];

        $lossReasons = SalesLead::query()
            ->where('stage', 'lost')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->whereNotNull('lost_reason')
            ->where('lost_reason', '!=', '')
            ->selectRaw('lost_reason, COUNT(*) as total')
            ->groupBy('lost_reason')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $sourceCreated = SalesLead::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $sourceWon = SalesLead::query()
            ->where('stage', 'won')
            ->whereNotNull('closed_at')
            ->whereBetween('closed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('source, COUNT(*) as total, COALESCE(SUM(expected_value), 0) as revenue')
            ->groupBy('source')
            ->get()
            ->keyBy('source');

        $sourcePerformance = collect(SalesLead::SOURCES)->map(function ($label, $source) use ($sourceCreated, $sourceWon) {
            $created = (int) ($sourceCreated[$source] ?? 0);
            $won = (int) ($sourceWon[$source]->total ?? 0);
            $revenue = (float) ($sourceWon[$source]->revenue ?? 0);
            $conversion = $created > 0 ? round(($won / $created) * 100, 1) : null;

            return [
                'source' => $source,
                'label' => $label,
                'created' => $created,
                'won' => $won,
                'conversion' => $conversion,
                'revenue' => $revenue,
            ];
        })->filter(fn ($row) => $row['created'] > 0 || $row['won'] > 0)
            ->sortByDesc(fn ($row) => [$row['conversion'] ?? 0, $row['won'], $row['created']])
            ->values();

        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $periodReminderNotifications = Notification::query()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->where('title', 'تذكير المتابعات اليومية - المبيعات')
            ->whereIn('user_id', $salesReps->pluck('id')->all())
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id');

        $reminderMonitoringRows = $salesReps->map(function ($rep) use ($periodReminderNotifications) {
            $repNotifs = $periodReminderNotifications->get($rep->id, collect());
            $notif = $repNotifs->first();
            $sentCount = $repNotifs->count();
            $overdueAtReminder = (int) data_get($notif?->data, 'overdue_followups', 0);
            $todayAtReminder = (int) data_get($notif?->data, 'today_followups', 0);
            $staleAtReminder = (int) data_get($notif?->data, 'stale_open_leads', 0);

            $currentOverdue = SalesLead::query()->forAssignee((int) $rep->id)->openPipeline()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count();

            $compliancePct = null;
            if ($sentCount > 0) {
                if ($overdueAtReminder <= 0) {
                    $compliancePct = 100.0;
                } else {
                    $resolved = max(0, $overdueAtReminder - $currentOverdue);
                    $compliancePct = round(($resolved / $overdueAtReminder) * 100, 1);
                }
            }

            return [
                'rep' => $rep,
                'sent_count' => $sentCount,
                'overdue_at_reminder' => $overdueAtReminder,
                'today_at_reminder' => $todayAtReminder,
                'stale_at_reminder' => $staleAtReminder,
                'current_overdue' => $currentOverdue,
                'compliance_pct' => $compliancePct,
            ];
        })->values();

        $reminderMonitoringSummary = [
            'sent_total' => (int) $reminderMonitoringRows->sum('sent_count'),
            'reps_with_alerts' => (int) $reminderMonitoringRows->filter(fn ($r) => $r['sent_count'] > 0 && $r['overdue_at_reminder'] > 0)->count(),
            'avg_compliance_pct' => round((float) $reminderMonitoringRows
                ->pluck('compliance_pct')
                ->filter(fn ($v) => $v !== null)
                ->avg(), 1),
        ];

        return view('admin.sales.kpi.index', compact(
            'rows',
            'slaSummary',
            'lossReasons',
            'sourcePerformance',
            'reminderMonitoringRows',
            'reminderMonitoringSummary',
            'period',
            'periodLabel',
            'rangeStart',
            'rangeEnd'
        ));
    }

    public function targets(Request $request)
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $yearMonth = $request->get('year_month', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rep = $salesReps->first();
        if ($salesReps->isNotEmpty()) {
            $uid = (int) $request->get('user_id', $salesReps->first()->id);
            $rep = $salesReps->firstWhere('id', $uid) ?? $salesReps->first();
        }

        $merged = $rep
            ? app(SalesKpiService::class)->mergedTargets($rep, \Carbon\Carbon::createFromFormat('Y-m-d', $yearMonth.'-01'))
            : config('sales_kpi.defaults');

        return view('admin.sales.kpi.targets', [
            'salesReps' => $salesReps,
            'userId' => $rep?->id,
            'yearMonth' => $yearMonth,
            'targets' => $merged,
            'rep' => $rep,
        ]);
    }

    public function updateTargets(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'year_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'sales_commission_mode' => ['nullable', Rule::in(['none', 'percent', 'fixed'])],
            'sales_commission_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $rep = User::query()->findOrFail($validated['user_id']);
        if (! $rep->isSalesEmployee()) {
            return back()->withErrors(['user_id' => 'المستخدم ليس موظف مبيعات.'])->withInput();
        }

        $keys = array_keys(config('sales_kpi.defaults', []));
        $existing = SalesKpiTarget::where('user_id', $rep->id)
            ->where('year_month', $validated['year_month'])
            ->value('targets');
        $existing = is_array($existing) ? $existing : [];

        $payload = $existing;
        foreach ($keys as $key) {
            if ($request->has($key) && $request->input($key) !== '' && $request->input($key) !== null) {
                $payload[$key] = (float) $request->input($key);
            }
        }

        SalesKpiTarget::updateOrCreate(
            [
                'user_id' => $rep->id,
                'year_month' => $validated['year_month'],
            ],
            ['targets' => $payload]
        );

        // تحديث إعداد الكوميشن على مستوى الموظف (ليس شهرياً)
        if ($request->has('sales_commission_mode')) {
            $mode = (string) ($validated['sales_commission_mode'] ?? 'none');
            $val = $validated['sales_commission_value'] ?? null;
            $rep->forceFill([
                'sales_commission_mode' => $mode,
                'sales_commission_value' => $mode === 'none' ? null : (float) ($val ?? 0),
            ])->save();
        }

        \App\Services\SalesAuditService::log(
            'sales_kpi_targets_updated',
            $rep,
            null,
            [
                'year_month' => $validated['year_month'],
                'keys' => array_keys($payload),
                'sales_commission_mode' => $rep->sales_commission_mode ?? null,
                'sales_commission_value' => $rep->sales_commission_value ?? null,
            ],
            'تحديث أهداف KPIs مبيعات للموظف: '.($rep->name ?? '').' — '.$validated['year_month'].' — بواسطة '.(Auth::user()->name ?? '')
        );

        return redirect()
            ->route('admin.sales.kpi.targets', ['user_id' => $rep->id, 'year_month' => $validated['year_month']])
            ->with('success', 'تم حفظ الأهداف.');
    }
}
