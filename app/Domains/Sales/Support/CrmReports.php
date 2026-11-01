<?php

namespace App\Domains\Sales\Support;

use App\Domains\Sales\Http\Controllers\Crm\SalesCrmController;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * تقارير CRM (Reports في SuiteCRM). كل تقرير بيرجع جدول + رسم سريع، محسوب من sales_leads و sales_activities
 * و sales_planned_activities، ومقيّد بنطاق الدور (CrmScope): الموظف بياناته، المدير فريقه، الأدمن الكل.
 *
 * @phpstan-type Report array{columns: array<string, string>, rows: list<array<string, mixed>>, totals: ?array<string, mixed>, chart: array{label: string, key: string, by: string}, note: string}
 */
final class CrmReports
{
    public const REPORTS = [
        'performance' => ['label' => 'أداء الموظفين', 'icon' => 'fa-user-check', 'desc' => 'عملاء جداد، مكالمات، رد، محادثات مؤهلة، اجتماعات، وصفقات فازت لكل موظف.'],
        'daily' => ['label' => 'النشاط اليومي', 'icon' => 'fa-chart-line', 'desc' => 'المكالمات والرد والفوز يوم بيوم في الفترة.'],
        'funnel' => ['label' => 'قمع المراحل', 'icon' => 'fa-filter', 'desc' => 'العملاء اللي دخلوا في الفترة: وصلوا لحد فين دلوقتي.'],
        'sources' => ['label' => 'المصادر', 'icon' => 'fa-bullhorn', 'desc' => 'كل مصدر جاب كام عميل، وكام منهم اتحوّل لطالب.'],
        'activities' => ['label' => 'الأنشطة المخططة', 'icon' => 'fa-tasks', 'desc' => 'اللي اتجدول، واتنفّذ، وما اتنفّذش، والمتأخر لكل موظف.'],
        'deals' => ['label' => 'الصفقات والإيراد', 'icon' => 'fa-coins', 'desc' => 'الصفقات اللي فازت في الفترة، قيمتها وعمولتها، حسب الكورس.'],
        'lost' => ['label' => 'أسباب الخسارة', 'icon' => 'fa-times-circle', 'desc' => 'العملاء اللي اتقفلوا خسارة في الفترة، وليه.'],
    ];

    public function __construct(
        private CrmScope $scope,
        private Carbon $from,
        private Carbon $to,
        private ?int $userId = null,
    ) {}

    /** @return Report */
    public function build(string $key): array
    {
        return match ($key) {
            'performance' => $this->performance(),
            'daily' => $this->daily(),
            'funnel' => $this->funnel(),
            'sources' => $this->sources(),
            'activities' => $this->plannedActivities(),
            'deals' => $this->deals(),
            'lost' => $this->lost(),
        };
    }

    private function leads(): Builder
    {
        return $this->scope->leads()->when($this->userId, fn ($q) => $q->where('assigned_to', $this->userId));
    }

    /** الأنشطة اللي سجّلها موظفو النطاق (بغض النظر عن صاحب العميل). */
    private function logged(): Builder
    {
        $ids = $this->userId ? [$this->userId] : $this->scope->assigneeIds;

        return SalesActivity::query()
            ->when($ids !== null, fn ($q) => $q->whereIn('user_id', $ids === [] ? [0] : $ids))
            ->whereBetween('created_at', [$this->from, $this->to]);
    }

    private function members(): Collection
    {
        $members = $this->scope->members();

        return $this->userId ? $members->where('id', $this->userId)->values() : $members;
    }

    private function wonLeads(): Builder
    {
        return $this->leads()->whereIn('stage', SalesLead::WON_LIKE_STAGES)->whereBetween('closed_at', [$this->from, $this->to]);
    }

    private static function pct(int|float $part, int|float $whole): float
    {
        return $whole > 0 ? round($part / $whole * 100, 1) : 0.0;
    }

