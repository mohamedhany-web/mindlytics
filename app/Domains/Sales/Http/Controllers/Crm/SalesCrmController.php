<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Hr\Models\EmployeeSalaryDeduction;
use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\WhatsAppConversation;
use App\Domains\Messaging\Services\WhatsAppQueueService;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesInterestType;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesLeadTransfer;
use App\Domains\Sales\Models\SalesShiftSwapRequest;
use App\Domains\Sales\Services\SalesDailyResultService;
use App\Domains\Sales\Services\SalesLeadOrderLinker;
use App\Domains\Sales\Services\SalesLeadTransferService;
use App\Domains\Sales\Services\SalesTeamService;
use App\Domains\Sales\Support\CrmNavigation;
use App\Domains\Sales\Support\CrmScope;
use App\Domains\Sales\Support\PhoneKey;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * شاشات CRM الموحّدة لموظف المبيعات ومدير المبيعات والأدمن (docs/SALES_CRM.md).
 * نفس الشاشات للثلاثة بأنماط SuiteCRM؛ CrmScope يحدد البيانات والإجراءات.
 * كل الكتابة على العميل (تحريك المرحلة، النشاط، المتابعة) تمر بنفس الـ endpoints القديمة المختبرة.
 */
class SalesCrmController extends Controller
{
    /** مراحل الصفقة: من عرض السعر لحد التسجيل (أو أي عميل اتربط بطلب). */
    public const DEAL_STAGES = ['offer_sent', 'payment_pending', 'payment_received', SalesLead::WON_STAGE, 'upsell'];

    public const OPEN_DEAL_STAGES = ['offer_sent', 'payment_pending', 'payment_received'];

    /** تجميع المراحل للرسوم السريعة. */
    public const STAGE_GROUPS = [
        'contact' => ['label' => 'تواصل وتأهيل', 'color' => '#534D64', 'stages' => ['new_lead', 'first_contact', 'no_answer', 'connected', 'qualification']],
        'interest' => ['label' => 'مهتم / اعتراض', 'color' => '#627587', 'stages' => ['interested', 'objection', 'follow_up_scheduled']],
        'offer' => ['label' => 'عرض / دفع', 'color' => '#B8492F', 'stages' => ['offer_sent', 'payment_pending', 'payment_received']],
        'won' => ['label' => 'اتحوّل لطالب', 'color' => '#0E6B44', 'stages' => ['enrollment_completed', 'upsell']],
        'closed' => ['label' => 'خسارة / راكد', 'color' => '#9CA3AF', 'stages' => ['lost', 'dormant']],
    ];

    private const SESSION_IDS = 'sales_crm.lead_ids';

    public function __construct(
        private SalesDailyResultService $results,
        private SalesTeamService $teams,
        private SalesLeadOrderLinker $linker,
    ) {}

    // ───────────────────────── الرئيسية ─────────────────────────

    public function home(Request $request): View
    {
        $scope = $this->scope($request);
        $today = today();
        $endOfDay = $today->copy()->endOfDay();

        $followUpsQuery = $scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)
            ->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', $endOfDay);

        $data = [
            'followUps' => (clone $followUpsQuery)->with('assignee:id,name')->orderBy('next_follow_up_at')->limit(8)->get(),
            'followUpsTotal' => (clone $followUpsQuery)->count(),
            'overdueTotal' => (clone $followUpsQuery)->where('next_follow_up_at', '<', now())->count(),
            'meetings' => $scope->leads()->openPipeline()->where('follow_up_channel', 'meeting')
                ->whereBetween('next_follow_up_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->with('assignee:id,name')->orderBy('next_follow_up_at')->limit(5)->get(),
            'openDeals' => $scope->leads()->whereIn('stage', array_merge(self::OPEN_DEAL_STAGES, [SalesLead::WON_STAGE]))
                ->whereNull('won_confirmed_at')->with('assignee:id,name')->orderByDesc('updated_at')->limit(6)->get(),
            'openDealsTotal' => $scope->leads()->whereIn('stage', array_merge(self::OPEN_DEAL_STAGES, [SalesLead::WON_STAGE]))->whereNull('won_confirmed_at')->count(),
            'stream' => $this->activityQuery($scope)->with(['lead:id,name', 'user:id,name'])->latest('id')->limit(8)->get(),
        ];

        if ($scope->isRep()) {
            $data['kpi'] = $this->results->comparisonFor($scope->user, $today);
            $data['commission'] = $this->commissionSummary($scope->leads());
        } else {
            $data['team'] = $this->teamRows($scope, $today);
            $data['funnel'] = $this->funnel($data['team']);
            $data['headline'] = $this->headline($scope, $today);
            $data['decisions'] = $this->pendingDecisions($scope);
            $data['duplicates'] = $this->duplicatePhones($scope);
        }

        return $this->view($scope, 'home', 'home', $data + ['today' => $today]);
    }

