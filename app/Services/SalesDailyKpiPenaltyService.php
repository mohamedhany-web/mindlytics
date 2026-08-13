<?php

namespace App\Services;

use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesDailyKpiPenalty;
use App\Models\User;
use App\Support\PenaltyWindow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * خصم تلقائي عند عدم تحقيق KPI اليومي الموثّق من CRM (بدون اجتماعات).
 */
class SalesDailyKpiPenaltyService
{
    public function __construct(
        private SalesDailyResultService $dailyResults,
        private SalesDailyReportService $dailyReports,
        private SalesNotificationService $notifications,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return (array) config('sales_kpi.daily_kpi_penalty', []);
    }

    public function enabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * @return array<string, array{amount: float, title: string}>
     */
    public function chargeableMetrics(): array
    {
        $metrics = (array) ($this->settings()['metrics'] ?? []);
        $out = [];
        foreach ($metrics as $key => $cfg) {
            if (! is_array($cfg)) {
                continue;
            }
            $out[$key] = [
                'amount' => (float) ($cfg['amount'] ?? 0),
                'title' => (string) ($cfg['title'] ?? 'غرامة KPI يومي'),
            ];
        }

        return $out;
    }

    public function thresholdPct(): float
    {
        return (float) ($this->settings()['threshold_pct'] ?? 70);
    }

    public function isPenaltyDueForDate(Carbon $date): bool
    {
        if ($date->isFuture()) {
            return false;
        }

        if ($date->isToday()) {
            $time = (string) ($this->settings()['deadline_time'] ?? '23:59');
            [$h, $m] = array_pad(explode(':', $time), 2, '0');

            return now()->greaterThan($date->copy()->setTime((int) $h, (int) $m, 59));
        }

        return true;
    }

    /**
     * تطبيق خصومات KPI ليوم واحد لموظف.
     *
     * @return list<EmployeeSalaryDeduction>
     */
    public function applyForDate(User $employee, Carbon $date): array
    {
        if (! $this->enabled() || ! Schema::hasTable('sales_daily_kpi_penalties')) {
            return [];
        }

        if (! $employee->isSalesEmployee() || ! $employee->is_active) {
            return [];
        }

        $date = $date->copy()->startOfDay();

        if (! $this->isPenaltyDueForDate($date)) {
            return [];
        }

        if (($this->settings()['work_days_only'] ?? true) && ! $this->dailyReports->isWorkDay($date, $employee)) {
            return [];
        }

        if (! PenaltyWindow::isChargeable(PenaltyWindow::SALES_DAILY_KPI, $employee, $date)) {
            return [];
        }

        $comparison = $this->dailyResults->comparisonFor($employee, $date);
        $threshold = $this->thresholdPct();
        $chargeable = $this->chargeableMetrics();
        $created = [];

        $linesByKey = collect($comparison['lines'] ?? [])->keyBy('key');

        foreach ($chargeable as $metricKey => $cfg) {
            if (($cfg['amount'] ?? 0) <= 0) {
                continue;
            }

            $line = $linesByKey->get($metricKey);
            if (! $line) {
                continue;
            }

            $target = (float) ($line['target'] ?? 0);
            if ($target <= 0) {
                continue; // مؤشر غير مطلوب (مثل الاجتماعات)
            }

            $pct = (float) ($line['pct'] ?? 0);
            if ($pct >= $threshold) {
                continue;
            }

            $deduction = $this->createPenaltyIfNeeded(
                $employee,
                $date,
                $metricKey,
                (float) ($line['actual'] ?? 0),
                $target,
                $pct,
                $cfg
            );

            if ($deduction) {
                $created[] = $deduction;
            }
        }

        return $created;
    }

