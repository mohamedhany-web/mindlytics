<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\MetaSocialMessage;
use App\Domains\Messaging\Models\WhatsAppConversation;
use App\Domains\Messaging\Models\WhatsAppConversationMessage;
use App\Domains\Sales\Http\Controllers\Crm\Concerns\InteractsWithCrm;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\CrmAccessService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * تقارير قنوات المراسلة داخل CRM: ردود الموظفين + عملاء ماسنجر/واتساب ومراحلهم.
 */
class CrmSocialReportController extends Controller
{
    use InteractsWithCrm;

    public function __construct(private CrmAccessService $access) {}

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canManage() || $scope->isRep(), 403);
        abort_unless(
            $this->access->allows($scope->user, 'ops.meta', $scope)
            || $this->access->allows($scope->user, 'ops.whatsapp', $scope)
            || $this->access->allows($scope->user, 'module.reports', $scope)
            || $this->access->allows($scope->user, 'ops.social_report', $scope),
            403
        );

        $from = Carbon::parse((string) $request->input('from', now()->subDays(6)->toDateString()))->startOfDay();
        $to = Carbon::parse((string) $request->input('to', now()->toDateString()))->endOfDay();
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $staffOptions = $scope->isRep()
            ? collect([$scope->user])
            : $scope->members()->sortBy('name')->values();

        $memberIds = $staffOptions->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($memberIds === []) {
            $memberIds = [(int) $scope->user->id];
            $staffOptions = collect([$scope->user]);
        }

        $filterUserId = (int) $request->input('user_id', 0);
        if ($scope->isRep()) {
            $filterUserId = (int) $scope->user->id;
        } elseif ($filterUserId > 0 && ! in_array($filterUserId, $memberIds, true)) {
            abort(403);
        }

        $replyMemberIds = $filterUserId > 0 ? [$filterUserId] : $memberIds;
        $replyMembers = $staffOptions->whereIn('id', $replyMemberIds)->values();
        if ($replyMembers->isEmpty() && $filterUserId > 0) {
            $u = User::query()->find($filterUserId);
            $replyMembers = $u ? collect([$u]) : collect();
        }

        $replyRows = $this->replyRows($replyMemberIds, $from, $to, $replyMembers);
        $employeeReplies = $filterUserId > 0
            ? $this->employeeReplies($filterUserId, $from, $to, (string) $request->input('channel', 'all'))
            : collect();

        $messengerCustomers = $this->messengerCustomers($scope, $request);
        $whatsappCustomers = $this->whatsappCustomers($scope, $request);

        $selectedUser = $filterUserId > 0
            ? ($staffOptions->firstWhere('id', $filterUserId) ?? User::query()->find($filterUserId))
            : null;

        $totals = [
            'whatsapp' => (int) collect($replyRows)->sum('whatsapp'),
            'messenger' => (int) collect($replyRows)->sum('messenger'),
            'messenger_customers' => $messengerCustomers->total(),
            'whatsapp_customers' => $whatsappCustomers->total(),
            'employee_replies' => $employeeReplies->count(),
        ];

        return $this->crmView($scope, 'reports', 'reports.social', [
            'from' => $from,
            'to' => $to,
            'replyRows' => $replyRows,
            'employeeReplies' => $employeeReplies,
            'selectedUser' => $selectedUser,
            'filterUserId' => $filterUserId,
            'staffOptions' => $staffOptions,
            'messengerCustomers' => $messengerCustomers,
            'whatsappCustomers' => $whatsappCustomers,
            'totals' => $totals,
            'stages' => MetaSocialConversation::LEAD_STAGES,
            'crmStages' => SalesLead::STAGES,
            'filterStage' => (string) $request->input('stage', ''),
            'filterChannel' => (string) $request->input('channel', 'all'),
            'q' => trim((string) $request->input('q', '')),
        ]);
    }

    /**
     * @param  list<int>  $memberIds
     * @return list<array{user: User, whatsapp: int, messenger: int, total: int}>
     */
    private function replyRows(array $memberIds, Carbon $from, Carbon $to, $members): array
    {
        $wa = [];
        if (Schema::hasTable('whatsapp_conversation_messages')
            && Schema::hasColumn('whatsapp_conversation_messages', 'sent_by_user_id')) {
            $wa = WhatsAppConversationMessage::query()
                ->selectRaw('sent_by_user_id as uid, COUNT(*) as c')
                ->where('direction', 'outbound')
                ->whereIn('sent_by_user_id', $memberIds)
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('sent_by_user_id')
                ->pluck('c', 'uid')
                ->all();
        }

        $meta = [];
        if (Schema::hasTable('meta_social_messages')
            && Schema::hasColumn('meta_social_messages', 'sent_by_user_id')) {
            $meta = MetaSocialMessage::query()
                ->selectRaw('sent_by_user_id as uid, COUNT(*) as c')
                ->where('direction', 'outbound')
                ->whereIn('sent_by_user_id', $memberIds)
                ->whereBetween('created_at', [$from, $to])
                ->groupBy('sent_by_user_id')
                ->pluck('c', 'uid')
                ->all();
        }

        $byId = $members->keyBy('id');
        $rows = [];
        foreach ($memberIds as $id) {
            $user = $byId->get($id) ?? User::query()->find($id);
            if (! $user) {
                continue;
            }
            $w = (int) ($wa[$id] ?? 0);
            $m = (int) ($meta[$id] ?? 0);
            $rows[] = [
                'user' => $user,
                'whatsapp' => $w,
                'messenger' => $m,
                'total' => $w + $m,
            ];
        }

        usort($rows, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $rows;
    }

    /**
     * كل ردود موظف واحد في الفترة (واتساب + ماسنجر) مرتّبة زمنيًا.
     *
     * @return Collection<int, array{channel: string, at: Carbon, body: string, customer: string, phone: string, conversation_id: int|null}>
     */
    private function employeeReplies(int $userId, Carbon $from, Carbon $to, string $channel): Collection
    {
        $rows = collect();
        $channel = in_array($channel, ['whatsapp', 'messenger', 'all'], true) ? $channel : 'all';

        if (($channel === 'all' || $channel === 'whatsapp')
            && Schema::hasTable('whatsapp_conversation_messages')
            && Schema::hasColumn('whatsapp_conversation_messages', 'sent_by_user_id')) {
            WhatsAppConversationMessage::query()
                ->with(['conversation:id,phone_number,contact_name'])
                ->where('direction', 'outbound')
                ->where('sent_by_user_id', $userId)
                ->whereBetween('created_at', [$from, $to])
                ->orderByDesc('created_at')
                ->limit(500)
                ->get()
                ->each(function (WhatsAppConversationMessage $msg) use ($rows) {
                    $conv = $msg->conversation;
                    $rows->push([
                        'channel' => 'whatsapp',
                        'at' => $msg->sent_at ?? $msg->created_at,
                        'body' => $msg->displayBody(),
                        'customer' => $conv?->displayName() ?? 'عميل واتساب',
                        'phone' => $conv ? $conv->formattedPhone() : '—',
                        'conversation_id' => $conv?->id,
                    ]);
                });
        }

        if (($channel === 'all' || $channel === 'messenger')
            && Schema::hasTable('meta_social_messages')
            && Schema::hasColumn('meta_social_messages', 'sent_by_user_id')) {
            MetaSocialMessage::query()
                ->with(['conversation:id,participant_name,participant_username,phone,platform'])
                ->where('direction', 'outbound')
                ->where('sent_by_user_id', $userId)
                ->whereBetween('created_at', [$from, $to])
                ->orderByDesc('created_at')
                ->limit(500)
                ->get()
                ->each(function (MetaSocialMessage $msg) use ($rows) {
                    $conv = $msg->conversation;
                    $phone = $conv && $conv->phone && ! str_starts_with((string) $conv->phone, 'meta_')
                        ? (string) $conv->phone
                        : '—';
                    $rows->push([
                        'channel' => $conv?->platform === 'instagram' ? 'instagram' : 'messenger',
                        'at' => $msg->sent_at ?? $msg->created_at,
                        'body' => $msg->displayBody(),
                        'customer' => $conv?->displayName() ?? 'عميل ماسنجر',
                        'phone' => $phone,
                        'conversation_id' => $conv?->id,
                    ]);
                });
        }

        return $rows
            ->sortByDesc(fn ($r) => $r['at']?->timestamp ?? 0)
            ->values();
    }

    private function messengerCustomers($scope, Request $request)
    {
        if (! Schema::hasTable('meta_social_conversations')) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
        }

        $q = MetaSocialConversation::query()
            ->with(['assignee:id,name', 'salesLead:id,name,phone,stage,assigned_to', 'page:id,page_name'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($scope->assigneeIds !== null) {
            $q->whereIn('assigned_to', $scope->assigneeIds === [] ? [0] : $scope->assigneeIds);
        }

        $stage = trim((string) $request->input('stage', ''));
        if ($stage !== '' && Schema::hasColumn('meta_social_conversations', 'lead_stage')) {
            $q->where('lead_stage', $stage);
        }

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('participant_name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('participant_username', 'like', '%'.$search.'%')
                    ->orWhereHas('salesLead', fn ($l) => $l->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
            });
        }

        return $q->paginate(25, ['*'], 'meta_page')->withQueryString();
    }

    private function whatsappCustomers($scope, Request $request)
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
        }

        $q = WhatsAppConversation::query()
            ->with(['assignee:id,name', 'salesLead:id,name,phone,stage,assigned_to'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        if ($scope->assigneeIds !== null) {
            $q->whereIn('assigned_to', $scope->assigneeIds === [] ? [0] : $scope->assigneeIds);
        }

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('phone_number', 'like', '%'.$search.'%')
                    ->orWhere('contact_name', 'like', '%'.$search.'%')
                    ->orWhereHas('salesLead', fn ($l) => $l->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%'));
            });
        }

        $crmStage = trim((string) $request->input('crm_stage', ''));
        if ($crmStage !== '') {
            $q->whereHas('salesLead', fn ($l) => $l->where('stage', $crmStage));
        }

        return $q->paginate(25, ['*'], 'wa_page')->withQueryString();
    }
}