    // ───────────────────────── العملاء المحتملون ─────────────────────────

    public function leads(Request $request): View
    {
        $scope = $this->scope($request);
        $filters = $this->leadFilters($request, $scope);
        $base = $this->filteredLeads($scope, $filters);

        $sortable = ['name', 'stage', 'next_follow_up_at', 'updated_at', 'source'];
        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : 'next_follow_up_at';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $ordered = (clone $base)->orderByRaw($sort === 'next_follow_up_at' ? 'next_follow_up_at IS NULL' : '1')->orderBy($sort, $dir)->orderByDesc('id');
        $leads = (clone $ordered)->with(['assignee:id,name', 'interestType:id,name_ar'])->paginate(20)->withQueryString();

        // ترتيب القائمة الحالية لأسهم «السابق / التالي» في ملف العميل
        $request->session()->put(self::SESSION_IDS, (clone $ordered)->limit(500)->pluck('id')->map(fn ($id) => (int) $id)->all());

        return $this->view($scope, 'leads', 'leads.index', [
            'leads' => $leads,
            'filters' => $filters,
            'sort' => $sort,
            'dir' => $dir,
            'charts' => $this->stageChart(clone $base),
            'quickCounts' => $this->quickCounts($scope),
            'interestTypes' => SalesInterestType::query()->orderBy('sort_order')->get(['id', 'name_ar']),
            'members' => $scope->isRep() ? collect() : $scope->members(),
        ]);
    }

    public function lead(Request $request, int $lead): View
    {
        $scope = $this->scope($request);
        $lead = $this->findLead($scope, $lead);

        return $this->view($scope, 'leads', 'leads.show', $this->recordData($request, $scope, $lead));
    }

