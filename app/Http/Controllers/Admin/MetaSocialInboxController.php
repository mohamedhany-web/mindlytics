<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialPage;
use App\Models\SalesLead;
use App\Services\MetaSocial\MetaSocialCrmService;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetaSocialInboxController extends Controller
{
    public function __construct(
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
        private MetaSocialCrmService $crm,
    ) {}

    public function index(Request $request)
    {
        $tablesReady = $this->inbox->tablesReady();
        $crmReady = $tablesReady && $this->crm->crmReady();
        $connectionMeta = $this->graph->connectionMeta();

        $pages = $tablesReady
            ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get()
            : collect();

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');
        $assignedFilter = $request->query('assigned_to');
        $platformFilter = $request->query('platform');

        $conversations = collect();
        $activeConversation = null;
        $messages = collect();
        $crmPayload = null;
        $agents = $crmReady ? $this->crm->eligibleAgents() : [];

        if ($tablesReady) {
            $query = MetaSocialConversation::query()
                ->with(['page', 'assignee:id,name', 'salesLead:id,name,stage,phone'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id');

            if ($pageId > 0) {
                $query->where('meta_social_page_id', $pageId);
            }
            if ($platformFilter && in_array($platformFilter, ['messenger', 'instagram'], true)) {
                $query->where('platform', $platformFilter);
            }
            if ($assignedFilter === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif (is_numeric($assignedFilter) && (int) $assignedFilter > 0) {
                $query->where('assigned_to', (int) $assignedFilter);
            }

            $conversations = $query->limit(150)->get();

            if ($conversationId > 0) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'assignee:id,name', 'salesLead', 'messages' => fn ($q) => $q->orderBy('sent_at')->orderBy('id'), 'messages.sentBy:id,name'])
                    ->find($conversationId);
            } elseif ($conversations->isNotEmpty()) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'assignee:id,name', 'salesLead', 'messages' => fn ($q) => $q->orderBy('sent_at')->orderBy('id'), 'messages.sentBy:id,name'])
                    ->find($conversations->first()->id);
            }

            if ($activeConversation) {
                if ($crmReady && ! $activeConversation->participant_name) {
                    $this->crm->enrichParticipantProfile($activeConversation);
                    $activeConversation->refresh();
                    $activeConversation->load(['page', 'assignee:id,name', 'salesLead', 'messages.sentBy:id,name']);
                }
                $this->inbox->markConversationRead($activeConversation);
                $messages = $activeConversation->messages;
                if ($crmReady) {
                    $crmPayload = $this->crm->serializeCrm($activeConversation);
                }
            }
        }

        $unreadTotal = $tablesReady ? (int) MetaSocialConversation::sum('unread_count') : 0;
        $connected = (bool) ($connectionMeta['can_use'] ?? false);

        return view('admin.meta-social.inbox', compact(
            'tablesReady',
            'crmReady',
            'connectionMeta',
            'connected',
            'pages',
            'pageId',
            'conversations',
            'activeConversation',
            'messages',
            'unreadTotal',
            'agents',
            'crmPayload',
            'assignedFilter',
            'platformFilter',
        ))->with([
            'waImmersiveInbox' => true,
            'waInboxTitle' => 'محادثات السوشيال',
            'waInboxSubtitle' => 'Messenger · Instagram — CRM مثل واتساب',
            'waAdminSettingsUrl' => route('admin.meta-social.settings'),
            'waAdminPagesUrl' => route('admin.meta-social.pages.index'),
            'waAdminDashboardUrl' => route('admin.meta-social.index'),
        ]);
    }

    public function reply(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $result = $this->inbox->sendReply($conversation, $validated['body'], auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل'], 422);
        }

        /** @var \App\Models\MetaSocialMessage $message */
        $message = $result['message'];

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'direction' => $message->direction,
                'sent_at_human' => $message->sent_at?->format('H:i'),
                'author' => auth()->user()?->name,
            ],
        ]);
    }

    public function assign(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        $conversation = $this->crm->assign($conversation, (int) $validated['assigned_to'], auth()->id());

        return response()->json([
            'success' => true,
            'crm' => $this->crm->serializeCrm($conversation),
        ]);
    }

    public function updateContact(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:open,closed',
        ]);

        $conversation = $this->crm->updateContactDetails($conversation, $validated);

        return response()->json([
            'success' => true,
            'crm' => $this->crm->serializeCrm($conversation),
        ]);
    }

    public function createLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        if (! empty($validated['phone'])) {
            $conversation->update(['phone' => $validated['phone']]);
        }
        if (! empty($validated['email'])) {
            $conversation->update(['email' => $validated['email']]);
        }

        $lead = $this->crm->createLeadFromConversation(
            $conversation->fresh(),
            isset($validated['assigned_to']) ? (int) $validated['assigned_to'] : null,
            auth()->id(),
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['name'] ?? null,
        );

        return response()->json([
            'success' => true,
            'lead_id' => $lead->id,
            'crm' => $this->crm->serializeCrm($conversation->fresh(['page', 'assignee', 'salesLead'])),
        ]);
    }

    public function linkLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'sales_lead_id' => 'required|integer|exists:sales_leads,id',
        ]);

        $lead = SalesLead::query()->findOrFail($validated['sales_lead_id']);
        $conversation = $this->crm->linkLead($conversation, $lead);

        return response()->json([
            'success' => true,
            'crm' => $this->crm->serializeCrm($conversation),
        ]);
    }

    public function enrich(MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $conversation = $this->crm->enrichParticipantProfile($conversation);

        return response()->json([
            'success' => true,
            'crm' => $this->crm->serializeCrm($conversation),
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        if (! $this->inbox->tablesReady()) {
            return response()->json(['success' => false], 503);
        }

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');
        $afterId = (int) $request->query('after_id', 0);

        $unreadTotal = (int) MetaSocialConversation::sum('unread_count');

        $payload = [
            'success' => true,
            'unread_total' => $unreadTotal,
        ];

        if ($conversationId > 0) {
            $conversation = MetaSocialConversation::query()
                ->with(['assignee:id,name', 'salesLead:id,name,stage'])
                ->find($conversationId);

            if ($conversation) {
                $messagesQuery = $conversation->messages()->with('sentBy:id,name')->orderBy('sent_at')->orderBy('id');
                if ($afterId > 0) {
                    $messagesQuery->where('id', '>', $afterId);
                }
                $messages = $messagesQuery->get();

                $payload['message_count'] = $conversation->messages()->count();
                $payload['messages'] = $messages->map(fn ($m) => [
                    'id' => $m->id,
                    'body' => $m->displayBody(),
                    'direction' => $m->direction,
                    'message_type' => $m->message_type,
                    'attachment_url' => $m->attachment_url,
                    'author' => $m->sentBy?->name,
                    'sent_at_human' => $m->sent_at?->format('H:i') ?? $m->created_at?->format('H:i'),
                ]);
                if ($this->crm->crmReady()) {
                    $payload['crm'] = $this->crm->serializeCrm($conversation);
                }
            }
        }

        if ($pageId >= 0) {
            $listQuery = MetaSocialConversation::query()
                ->with(['page:id,page_name', 'assignee:id,name'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id')
                ->limit(50);

            if ($pageId > 0) {
                $listQuery->where('meta_social_page_id', $pageId);
            }

            $payload['conversations'] = $listQuery->get()->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->displayName(),
                'platform' => $c->platformLabel(),
                'page' => $c->page?->page_name,
                'preview' => $c->last_message_preview,
                'unread' => $c->unread_count,
                'assignee' => $c->assignee?->name,
                'last_at' => $c->last_message_at?->diffForHumans(),
            ]);
        }

        return response()->json($payload);
    }
}
