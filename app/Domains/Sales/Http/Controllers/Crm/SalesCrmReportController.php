<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Support\CrmReports;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * وحدة التقارير داخل CRM (Reports في SuiteCRM): نفس التقارير للثلاثة، والنطاق حسب الدور.
 * الموظف: أرقامه. المدير: فريقه، ويقدر يفلتر بموظف. الأدمن: الكل. كل تقرير يتصدّر CSV.
 */
class SalesCrmReportController extends Controller
{
    use InteractsWithCrm;

    public const RANGES = [
        'today' => 'النهارده',
        'week' => 'الأسبوع ده',
        'month' => 'الشهر ده',
        'last_month' => 'الشهر اللي فات',
        '30d' => 'آخر 30 يوم',
        'custom' => 'فترة محددة',
    ];

    public function index(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->view($scope, 'reports', 'reports.index', [
            'reports' => CrmReports::REPORTS,
            'legacy' => $this->legacyLinks($scope),
        ]);
    }

    public function show(Request $request, string $report): View|StreamedResponse
    {
        abort_unless(array_key_exists($report, CrmReports::REPORTS), 404);
        $scope = $this->scope($request);
        [$range, $from, $to] = $this->range($request);
        $userId = $scope->isRep() ? null : ($request->integer('user') ?: null);
        if ($userId && ! $scope->members()->contains('id', $userId)) {
            $userId = null;
        }

        $data = (new CrmReports($scope, $from, $to, $userId))->build($report);

        if ($request->query('export') === 'csv') {
            return $this->csv($report, $data, $from, $to);
        }

        return $this->view($scope, 'reports', 'reports.show', [
            'key' => $report,
            'meta' => CrmReports::REPORTS[$report],
            'reports' => CrmReports::REPORTS,
            'data' => $data,
            'range' => $range,
            'from' => $from,
            'to' => $to,
            'userId' => $userId,
            'members' => $scope->isRep() ? collect() : $scope->members(),
        ]);
    }

    /** @return array{0: string, 1: Carbon, 2: Carbon} */
    private function range(Request $request): array
    {
        $range = array_key_exists((string) $request->query('range'), self::RANGES) ? (string) $request->query('range') : 'month';

        [$from, $to] = match ($range) {
            'today' => [today(), today()->endOfDay()],
            'week' => [today()->startOfWeek(Carbon::SATURDAY), today()->endOfDay()],
            'last_month' => [today()->subMonthNoOverflow()->startOfMonth(), today()->subMonthNoOverflow()->endOfMonth()],
            '30d' => [today()->subDays(29), today()->endOfDay()],
            'custom' => [
                rescue(fn () => Carbon::parse((string) $request->query('from'))->startOfDay(), today()->startOfMonth(), false),
                rescue(fn () => Carbon::parse((string) $request->query('to'))->endOfDay(), today()->endOfDay(), false),
            ],
            default => [today()->startOfMonth(), today()->endOfDay()],
        };

        if ($to->lt($from)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }
        if ($from->diffInDays($to) > 366) {
            $from = $to->copy()->subDays(366)->startOfDay();
        }

        return [$range, $from, $to];
    }

    private function csv(string $key, array $data, Carbon $from, Carbon $to): StreamedResponse
    {
        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_values($data['columns']));
            foreach (array_merge($data['rows'], $data['totals'] ? [$data['totals']] : []) as $row) {
                fputcsv($out, array_map(fn ($k) => $row[$k] ?? '', array_keys($data['columns'])));
            }
            fclose($out);
        }, 'crm-'.$key.'-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** التقارير التفصيلية الموجودة قبل CRM (KPIs، التقارير اليومية…) — لسه متاحة من هنا. */
    private function legacyLinks(CrmScope $scope): array
    {
        $links = match (true) {
            $scope->isRep() => [['KPIs والأداء', 'employee.sales.kpi.index'], ['التقرير اليومي', 'employee.sales.daily-reports.index'], ['تقارير الأداء', 'employee.sales.reports.index'], ['خصومات KPI', 'employee.sales.penalties.index']],
            $scope->isManager() => [['مركز الرقابة اليومية', 'employee.sales-manager.scorecard.index'], ['KPIs والأهداف', 'employee.sales-manager.kpi.index'], ['اللوحة الحية', 'employee.sales-manager.live-board'], ['التقارير اليومية', 'employee.sales-manager.daily-reports.index'], ['تقرير الفريق للإدارة', 'employee.sales-manager.team-reports.index'], ['تقارير الكامبين', 'employee.sales-manager.campaign-reports.index']],
            default => [['تقارير المبيعات', 'admin.sales.reports.index'], ['مراقبة KPIs', 'admin.sales.kpi.index'], ['التقارير اليومية', 'admin.sales.daily-reports.index'], ['تقارير الفرق', 'admin.sales.team-daily-reports.index'], ['Insights الموظفين', 'admin.sales.insights.index'], ['تدقيق استخدام CRM', 'admin.sales.crm-compliance.index']],
        };

        return array_values(array_map(fn ($l) => ['label' => $l[0], 'url' => route($l[1])], array_filter($links, fn ($l) => Route::has($l[1]))));
    }
}
