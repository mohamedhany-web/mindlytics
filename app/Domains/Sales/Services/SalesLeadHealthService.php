<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\CrmScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * حالة العملاء (docs/SALES_CRM.md): كل عميل مفتوح عنده مشكلة ولا لأ، ولكل موظف.
 * نفس التعريفات دي هي اللي بتفلتر قايمة العملاء لما تضغط على أي رقم.
 */
class SalesLeadHealthService
{
    /** أنواع النشاط اللي تتحسب تواصل حقيقي مع العميل (الملاحظة وتغيير المرحلة لأ). */
    public const CONTACT_TYPES = ['call', 'meeting', 'follow_up', 'whatsapp', 'messenger', 'email'];

    /** نشاط مخطط أو ما اتمش ما يتحسبش تواصل. */
    public const NOT_DONE_STATUSES = ['planned', 'not_held', 'deferred'];

    /** @var array<string, array{0: string, 1: string}> المفتاح => [الاسم، الشرح] */
    public const BUCKETS = [
        'untouched' => ['ما اتكلمش خالص', 'عميل مفتوح محدش سجّل معاه أي تواصل لحد دلوقتي.'],
        'overdue' => ['متابعة متأخرة', 'ميعاد المتابعة الجاية عدّى ومحدش حدّثه.'],
        'no_next' => ['من غير خطوة جاية', 'عميل مفتوح مالوش ميعاد متابعة، فممكن يتنسي.'],
        'stale' => ['راكد', 'مفيش تواصل حقيقي معاه من '.SalesLead::STALE_CONTACT_DAYS.' أيام أو أكتر.'],
        'pending_win' => ['صفقة مستنية اعتماد', 'الموظف قفلها كسب ومستنية اعتماد المدير.'],
    ];

    /** المشاكل اللي بتخلّي العميل «مش سليم». */
    public const PROBLEMS = ['untouched', 'overdue', 'no_next', 'stale'];

    /** يطبّق فلتر الحالة على query العملاء. */
    public static function apply(Builder $query, string $bucket): Builder
    {
        if ($bucket === 'pending_win') {
            return $query->pendingWinApproval();
        }

        $query->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES);