    /** إجراءات جماعية: تصدير CSV للجميع، وتحويل لموظف للمدير والأدمن. */
    public function bulk(Request $request): RedirectResponse|StreamedResponse
    {
        $scope = $this->scope($request);
        $data = $request->validate([
            'action' => ['required', Rule::in(['export', 'transfer'])],
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['integer'],
            'to_user_id' => ['nullable', 'integer'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], ['ids.required' => 'اختر عميل واحد على الأقل.']);

        $leads = $scope->leads()->whereIn('id', $data['ids'])->with(['assignee:id,name', 'interestType:id,name_ar'])->get();
        if ($leads->isEmpty()) {
            return back()->withErrors(['ids' => 'العملاء المحددون خارج نطاقك.']);
        }

        if ($data['action'] === 'export') {
            return $this->exportCsv($leads);
        }

        abort_unless($scope->canManage(), 403);
        $toUserId = (int) ($data['to_user_id'] ?? 0);
        if (! $scope->members()->contains('id', $toUserId)) {
            throw ValidationException::withMessages(['to_user_id' => 'اختر موظف مبيعات من نطاقك.']);
        }

        $moved = 0;
        foreach ($leads as $lead) {
            if ((int) $lead->assigned_to === $toUserId) {
                continue;
            }
            $scope->isManager()
                ? $this->teams->transferLead($scope->user, $lead, $toUserId, $data['reason'] ?? null)
                : app(SalesLeadTransferService::class)->assign($lead, $toUserId, $scope->user, $data['reason'] ?? null);
            $moved++;
        }

        return back()->with('success', 'تم تحويل '.$moved.' عميل.');
    }

    // ───────────────────────── الصفقات ─────────────────────────

    public function deals(Request $request): View
    {
        $scope = $this->scope($request);
        $quick = in_array($request->query('quick'), ['open', 'won_pending', 'won_month', 'refunded', 'all'], true) ? $request->query('quick') : 'open';

        $base = $this->dealsQuery($scope);
        match ($quick) {
            'open' => $base->whereIn('stage', self::OPEN_DEAL_STAGES),
            'won_pending' => $base->pendingWinApproval(),
            'won_month' => $base->wonLike()->where('closed_at', '>=', now()->startOfMonth()),
            'refunded' => SalesLeadOrderLinker::ready() ? $base->where('commission_reversed_amount', '>', 0) : $base->whereRaw('1 = 0'),
            default => null,
        };
        if ($request->filled('q')) {
            $this->applySearch($base, (string) $request->query('q'));
        }
        if (! $scope->isRep() && $request->integer('assigned_to')) {
            $base->where('assigned_to', $request->integer('assigned_to'));
        }

        $deals = (clone $base)->with(['assignee:id,name', 'order:id,status,amount'])->orderByDesc('updated_at')->paginate(20)->withQueryString();
        $request->session()->put(self::SESSION_IDS, (clone $base)->orderByDesc('updated_at')->limit(500)->pluck('id')->map(fn ($id) => (int) $id)->all());

        $byStage = $this->dealsQuery($scope)->whereIn('stage', self::DEAL_STAGES)
            ->selectRaw('stage, COUNT(*) as c, COALESCE(SUM(expected_value), 0) as v')->groupBy('stage')->get()->keyBy('stage');

        return $this->view($scope, 'deals', 'deals.index', [
            'deals' => $deals,
            'quick' => $quick,
            'byStage' => $byStage,
            'members' => $scope->isRep() ? collect() : $scope->members(),
        ]);
    }

    public function deal(Request $request, int $lead): View
    {
        $scope = $this->scope($request);
        $lead = $this->findLead($scope, $lead);
        $data = $this->recordData($request, $scope, $lead);

        $order = $data['order'];
        $data['payment'] = $order?->payment;
        $data['invoice'] = $order?->invoice;
        $data['commissionTxns'] = Transaction::query()->where('category', 'commission')
            ->where(function (Builder $q) use ($lead) {
                $q->where('metadata->sales_lead_id', $lead->id);
                if ($lead->commission_transaction_id) {
                    $q->orWhere('id', $lead->commission_transaction_id);
                }
            })->orderBy('id')->get();

        return $this->view($scope, 'deals', 'deals.show', $data);
    }

    // ───────────────────────── تحويل لطالب (ربط بطلب) ─────────────────────────

    public function convertForm(Request $request, int $lead): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canManage(), 403);
        $lead = $this->findLead($scope, $lead);

        $students = $this->matchingStudents($lead);
        $linkedOrderIds = SalesLeadOrderLinker::ready() ? SalesLead::query()->whereNotNull('order_id')->pluck('order_id')->all() : [];
        $orders = Order::query()->with(['user:id,name,phone', 'course:id,title'])
            ->where('status', Order::STATUS_APPROVED)
            ->whereIn('user_id', $students->pluck('id')->all() ?: [0])
            ->whereNotIn('id', $linkedOrderIds ?: [0])
            ->latest('approved_at')->limit(20)->get();

