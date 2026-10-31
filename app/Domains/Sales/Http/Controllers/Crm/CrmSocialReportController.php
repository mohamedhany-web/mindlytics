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
            || $this->access->allows($scope->user, 'module.reports', $scope),
            403
        );

        $from = Carbon::parse((string) $request->input('from', now()->toDateString()))->startOfDay();
        $to = Carbon::parse((string) $request->input('to', now()->toDateString()))->endOfDay();
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $members = $scope->isRep()
            ? collect([$scope->user])
            : $scope->members();

        $memberIds = $members->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($memberIds === []) {
            $memberIds = [(int) $scope->user->id];
        }

        $replyRows = $this->replyRows($memberIds, $from, $to, $members);
        $messengerCustomers = $this->messengerCustomers($scope, $request);
        $whatsappCustomers = $this->whatsappCustomers($scope, $request);

        $totals = [
            'whatsapp' => (int) collect($replyRows)->sum('whatsapp'),
            'messenger' => (int) collect($replyRows)->sum('messenger'),
            'messenger_customers' => $messengerCustomers->total(),
            'whatsapp_customers' => $whatsappCustomers->total(),
        ];

        return $this->crmView($scope, 'reports', 'reports.social', [
            'from' => $from,
            'to' => $to,
            'replyRows' => $replyRows,
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
        if (Schema::hasTable('whatsapp_conversation_messages')) {
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
        if (Schema::hasTable('meta_social_messages')) {
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