        return match ($bucket) {
            'untouched' => self::whereUntouched($query),
            'overdue' => $query->where('next_follow_up_at', '<', now()),
            'no_next' => $query->whereNull('next_follow_up_at'),
            'stale' => self::whereStale($query),
            default => $query,
        };
    }

    /** عملاء مفتوحين عندهم أي مشكلة من PROBLEMS. */
    public static function applyAnyProblem(Builder $query): Builder
    {
        return $query->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)->where(function (Builder $w) {
            $w->where(fn (Builder $q) => self::whereUntouched($q))
                ->orWhere('next_follow_up_at', '<', now())
                ->orWhereNull('next_follow_up_at')
                ->orWhere(fn (Builder $q) => self::whereStale($q));
        });
    }

    private static function whereUntouched(Builder $query): Builder
    {
        return $query->whereNull('last_contacted_at')->whereDoesntHave('activities', fn ($a) => self::contactActivity($a));
    }

    private static function whereStale(Builder $query): Builder
    {
        $cutoff = now()->subDays(SalesLead::STALE_CONTACT_DAYS);

        return $query->where('created_at', '<', $cutoff)
            ->where(fn ($q) => $q->whereNull('last_contacted_at')->orWhere('last_contacted_at', '<', $cutoff))
            ->whereDoesntHave('activities', fn ($a) => self::contactActivity($a)->where('created_at', '>=', $cutoff));
    }

    private static function contactActivity($activities)
    {
        return $activities->reorder()->whereIn('type', self::CONTACT_TYPES)
            ->where(fn ($s) => $s->whereNull('status')->orWhereNotIn('status', self::NOT_DONE_STATUSES));
    }

    /** النشاط ده يتحسب تواصل حقيقي؟ (عشان «آخر تواصل» يتحدّث من شاشة الأنشطة في الـ CRM كمان) */
    public static function isContact(string $type, ?string $status): bool
    {
        return in_array($type, self::CONTACT_TYPES, true) && ! in_array((string) $status, self::NOT_DONE_STATUSES, true);
    }

    /**
     * @return array{rows: list<array<string, mixed>>, totals: array<string, int>, stages: array<string, string>, unassigned: int|null}
     */
    public function board(CrmScope $scope): array
    {
        $members = $scope->members();
        $ids = $members->pluck('id')->map(fn ($id) => (int) $id)->all();

        $grouped = fn (Builder $q) => $q->whereIn('assigned_to', $ids === [] ? [0] : $ids)
            ->select('assigned_to', DB::raw('COUNT(*) as n'))->groupBy('assigned_to')->pluck('n', 'assigned_to')->map(fn ($n) => (int) $n);

        $open = $grouped($scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES));
        $problem = $grouped(self::applyAnyProblem($scope->leads()));
        $buckets = [];
        foreach (array_keys(self::BUCKETS) as $bucket) {
            $buckets[$bucket] = $grouped(self::apply($scope->leads(), $bucket));
        }

        $stageCounts = $scope->leads()->openPipeline()->whereIn('assigned_to', $ids === [] ? [0] : $ids)
            ->select('assigned_to', 'stage', DB::raw('COUNT(*) as n'))->groupBy('assigned_to', 'stage')->get()
            ->groupBy('assigned_to')->map(fn ($g) => $g->pluck('n', 'stage')->map(fn ($n) => (int) $n));
        $usedStages = $stageCounts->flatMap(fn ($g) => $g->keys())->unique()->all();
        $stages = array_filter(SalesLead::STAGES, fn ($k) => in_array($k, $usedStages, true), ARRAY_FILTER_USE_KEY);

        $contactsToday = SalesActivity::query()->whereIn('user_id', $ids === [] ? [0] : $ids)->whereNotNull('sales_lead_id')
            ->whereIn('type', self::CONTACT_TYPES)->where(fn ($s) => $s->whereNull('status')->orWhereNotIn('status', self::NOT_DONE_STATUSES))
            ->where('created_at', '>=', today())
            ->select('user_id', DB::raw('COUNT(*) as n'))->groupBy('user_id')->pluck('n', 'user_id');
        $lastActivity = SalesActivity::query()->whereIn('user_id', $ids === [] ? [0] : $ids)->whereNotNull('sales_lead_id')
            ->where('type', '!=', 'stage_change')
            ->select('user_id', DB::raw('MAX(created_at) as at'))->groupBy('user_id')->pluck('at', 'user_id');

        $rows = $members->map(function ($m) use ($open, $problem, $buckets, $stageCounts, $contactsToday, $lastActivity) {
            $id = (int) $m->id;
            $openN = $open[$id] ?? 0;
            $row = [
                'id' => $id,
                'name' => $m->name,
                'open' => $openN,
                'healthy_pct' => $openN > 0 ? (int) round(100 * ($openN - ($problem[$id] ?? 0)) / $openN) : null,
                'contacts_today' => (int) ($contactsToday[$id] ?? 0),
                'last_activity_at' => isset($lastActivity[$id]) ? \Illuminate\Support\Carbon::parse($lastActivity[$id]) : null,
                'stages' => ($stageCounts[$id] ?? collect())->all(),
            ];
            foreach ($buckets as $bucket => $counts) {
                $row[$bucket] = $counts[$id] ?? 0;
            }

            return $row;
        })
            // الأسوأ فوق: أقل نسبة سليمة، وبعدين الأكتر عملاء
            ->sortBy(fn ($r) => [$r['healthy_pct'] ?? 101, -$r['open']])
            ->values()->all();

        $totals = ['open' => array_sum(array_column($rows, 'open')), 'contacts_today' => array_sum(array_column($rows, 'contacts_today'))];
        foreach (array_keys(self::BUCKETS) as $bucket) {
            $totals[$bucket] = array_sum(array_column($rows, $bucket));
        }
        $totals['problem'] = array_sum($problem->all());
        $totals['healthy_pct'] = $totals['open'] > 0 ? (int) round(100 * ($totals['open'] - $totals['problem']) / $totals['open']) : null;

        return [
            'rows' => $rows,
            'totals' => $totals,
            'stages' => $stages,
            'unassigned' => $scope->isAdmin()
                ? SalesLead::query()->openPipeline()->where(fn ($q) => $q->whereNull('assigned_to')->orWhere('assigned_to', 0))->count()
                : null,
        ];
    }
}