        return $this->view($scope, 'leads', 'convert', [
            'lead' => $lead,
            'students' => $students,
            'orders' => $orders,
        ]);
    }

    public function convert(Request $request, int $lead): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->canManage() && SalesLeadOrderLinker::ready(), 403);
        $lead = $this->findLead($scope, $lead);
        $data = $request->validate(['order_id' => ['required', 'integer', 'exists:orders,id']], ['order_id.required' => 'اختر الطلب المدفوع.']);

        $this->linker->linkManually($lead, Order::query()->findOrFail((int) $data['order_id']), $scope->user);

        return redirect()->route($scope->route('deals.show'), $lead->id)->with('success', 'تم تحويل العميل لطالب وربطه بالطلب #'.$data['order_id'].'.');
    }

    // ───────────────────────── مساعدات ─────────────────────────

    private function scope(Request $request): CrmScope
    {
        return CrmScope::for(Auth::user(), str_starts_with((string) $request->route()?->getName(), 'admin.'));
    }

    private function view(CrmScope $scope, string $module, string $view, array $data): View
    {
        return view('sales::crm.'.$view, $data + [
            'crm' => $scope,
            'crmModule' => $module,
            'crmTabs' => CrmNavigation::tabs($scope),
            'crmQuickCreate' => CrmNavigation::quickCreate($scope),
        ]);
    }

    private function findLead(CrmScope $scope, int $id): SalesLead
    {
        $lead = SalesLead::query()->with(['assignee:id,name', 'interestType:id,name_ar', 'category:id,name', 'group:id,name'])->find($id);
        abort_unless($lead && $scope->canSee($lead), 404);

        return $lead;
    }

    /** @return array<string, mixed> */
    private function leadFilters(Request $request, CrmScope $scope): array
    {
        $quick = (string) $request->query('quick', $scope->isRep() ? 'mine' : 'all');
        $allowed = ['all', 'mine', 'today', 'overdue', 'new', 'meta', 'stale'];
        if ($scope->isAdmin()) {
            $allowed[] = 'unassigned';
        }

        return [
            'quick' => in_array($quick, $allowed, true) ? $quick : 'all',
            'q' => trim((string) $request->query('q', '')),
            'stage' => array_key_exists((string) $request->query('stage'), SalesLead::STAGES) ? (string) $request->query('stage') : '',
            'source' => array_key_exists((string) $request->query('source'), SalesLead::SOURCES) ? (string) $request->query('source') : '',
            'interest_type_id' => $request->integer('interest_type_id') ?: null,
            'assigned_to' => $scope->isRep() ? null : ($request->integer('assigned_to') ?: null),
        ];
    }

    private function filteredLeads(CrmScope $scope, array $f): Builder
    {
        $query = $f['quick'] === 'unassigned'
            ? SalesLead::query()->where(fn ($q) => $q->whereNull('assigned_to')->orWhere('assigned_to', 0))
            : $scope->leads();

        match ($f['quick']) {
            'mine' => $query->where('assigned_to', $scope->user->id),
            'today' => $query->openPipeline()->whereBetween('next_follow_up_at', [today(), today()->endOfDay()]),
            'overdue' => $query->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)->where('next_follow_up_at', '<', now()),
            'new' => $query->where('stage', 'new_lead'),
            'meta' => $query->where(fn ($q) => $q->where('source', 'social')->orWhereHas('metaSocialConversations')),
            'stale' => $query->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)->where('updated_at', '<', now()->subDays(10)),
            default => null,
        };

        if ($f['q'] !== '') {
            $this->applySearch($query, $f['q']);
        }
        foreach (['stage', 'source', 'interest_type_id', 'assigned_to'] as $key) {
            if (! empty($f[$key])) {
                $query->where($key, $f[$key]);
            }
        }

        return $query;
    }

    private function applySearch(Builder $query, string $q): void
    {
        $tail = PhoneKey::likeTail($q);
        $query->where(function (Builder $w) use ($q, $tail) {
            $w->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%')->orWhere('company', 'like', '%'.$q.'%');
            if ($tail) {
                $w->orWhere('phone', 'like', $tail);
            }
            if (ctype_digit($q) && SalesLeadOrderLinker::ready()) {
                $w->orWhere('order_id', (int) $q)->orWhere('id', (int) $q);
            }
        });
    }

    /** @return array<string, int> */
    private function quickCounts(CrmScope $scope): array
    {
        $open = fn () => $scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES);

        return [
            'today' => $scope->leads()->openPipeline()->whereBetween('next_follow_up_at', [today(), today()->endOfDay()])->count(),
            'overdue' => $open()->where('next_follow_up_at', '<', now())->count(),
            'new' => $scope->leads()->where('stage', 'new_lead')->count(),
            'stale' => $open()->where('updated_at', '<', now()->subDays(10))->count(),
            'unassigned' => $scope->isAdmin() ? SalesLead::query()->where(fn ($q) => $q->whereNull('assigned_to')->orWhere('assigned_to', 0))->count() : 0,
        ];
    }

    /** @return list<array{key: string, label: string, color: string, count: int, pct: float}> */
    private function stageChart(Builder $base): array
    {
        $counts = $base->reorder()->selectRaw('stage, COUNT(*) as c')->groupBy('stage')->pluck('c', 'stage');
        $total = max(1, (int) $counts->sum());
        $out = [];
        foreach (self::STAGE_GROUPS as $key => $group) {
            $c = (int) collect($group['stages'])->sum(fn ($s) => (int) ($counts[$s] ?? 0));
            $out[] = ['key' => $key, 'label' => $group['label'], 'color' => $group['color'], 'count' => $c, 'pct' => round($c / $total * 100, 1)];
        }

        return $out;
    }

    private function dealsQuery(CrmScope $scope): Builder
    {
        return $scope->leads()->where(function (Builder $q) {
            $q->whereIn('stage', self::DEAL_STAGES);
            if (SalesLeadOrderLinker::ready()) {
                $q->orWhereNotNull('order_id');
            }
        });
    }

    private function activityQuery(CrmScope $scope): Builder
    {
        $query = SalesActivity::query();
        if ($scope->assigneeIds !== null) {
            $ids = $scope->assigneeIds ?: [0];
            $query->where(fn ($q) => $q->whereIn('user_id', $ids)->orWhereHas('lead', fn ($l) => $l->whereIn('assigned_to', $ids)));
        }

        return $query;
    }

    /** @return array<string, mixed> */
    private function recordData(Request $request, CrmScope $scope, SalesLead $lead): array
    {
        $ids = (array) $request->session()->get(self::SESSION_IDS, []);
        $pos = array_search((int) $lead->id, $ids, true);

        $whatsapp = Schema::hasColumn((new WhatsAppConversation)->getTable(), 'sales_lead_id')
            ? WhatsAppConversation::query()->where('sales_lead_id', $lead->id)->latest('last_message_at')->limit(10)->get()
            : collect();
        $meta = MetaSocialConversation::query()->where('sales_lead_id', $lead->id)->latest('last_message_at')->limit(10)->get();

        $order = SalesLeadOrderLinker::ready() && $lead->order_id
            ? Order::query()->with(['payment', 'invoice', 'course:id,title'])->find($lead->order_id)
            : null;

        return [
            'lead' => $lead,
            'position' => $pos === false ? null : ['index' => $pos + 1, 'total' => count($ids), 'prev' => $ids[$pos - 1] ?? null, 'next' => $ids[$pos + 1] ?? null],
            'history' => $lead->activities()->with('user:id,name')->limit(15)->get(),
            'historyTotal' => $lead->activities()->count(),
            'conversations' => $whatsapp->map(fn ($c) => ['channel' => 'واتساب', 'at' => $c->last_message_at, 'status' => $c->status])
                ->concat($meta->map(fn ($c) => ['channel' => $c->platform === 'instagram' ? 'Instagram' : 'Messenger', 'at' => $c->last_message_at, 'status' => $c->status])),
            'transfers' => SalesLeadTransfer::query()->where('sales_lead_id', $lead->id)->with(['fromUser:id,name', 'toUser:id,name', 'transferredBy:id,name'])->latest('id')->limit(10)->get(),
            'order' => $order,
            'netPaid' => $order ? $this->linker->netPaid($order) : null,
            'daysInStage' => $lead->stage_entered_at ? (int) $lead->stage_entered_at->diffInDays(now()) : null,
            'members' => $scope->canManage() ? $scope->members() : collect(),
        ];
    }

    /** @return array{earned: float, pending: int, reversed: float} */
    private function commissionSummary(Builder $leads): array
    {
        $month = now()->startOfMonth();
        $won = (clone $leads)->whereNotNull('won_confirmed_at')->where('won_confirmed_at', '>=', $month)->get(['commission_amount', 'commission_reversed_amount']);

        return [
            'earned' => round((float) $won->sum(fn ($l) => (float) $l->commission_amount - (float) ($l->commission_reversed_amount ?? 0)), 2),
            'count' => $won->count(),
            'pending' => (clone $leads)->pendingWinApproval()->count(),
            'reversed' => round((float) $won->sum(fn ($l) => (float) ($l->commission_reversed_amount ?? 0)), 2),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function teamRows(CrmScope $scope, $day): array
    {
        $members = $scope->members();
        $overdue = $scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)
            ->where('next_follow_up_at', '<', now())->selectRaw('assigned_to, COUNT(*) as c')->groupBy('assigned_to')->pluck('c', 'assigned_to');

        return $members->map(function (User $m) use ($day, $overdue) {
            $cmp = $this->results->comparisonFor($m, $day);
            $metric = fn ($k) => (int) ($cmp['metrics'][$k] ?? 0);
            $target = fn ($k) => (int) ($cmp['targets'][$k] ?? 0);

            return [
                'user' => $m,
                'worked' => $metric('people_worked_daily'), 'worked_target' => $target('people_worked_daily'),
                'attempts' => $metric('call_attempts_daily'),
                'answered' => $metric('calls_answered_daily'), 'answered_target' => $target('calls_answered_daily'),
                'qualified' => $metric('qualified_conversations_daily'),
                'offers' => $metric('proposals_daily'),
                'paid' => $metric('paid_enrollments_daily'),
                'overdue' => (int) ($overdue[$m->id] ?? 0),
                'overall' => (float) ($cmp['overall_pct'] ?? 0),
            ];
        })->values()->all();
    }

    /** @return list<array{label: string, value: int}> */
    private function funnel(array $rows): array
    {
        $sum = fn ($k) => (int) array_sum(array_column($rows, $k));

        return [
            ['label' => 'اتعمل عليهم', 'value' => $sum('worked')],
            ['label' => 'محاولات اتصال', 'value' => $sum('attempts')],
            ['label' => 'رد', 'value' => $sum('answered')],
            ['label' => 'مؤهل', 'value' => $sum('qualified')],
            ['label' => 'عرض سعر', 'value' => $sum('offers')],
            ['label' => 'تسجيل مدفوع', 'value' => $sum('paid')],
        ];
    }

    /** @return array<string, float|int> */
    private function headline(CrmScope $scope, $day): array
    {
        $revenue = 0.0;
        $wins = 0;
        if (SalesLeadOrderLinker::ready()) {
            $orderIds = $scope->leads()->whereNotNull('order_id')->pluck('order_id');
            $orders = Order::query()->whereIn('id', $orderIds->all() ?: [0])->where('status', Order::STATUS_APPROVED)
                ->whereBetween('approved_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])->get(['id', 'amount', 'payment_id']);
            $revenue = (float) $orders->sum(fn (Order $o) => $this->linker->netPaid($o));
            $wins = $orders->count();
        }

        return [
            'revenue' => round($revenue, 2),
            'wins' => $wins,
            'overdue' => $scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)->where('next_follow_up_at', '<', now())->count(),
        ];
    }

    /** @return list<array{label: string, count: int, url: ?string}> */
    private function pendingDecisions(CrmScope $scope): array
    {
        $memberIds = $scope->assigneeIds ?? $scope->members()->pluck('id')->all();
        $kpiPending = Schema::hasTable('employee_salary_deductions')
            ? EmployeeSalaryDeduction::query()->where('status', 'pending')->whereIn('employee_id', $memberIds ?: [0])
                ->where(fn ($q) => $q->where('notes', 'like', 'KPI يومي مبيعات%')->orWhere('notes', 'like', 'sales_commission_reversal%'))->count()
            : 0;
        $swaps = Schema::hasTable('sales_shift_swap_requests')
            ? SalesShiftSwapRequest::query()->where('status', SalesShiftSwapRequest::STATUS_PENDING)->whereIn('requester_id', $memberIds ?: [0])->count()
            : 0;
        $route = fn (string $name) => \Illuminate\Support\Facades\Route::has($name) ? route($name) : null;

        return array_values(array_filter([
            ['label' => 'صفقات فازت بانتظار اعتماد العمولة', 'count' => $scope->leads()->pendingWinApproval()->count(),
                'url' => $scope->isAdmin() ? $route('admin.sales.win-approvals.index') : route($scope->route('deals.index'), ['quick' => 'won_pending'])],
            ['label' => 'خصومات مقترحة (KPI / استرجاع عمولة)', 'count' => $kpiPending,
                'url' => $scope->isAdmin() ? $route('admin.employee-deductions.index') : $route('employee.sales-manager.scorecard.index')],
            ['label' => 'طلبات تبديل شيفت', 'count' => $swaps,
                'url' => $scope->isAdmin() ? $route('admin.sales.shifts.index') : $route('employee.sales-manager.shift-swaps.index')],
            ['label' => 'محادثات واتساب بدون موظف', 'count' => $this->queueCount(),
                'url' => $scope->isAdmin() ? $route('admin.whatsapp.inbox') : $route('employee.sales-manager.whatsapp.queue.index')],
        ], fn ($d) => $d['count'] > 0 || $d['url'] !== null));
    }

    private function queueCount(): int
    {
        try {
            return app(WhatsAppQueueService::class)->pendingCount();
        } catch (\Throwable) {
            return 0;
        }
    }

    /** أرقام مسجّلة كعملاء نشطين عند أكثر من موظف داخل النطاق. @return list<array{key: string, leads: \Illuminate\Support\Collection}> */
    private function duplicatePhones(CrmScope $scope): array
    {
        $rows = $scope->leads()->where('stage', '!=', 'lost')->whereNotNull('assigned_to')->get(['id', 'name', 'phone', 'assigned_to']);
        $groups = $rows->groupBy(fn (SalesLead $l) => PhoneKey::of($l->phone) ?? 'none:'.$l->id)
            ->filter(fn ($g, $key) => ! str_starts_with((string) $key, 'none:') && $g->pluck('assigned_to')->unique()->count() > 1);

        return $groups->map(fn ($g, $key) => ['key' => (string) $key, 'leads' => $g->values()])->values()->take(10)->all();
    }

    /** طلاب يطابقون العميل بالهاتف أو البريد (لشاشة التحويل). */
    private function matchingStudents(SalesLead $lead)
    {
        $tail = PhoneKey::likeTail($lead->phone);
        $email = trim((string) $lead->email);
        if (! $tail && $email === '') {
            return collect();
        }

        return User::query()
            ->where(function ($q) use ($tail, $email, $lead) {
                if ($tail) {
                    $q->orWhere('phone', 'like', $tail);
                }
                if ($email !== '') {
                    $q->orWhere('email', $email);
                }
                if ($lead->student_user_id) {
                    $q->orWhere('id', $lead->student_user_id);
                }
            })
            ->limit(20)->get(['id', 'name', 'phone', 'email', 'created_at'])
            ->filter(fn (User $u) => (int) $u->id === (int) $lead->student_user_id || PhoneKey::same($u->phone, $lead->phone) || ($email !== '' && strcasecmp((string) $u->email, $email) === 0))
            ->values();
    }

    private function exportCsv($leads): StreamedResponse
    {
        return response()->streamDownload(function () use ($leads) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['#', 'الاسم', 'الهاتف', 'البريد', 'المرحلة', 'الاهتمام', 'المصدر', 'المتابعة التالية', 'الموظف']);
            foreach ($leads as $l) {
                fputcsv($out, [$l->id, $l->name, $l->phone, $l->email, SalesLead::stageLabel((string) $l->stage), $l->interestType?->name_ar,
                    SalesLead::sourceLabel((string) $l->source), $l->next_follow_up_at?->format('Y-m-d H:i'), $l->assignee?->name]);
            }
            fclose($out);
        }, 'leads-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