    /**
     * @param  array{amount: float, title: string}  $cfg
     */
    private function createPenaltyIfNeeded(
        User $employee,
        Carbon $date,
        string $metricKey,
        float $actual,
        float $target,
        float $pct,
        array $cfg,
    ): ?EmployeeSalaryDeduction {
        // whereDate يتجنّب مشاكل مقارنة التاريخ مع SQLite/casts
        $row = SalesDailyKpiPenalty::query()
            ->where('user_id', $employee->id)
            ->whereDate('work_date', $date->toDateString())
            ->where('metric_key', $metricKey)
            ->first();

        if ($row?->waived_at) {
            return null;
        }

        // موجود مسبقاً — لا نعيد إنشاء خصم ولا نُعدّه «جديداً»
        if ($row?->deduction_id) {
            return null;
        }

        $agreement = EmployeeAgreement::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->first();

        $label = $this->metricLabel($metricKey);
        $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber([
            'employee_id' => $employee->id,
            'agreement_id' => $agreement?->id,
            'title' => $cfg['title'],
            'description' => 'عدم تحقيق هدف «'.$label.'» ليوم '.$date->toDateString()
                .' — الفعلي '.number_format($actual, 0).' / الهدف '.number_format($target, 0)
                .' ('.number_format($pct, 0).'٪، الحد الأدنى '.number_format($this->thresholdPct(), 0).'٪)',
            'amount' => $cfg['amount'],
            'type' => in_array(($this->settings()['penalty_type'] ?? 'penalty'), ['tax', 'insurance', 'loan', 'penalty', 'other'], true)
                ? $this->settings()['penalty_type']
                : 'penalty',
            'deduction_date' => $date->toDateString(),
            'status' => 'applied',
            'notes' => 'KPI يومي مبيعات — '.$metricKey.' — '.$date->toDateString(),
            'created_by' => null,
        ]);

        $payload = [
            'user_id' => $employee->id,
            'work_date' => $date->toDateString(),
            'metric_key' => $metricKey,
            'actual' => $actual,
            'target' => $target,
            'pct' => $pct,
            'deduction_id' => $deduction->id,
        ];

        if ($row) {
            $row->fill($payload)->save();
        } else {
            SalesDailyKpiPenalty::query()->create($payload);
        }

        $this->notifications->notifyDailyKpiPenalty($employee, $deduction, $date->toDateString(), $label);

        return $deduction;
    }

    public function metricLabel(string $key): string
    {
        return match ($key) {
            'people_worked_daily' => 'أشخاص تم العمل عليهم',
            'call_attempts_daily' => 'محاولات اتصال',
            'calls_answered_daily' => 'مكالمات تم الرد',
            'qualified_conversations_daily' => 'محادثات مؤهلة',
            'discovery_sessions_daily' => 'اجتماعات / جلسات',
            'proposals_daily' => 'عروض سعر',
            'paid_enrollments_daily' => 'تسجيلات مدفوعة',
            default => $key,
        };
    }

    /**
     * ملخص سريع لخصومات الموظف (تقرير + KPI) لفترة.
     *
     * @return array{items: list<array<string, mixed>>, total_amount: float, count: int}
     */
    public function employeeDeductionsHub(User $employee, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = ($from ?? now()->startOfMonth())->copy()->startOfDay();
        $to = ($to ?? now())->copy()->endOfDay();

        $deductions = EmployeeSalaryDeduction::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('deduction_date', [$from->toDateString(), $to->toDateString()])
            ->where(function ($q) {
                $q->where('notes', 'like', 'KPI يومي مبيعات%')
                    ->orWhere('notes', 'like', 'تقرير يومي مبيعات%')
                    ->orWhere('title', 'like', 'غرامة KPI%')
                    ->orWhere('title', 'like', '%التقرير اليومي%');
            })
            ->orderByDesc('deduction_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $items = $deductions->map(function (EmployeeSalaryDeduction $d) {
            $kind = str_contains((string) $d->notes, 'KPI يومي') || str_contains((string) $d->title, 'KPI')
                ? 'kpi'
                : 'daily_report';

            return [
                'id' => $d->id,
                'kind' => $kind,
                'kind_label' => $kind === 'kpi' ? 'KPI يومي' : 'تقرير يومي',
                'title' => $d->title,
                'amount' => (float) $d->amount,
                'date' => $d->deduction_date?->format('Y-m-d'),
                'status' => $d->status,
                'number' => $d->deduction_number,
            ];
        })->all();

        return [
            'items' => $items,
            'total_amount' => round((float) collect($items)->sum('amount'), 2),
            'count' => count($items),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ];
    }

    /**
     * تطبيق على كل المندوبين لتاريخ.
     */
    public function applyForAllReps(Carbon $date): int
    {
        $count = 0;
        $reps = User::salesEmployees()->where('is_active', true)->get();
        foreach ($reps as $rep) {
            $count += count($this->applyForDate($rep, $date));
        }

        return $count;
    }
}