    private function performance(): array
    {
        $answered = "'".implode("','", SalesActivity::ANSWERED_OUTCOMES)."'";
        $qualified = "'".implode("','", SalesActivity::QUALIFIED_OUTCOMES)."'";

        $new = $this->leads()->whereBetween('created_at', [$this->from, $this->to])
            ->selectRaw('assigned_to, COUNT(*) c')->groupBy('assigned_to')->pluck('c', 'assigned_to');
        $acts = $this->logged()->where('type', '!=', 'stage_change')
            ->selectRaw("user_id, COUNT(DISTINCT sales_lead_id) worked, SUM(type = 'call') calls, SUM(type = 'call' AND outcome IN ($answered)) answered,
                SUM(type = 'call' AND outcome IN ($qualified)) qualified, SUM(type = 'meeting') meetings")
            ->groupBy('user_id')->get()->keyBy('user_id');
        $won = $this->wonLeads()->selectRaw('assigned_to, COUNT(*) c, SUM(COALESCE(expected_value, 0)) v')->groupBy('assigned_to')->get()->keyBy('assigned_to');

        $rows = $this->members()->map(function ($m) use ($new, $acts, $won) {
            $a = $acts->get($m->id);
            $w = $won->get($m->id);
            $calls = (int) ($a->calls ?? 0);
            $newCount = (int) ($new[$m->id] ?? 0);

            return [
                'name' => $m->name,
                'new' => $newCount,
                'worked' => (int) ($a->worked ?? 0),
                'calls' => $calls,
                'answered' => (int) ($a->answered ?? 0),
                'answer_rate' => self::pct((int) ($a->answered ?? 0), $calls),
                'qualified' => (int) ($a->qualified ?? 0),
                'meetings' => (int) ($a->meetings ?? 0),
                'won' => (int) ($w->c ?? 0),
                'won_value' => round((float) ($w->v ?? 0), 2),
                'conversion' => self::pct((int) ($w->c ?? 0), $newCount),
            ];
        })->sortByDesc('won_value')->values()->all();

        $sum = fn ($k) => array_sum(array_column($rows, $k));
        $totals = $rows === [] ? null : [
            'name' => 'الإجمالي', 'new' => $sum('new'), 'worked' => $sum('worked'), 'calls' => $sum('calls'), 'answered' => $sum('answered'),
            'answer_rate' => self::pct($sum('answered'), $sum('calls')), 'qualified' => $sum('qualified'), 'meetings' => $sum('meetings'),
            'won' => $sum('won'), 'won_value' => round($sum('won_value'), 2), 'conversion' => self::pct($sum('won'), $sum('new')),
        ];

        return [
            'columns' => ['name' => 'الموظف', 'new' => 'عملاء جداد', 'worked' => 'اشتغل على', 'calls' => 'مكالمات', 'answered' => 'رد', 'answer_rate' => 'نسبة الرد ٪',
                'qualified' => 'محادثات مؤهلة', 'meetings' => 'اجتماعات', 'won' => 'فاز', 'won_value' => 'قيمة الفوز', 'conversion' => 'التحويل ٪'],
            'rows' => $rows,
            'totals' => $totals,
            'chart' => ['label' => 'قيمة الفوز', 'key' => 'won_value', 'by' => 'name'],
            'note' => '«فاز» = عميل وصل لتسجيل مكتمل واتقفل في الفترة. «اشتغل على» = عملاء مختلفين سجّل عليهم نشاط. التحويل = فاز ÷ عملاء جداد.',
        ];
    }

    private function daily(): array
    {
        $answered = "'".implode("','", SalesActivity::ANSWERED_OUTCOMES)."'";
        $acts = $this->logged()->where('type', '!=', 'stage_change')
            ->selectRaw("DATE(created_at) d, SUM(type = 'call') calls, SUM(type = 'call' AND outcome IN ($answered)) answered, SUM(type = 'meeting') meetings, COUNT(*) total")
            ->groupBy('d')->get()->keyBy('d');
        $new = $this->leads()->whereBetween('created_at', [$this->from, $this->to])->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd');
        $won = $this->wonLeads()->selectRaw('DATE(closed_at) d, COUNT(*) c')->groupBy('d')->pluck('c', 'd');

        $rows = [];
        for ($d = $this->from->copy()->startOfDay(); $d->lte($this->to); $d->addDay()) {
            $k = $d->toDateString();
            $a = $acts->get($k);
            $rows[] = ['day' => $d->format('d/m').' '.$d->translatedFormat('D'), 'new' => (int) ($new[$k] ?? 0), 'total' => (int) ($a->total ?? 0),
                'calls' => (int) ($a->calls ?? 0), 'answered' => (int) ($a->answered ?? 0), 'meetings' => (int) ($a->meetings ?? 0), 'won' => (int) ($won[$k] ?? 0)];
        }
        $sum = fn ($k) => array_sum(array_column($rows, $k));

        return [
            'columns' => ['day' => 'اليوم', 'new' => 'عملاء جداد', 'total' => 'كل الأنشطة', 'calls' => 'مكالمات', 'answered' => 'رد', 'meetings' => 'اجتماعات', 'won' => 'فاز'],
            'rows' => $rows,
            'totals' => ['day' => 'الإجمالي', 'new' => $sum('new'), 'total' => $sum('total'), 'calls' => $sum('calls'), 'answered' => $sum('answered'), 'meetings' => $sum('meetings'), 'won' => $sum('won')],
            'chart' => ['label' => 'المكالمات', 'key' => 'calls', 'by' => 'day'],
            'note' => 'الأنشطة محسوبة باسم اللي سجّلها؛ العملاء الجداد والفوز باسم صاحب العميل.',
        ];
    }

    private function funnel(): array
    {
        $cohort = $this->leads()->whereBetween('created_at', [$this->from, $this->to]);
        $byStage = (clone $cohort)->selectRaw('stage, COUNT(*) c, SUM(COALESCE(expected_value, 0)) v')->groupBy('stage')->get()->keyBy('stage');
        $total = (int) $byStage->sum('c');

        $rows = [];
        foreach (SalesCrmController::STAGE_GROUPS as $group) {
            foreach ($group['stages'] as $stage) {
                $r = $byStage->get($stage);
                $rows[] = ['group' => $group['label'], 'stage' => SalesLead::stageLabel($stage), 'count' => (int) ($r->c ?? 0),
                    'share' => self::pct((int) ($r->c ?? 0), $total), 'value' => round((float) ($r->v ?? 0), 2)];
            }
        }

        return [
            'columns' => ['group' => 'المجموعة', 'stage' => 'المرحلة الحالية', 'count' => 'عدد', 'share' => 'النسبة ٪', 'value' => 'القيمة المتوقعة'],
            'rows' => $rows,
            'totals' => ['group' => 'الإجمالي', 'stage' => '', 'count' => $total, 'share' => $total ? 100.0 : 0.0, 'value' => round((float) $byStage->sum('v'), 2)],
            'chart' => ['label' => 'عدد العملاء', 'key' => 'count', 'by' => 'stage'],
            'note' => 'العملاء اللي اتسجّلوا في الفترة، موزّعين على مرحلتهم دلوقتي.',
        ];
    }

    private function sources(): array
    {
        $won = "'".implode("','", SalesLead::WON_LIKE_STAGES)."'";
        $data = $this->leads()->whereBetween('created_at', [$this->from, $this->to])
            ->selectRaw("source, COUNT(*) c, SUM(stage IN ($won)) w, SUM(stage = 'lost') l, SUM(CASE WHEN stage IN ($won) THEN COALESCE(expected_value, 0) ELSE 0 END) v")
            ->groupBy('source')->get();

        $rows = $data->map(fn ($r) => ['source' => SalesLead::sourceLabel((string) $r->source), 'leads' => (int) $r->c, 'won' => (int) $r->w,
            'lost' => (int) $r->l, 'conversion' => self::pct((int) $r->w, (int) $r->c), 'value' => round((float) $r->v, 2)])
            ->sortByDesc('leads')->values()->all();

        return [
            'columns' => ['source' => 'المصدر', 'leads' => 'عملاء', 'won' => 'اتحوّلوا لطلاب', 'lost' => 'خسارة', 'conversion' => 'التحويل ٪', 'value' => 'قيمة الفوز'],
            'rows' => $rows,
            'totals' => $rows === [] ? null : ['source' => 'الإجمالي', 'leads' => (int) $data->sum('c'), 'won' => (int) $data->sum('w'), 'lost' => (int) $data->sum('l'),
                'conversion' => self::pct((int) $data->sum('w'), (int) $data->sum('c')), 'value' => round((float) $data->sum('v'), 2)],
            'chart' => ['label' => 'عملاء', 'key' => 'leads', 'by' => 'source'],
            'note' => 'العملاء اللي اتسجّلوا في الفترة حسب مصدرهم، وحالتهم دلوقتي.',
        ];
    }

    private function plannedActivities(): array
    {
        $ids = $this->userId ? [$this->userId] : $this->scope->assigneeIds;
        $data = SalesPlannedActivity::query()
            ->when($ids !== null, fn ($q) => $q->whereIn('assigned_to', $ids === [] ? [0] : $ids))
            ->whereBetween('starts_at', [$this->from, $this->to])
            ->selectRaw("assigned_to, COUNT(*) total, SUM(status = 'done') done, SUM(status = 'missed') missed, SUM(status = 'cancelled') cancelled,
                SUM(status = 'planned' AND starts_at < ?) overdue, SUM(status = 'planned' AND starts_at >= ?) upcoming, SUM(kind = 'call') calls, SUM(kind = 'meeting') meetings", [now(), now()])
            ->groupBy('assigned_to')->get()->keyBy('assigned_to');

        $rows = $this->members()->map(function ($m) use ($data) {
            $r = $data->get($m->id);
            $due = (int) ($r->done ?? 0) + (int) ($r->missed ?? 0) + (int) ($r->overdue ?? 0);

            return ['name' => $m->name, 'total' => (int) ($r->total ?? 0), 'calls' => (int) ($r->calls ?? 0), 'meetings' => (int) ($r->meetings ?? 0),
                'done' => (int) ($r->done ?? 0), 'missed' => (int) ($r->missed ?? 0), 'overdue' => (int) ($r->overdue ?? 0), 'upcoming' => (int) ($r->upcoming ?? 0),
                'cancelled' => (int) ($r->cancelled ?? 0), 'completion' => self::pct((int) ($r->done ?? 0), $due)];
        })->sortByDesc('total')->values()->all();
        $sum = fn ($k) => array_sum(array_column($rows, $k));

        return [
            'columns' => ['name' => 'الموظف', 'total' => 'اتجدول', 'calls' => 'مكالمات', 'meetings' => 'اجتماعات', 'done' => 'اتنفّذ', 'missed' => 'ما اتنفّذش',
                'overdue' => 'متأخر', 'upcoming' => 'جاي', 'cancelled' => 'اتلغى', 'completion' => 'الالتزام ٪'],
            'rows' => $rows,
            'totals' => $rows === [] ? null : ['name' => 'الإجمالي', 'total' => $sum('total'), 'calls' => $sum('calls'), 'meetings' => $sum('meetings'), 'done' => $sum('done'),
                'missed' => $sum('missed'), 'overdue' => $sum('overdue'), 'upcoming' => $sum('upcoming'), 'cancelled' => $sum('cancelled'),
                'completion' => self::pct($sum('done'), $sum('done') + $sum('missed') + $sum('overdue'))],
            'chart' => ['label' => 'الالتزام ٪', 'key' => 'completion', 'by' => 'name'],
            'note' => 'الأنشطة اللي موعدها في الفترة. الالتزام = اتنفّذ ÷ (اتنفّذ + ما اتنفّذش + متأخر).',
        ];
    }

    private function deals(): array
    {
        $won = $this->wonLeads()->with(['assignee:id,name', 'advancedCourse:id,title', 'offlineCourse:id,title', 'legacyCourse:id,title'])
            ->get(['id', 'assigned_to', 'expected_value', 'commission_amount', 'commission_reversed_amount', 'won_confirmed_at', 'course_type', 'advanced_course_id', 'offline_course_id', 'course_id']);

        $rows = $won->groupBy(fn ($l) => ($l->assignee?->name ?? '—').'|'.($l->linkedCourseTitle() ?? 'بدون كورس'))
            ->map(function ($g, $key) {
                [$member, $course] = explode('|', $key, 2);

                return ['name' => $member, 'course' => $course, 'count' => $g->count(),
                    'value' => round((float) $g->sum('expected_value'), 2),
                    'confirmed' => $g->whereNotNull('won_confirmed_at')->count(),
                    'commission' => round((float) $g->sum(fn ($l) => (float) $l->commission_amount - (float) ($l->commission_reversed_amount ?? 0)), 2),
                    'reversed' => round((float) $g->sum('commission_reversed_amount'), 2)];
            })->sortByDesc('value')->values()->all();
        $sum = fn ($k) => array_sum(array_column($rows, $k));

        return [
            'columns' => ['name' => 'الموظف', 'course' => 'الكورس', 'count' => 'صفقات', 'value' => 'القيمة', 'confirmed' => 'معتمدة', 'commission' => 'العمولة الصافية', 'reversed' => 'مسترجع'],
            'rows' => $rows,
            'totals' => $rows === [] ? null : ['name' => 'الإجمالي', 'course' => '', 'count' => $sum('count'), 'value' => round($sum('value'), 2), 'confirmed' => $sum('confirmed'),
                'commission' => round($sum('commission'), 2), 'reversed' => round($sum('reversed'), 2)],
            'chart' => ['label' => 'القيمة', 'key' => 'value', 'by' => 'course'],
            'note' => 'القيمة = المدفوع فعلاً للصفقات المربوطة بطلب. العمولة الصافية بعد أي استرجاع.',
        ];
    }

    private function lost(): array
    {
        $data = $this->leads()->where('stage', 'lost')->whereBetween('closed_at', [$this->from, $this->to])
            ->selectRaw('lost_reason, COUNT(*) c')->groupBy('lost_reason')->pluck('c', 'lost_reason');
        $total = (int) $data->sum();

        $rows = $data->map(fn ($c, $reason) => ['reason' => SalesLead::LOSS_REASONS[$reason] ?? ($reason ?: 'بدون سبب'), 'count' => (int) $c, 'share' => self::pct((int) $c, $total)])
            ->sortByDesc('count')->values()->all();

        return [
            'columns' => ['reason' => 'السبب', 'count' => 'عدد', 'share' => 'النسبة ٪'],
            'rows' => $rows,
            'totals' => $rows === [] ? null : ['reason' => 'الإجمالي', 'count' => $total, 'share' => 100.0],
            'chart' => ['label' => 'عدد', 'key' => 'count', 'by' => 'reason'],
            'note' => 'العملاء اللي اتقفلوا «خسارة» في الفترة.',
        ];
    }
}
