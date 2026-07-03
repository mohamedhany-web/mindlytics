<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SalesLead;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppTag;
use App\Services\WhatsAppAssignmentService;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppCrmService;
use App\Services\WhatsAppInboxService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait HandlesWhatsAppInbox
{
    abstract protected function inboxView(): string;

    abstract protected function inboxRoute(string $action, mixed ...$params): string;

    abstract protected function inboxAudience(): string;

    /** @return array<string, mixed> */
    abstract protected function inboxBaseFilters(Request $request): array;

    /** @return array<string, mixed> */
    protected function inboxExtraViewData(): array
    {
        return [];
    }

    protected function authorizeInboxConversation(WhatsAppConversation $conversation): void
    {
        if ($this->inboxAudience() === 'admin') {
            return;
        }

        $userId = (int) auth()->id();

        if ((int) $conversation->assigned_to === $userId) {
            return;
        }

        if ($conversation->sales_lead_id) {
            $conversation->loadMissing('salesLead');
            if ((int) $conversation->salesLead?->assigned_to === $userId) {
                return;
            }
        }

        if ($conversation->assigned_to === null && $conversation->sales_lead_id === null) {
            return;
        }

        abort(403, 'هذه المحادثة غير مخصصة لك.');
    }

    protected function inboxServices(): array
    {
        return [
            app(WhatsAppInboxService::class),
            app(WhatsAppCloudService::class),
            app(WhatsAppCrmService::class),
            app(WhatsAppAssignmentService::class),
        ];
    }

    public function inboxIndex(Request $request): View
    {
        [$inbox, $cloud, $crm] = $this->inboxServices();

        $connectionMeta = $cloud->connectionMeta();
        $tablesReady = Schema::hasTable('whatsapp_conversations');

        $activeConversation = null;
        $messages = collect();
        $withinWindow = false;

        if ($tablesReady) {
            $inbox->syncRecentOutboundLogs();

            $query = WhatsAppConversation::query()
                ->with(['user:id,name', 'assignee:id,name', 'tags', 'salesLead:id,name,stage,assigned_to'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('updated_at');

            $inbox->applyConversationFilters($query, $this->inboxBaseFilters($request));

            if ($search = trim((string) $request->query('search'))) {
                $digits = preg_replace('/[^0-9]/', '', $search);
                $query->where(function ($q) use ($search, $digits) {
                    $q->where('contact_name', 'like', '%' . $search . '%');
                    if ($digits !== '') {
                        $q->orWhere('phone_number', 'like', '%' . $digits . '%');
                    }
                });
            }

            $conversations = $query->paginate(30)->withQueryString();

            $activeId = (int) $request->query('conversation');
            if ($activeId > 0) {
                $activeConversation = WhatsAppConversation::query()
                    ->with(['user:id,name,phone', 'assignee:id,name', 'tags', 'contact', 'salesLead'])
                    ->find($activeId);
                if ($activeConversation) {
                    $this->authorizeInboxConversation($activeConversation);
                }
            } elseif ($conversations->isNotEmpty()) {
                $activeConversation = $conversations->first();
            }

            if ($activeConversation) {
                $inbox->markConversationRead($activeConversation);
                $withinWindow = $inbox->isWithinServiceWindow($activeConversation);
                $messages = $activeConversation->messages()
                    ->with('sentBy:id,name')
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get();
            }
        } else {
            $conversations = new LengthAwarePaginator([], 0, 30, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        $unreadQuery = WhatsAppConversation::query();
        $inbox->applyConversationFilters($unreadQuery, $this->inboxBaseFilters($request));
        $unreadTotal = $tablesReady ? (int) $unreadQuery->sum('unread_count') : 0;

        $metaTemplates = [];
        $metaTemplatesError = null;
        if (WhatsAppCloudSettings::isSendConfigured()) {
            $tplResult = $cloud->listApprovedTemplates();
            $metaTemplates = $tplResult['templates'] ?? [];
            $metaTemplatesError = ($tplResult['success'] ?? false) ? null : ($tplResult['error'] ?? null);
        }

        $startLead = null;
        if ($tablesReady && ($startLeadId = (int) $request->query('start_lead')) > 0) {
            $startLead = SalesLead::query()
                ->when($this->inboxAudience() === 'employee', fn ($q) => $q->forAssignee((int) auth()->id()))
                ->find($startLeadId);
        }

        return view($this->inboxView(), array_merge(
            compact(
                'connectionMeta',
                'conversations',
                'activeConversation',
                'messages',
                'tablesReady',
                'unreadTotal',
                'withinWindow',
                'metaTemplates',
                'metaTemplatesError',
                'startLead',
            ),
            $this->inboxCrmViewData(),
            [
                'inboxAudience' => $this->inboxAudience(),
                'pipelineStages' => SalesLead::STAGES,
                'inboxRoutes' => $this->inboxRoutesForView($activeConversation),
            ],
            $this->inboxExtraViewData()
        ));
    }

    public function inboxTemplates(): JsonResponse
    {
        [, $cloud] = $this->inboxServices();

        return response()->json($cloud->listApprovedTemplates());
    }

    public function inboxShowConversation(WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();

        if (! Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['success' => false, 'error' => 'الجداول غير جاهزة — نفّذ migrate'], 503);
        }

        $this->authorizeInboxConversation($conversation);
        $inbox->markConversationRead($conversation);
        $conversation->load(['user:id,name,phone', 'assignee:id,name', 'tags', 'contact', 'salesLead']);

        $messages = $conversation->messages()
            ->with('sentBy:id,name')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => $inbox->serializeMessage($m));

        $notes = [];
        $timeline = [];
        if ($crm->crmTablesReady()) {
            $notes = $conversation->notes()
                ->with('author:id,name')
                ->latest()
                ->limit(30)
                ->get()
                ->map(fn ($n) => [
                    'id' => $n->id,
                    'body' => $n->body,
                    'author' => $n->author?->name,
                    'created_at_human' => $n->created_at?->diffForHumans(),
                ]);
            $timeline = $crm->timeline($conversation);
        }

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation, $this->inboxAudience()),
            'messages' => $messages,
            'notes' => $notes,
            'timeline' => $timeline,
            'within_service_window' => $inbox->isWithinServiceWindow($conversation),
            'reply_url' => $this->inboxRoute('reply', $conversation),
            'template_url' => $this->inboxRoute('template', $conversation),
            'crm_urls' => $this->inboxCrmUrls($conversation),
            'unread_total' => $this->inboxUnreadTotal(),
        ]);
    }

    public function inboxPoll(Request $request): JsonResponse
    {
        [$inbox] = $this->inboxServices();

        if (! Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['success' => false, 'error' => 'الجداول غير جاهزة — نفّذ migrate']);
        }

        $conversationId = (int) $request->query('conversation_id');
        $afterId = (int) $request->query('after_id', 0);

        $conversationsQuery = WhatsAppConversation::query()
            ->with(['user:id,name', 'assignee:id,name', 'tags', 'salesLead:id,name,stage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        $inbox->applyConversationFilters($conversationsQuery, $this->inboxBaseFilters($request));

        if ($search = trim((string) $request->query('search'))) {
            $digits = preg_replace('/[^0-9]/', '', $search);
            $conversationsQuery->where(function ($q) use ($search, $digits) {
                $q->where('contact_name', 'like', '%' . $search . '%');
                if ($digits !== '') {
                    $q->orWhere('phone_number', 'like', '%' . $digits . '%');
                }
            });
        }

        $conversations = $conversationsQuery
            ->limit(50)
            ->get()
            ->map(fn ($c) => $inbox->serializeConversation($c, $this->inboxAudience()));

        $payload = [
            'success' => true,
            'unread_total' => $this->inboxUnreadTotal(),
            'conversations' => $conversations,
            'messages' => [],
        ];

        if ($conversationId > 0) {
            $conversation = WhatsAppConversation::query()->find($conversationId);
            if ($conversation) {
                try {
                    $this->authorizeInboxConversation($conversation);
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json(['success' => false, 'error' => 'غير مصرح'], 403);
                }

                $query = $conversation->messages()->with('sentBy:id,name')->orderBy('created_at')->orderBy('id');
                if ($afterId > 0) {
                    $query->where('id', '>', $afterId);
                }
                $payload['messages'] = $query->get()->map(fn ($m) => $inbox->serializeMessage($m));
                $payload['within_service_window'] = $inbox->isWithinServiceWindow($conversation);
            }
        }

        return response()->json($payload);
    }

    public function inboxReply(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate(['body' => 'required|string|max:4096']);

        $result = $inbox->sendTextReply($conversation, $validated['body'], auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'فشل الإرسال',
                'requires_template' => $result['requires_template'] ?? false,
            ], 422);
        }

        /** @var WhatsAppConversationMessage $message */
        $message = $result['message'];
        $conversation->refresh();
        $conversation->load(['user:id,name', 'assignee:id,name', 'tags', 'contact', 'salesLead']);

        if ($this->inboxAudience() === 'employee' && ! $conversation->assigned_to) {
            app(WhatsAppCrmService::class)->assign($conversation, (int) auth()->id(), auth()->id());
            $conversation->refresh();
        }

        return response()->json([
            'success' => true,
            'message' => $inbox->serializeMessage($message->load('sentBy:id,name')),
            'conversation' => $inbox->serializeConversation($conversation, $this->inboxAudience()),
        ]);
    }

    public function inboxSendTemplate(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'template_name' => 'required|string|max:200',
            'language_code' => 'nullable|string|max:20',
        ]);

        $result = $inbox->sendTemplateReply(
            $conversation,
            $validated['template_name'],
            $validated['language_code'] ?? 'en_US',
            [],
            auth()->id()
        );

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال'], 422);
        }

        /** @var WhatsAppConversationMessage $message */
        $message = $result['message'];
        $conversation->refresh();
        $conversation->load(['user:id,name', 'assignee:id,name', 'tags', 'contact', 'salesLead']);

        return response()->json([
            'success' => true,
            'message' => $inbox->serializeMessage($message->load('sentBy:id,name')),
            'conversation' => $inbox->serializeConversation($conversation, $this->inboxAudience()),
        ]);
    }

    public function inboxStart(Request $request): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();

        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'body' => 'nullable|string|max:4096',
            'template_name' => 'nullable|string|max:200',
            'language_code' => 'nullable|string|max:20',
            'sales_lead_id' => 'nullable|exists:sales_leads,id',
        ]);

        if ($this->inboxAudience() === 'employee' && ! empty($validated['sales_lead_id'])) {
            $lead = SalesLead::query()->forAssignee(auth()->id())->findOrFail($validated['sales_lead_id']);
            $validated['phone'] = $lead->phone;
        }

        $body = trim((string) ($validated['body'] ?? ''));
        $templateName = trim((string) ($validated['template_name'] ?? ''));

        if ($body !== '') {
            $result = $inbox->startConversationWithMessage($validated['phone'], $body, auth()->id());
        } elseif ($templateName !== '') {
            $result = $inbox->startConversationWithTemplate(
                $validated['phone'],
                $templateName,
                $validated['language_code'] ?? 'en_US',
                auth()->id()
            );
        } else {
            return response()->json(['success' => false, 'error' => 'اكتب رسالة للإرسال'], 422);
        }

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'فشل البدء',
                'requires_template' => $result['requires_template'] ?? false,
            ], 422);
        }

        /** @var WhatsAppConversation $conversation */
        $conversation = $result['conversation'];

        if ($this->inboxAudience() === 'employee') {
            $crm->assign($conversation, (int) auth()->id(), auth()->id());
            if (! empty($validated['sales_lead_id'])) {
                $conversation->update(['sales_lead_id' => $validated['sales_lead_id']]);
                $crm->ensureContactForConversation($conversation->fresh());
            }
        }

        $payload = [
            'success' => true,
            'redirect' => $this->inboxRoute('index') . '?conversation=' . $conversation->id,
            'conversation' => $inbox->serializeConversation($conversation->fresh(), $this->inboxAudience()),
        ];

        if (isset($result['message'])) {
            $payload['message'] = $inbox->serializeMessage($result['message']->load('sentBy:id,name'));
        }

        return response()->json($payload);
    }

    public function inboxMarkRead(WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);
        $inbox->markConversationRead($conversation);

        return response()->json(['success' => true, 'unread_total' => $this->inboxUnreadTotal()]);
    }

    public function inboxUpdateStatus(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(WhatsAppConversation::STATUSES)),
        ]);

        $crm->updateStatus($conversation, $validated['status'], auth()->id());

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']), $this->inboxAudience()),
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

    public function inboxUpdateLeadStage(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(SalesLead::STAGES)),
        ]);

        $crm->updateLeadStage($conversation, $validated['stage'], auth()->id(), $this->inboxAudience() === 'employee');

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']), $this->inboxAudience()),
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

    public function inboxAssign(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        if ($this->inboxAudience() === 'employee') {
            abort(403);
        }

        [$inbox, , $crm] = $this->inboxServices();

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if (! empty($validated['assigned_to'])) {
            $crm->assign($conversation, (int) $validated['assigned_to'], auth()->id());
        } else {
            $conversation->update(['assigned_to' => null]);
        }

        $conversation->refresh();

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']), $this->inboxAudience()),
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

    public function inboxTransfer(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        if ($this->inboxAudience() === 'employee') {
            abort(403);
        }

        [$inbox, , $crm] = $this->inboxServices();

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $crm->transfer($conversation, (int) $validated['assigned_to'], $validated['reason'] ?? null);

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']), $this->inboxAudience()),
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

    public function inboxStoreNote(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate(['body' => 'required|string|max:5000']);
        $note = $crm->addNote($conversation, $validated['body'], auth()->id());

        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'body' => $note->body,
                'author' => $note->author?->name,
                'created_at_human' => $note->created_at?->diffForHumans(),
            ],
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

    public function inboxSyncTag(Request $request, WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        [$inbox, , $crm] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate(['attach' => 'required|boolean']);
        $crm->syncTag($conversation, $tag->id, (bool) $validated['attach'], auth()->id());

        return response()->json([
            'success' => true,
            'conversation' => $inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead']), $this->inboxAudience()),
            'timeline' => $crm->timeline($conversation->fresh()),
        ]);
    }

  protected function inboxUnreadTotal(): int
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return 0;
        }

        $query = WhatsAppConversation::query();
        app(WhatsAppInboxService::class)->applyConversationFilters($query, $this->inboxBaseFilters(request()));

        return (int) $query->sum('unread_count');
    }

    /** @return array<string, mixed> */
    protected function inboxCrmViewData(): array
    {
        [, , $crm, $assignment] = $this->inboxServices();

        if (! $crm->crmTablesReady()) {
            return [
                'crmReady' => false,
                'crmAgents' => [],
                'crmTags' => [],
                'crmStatuses' => [],
                'crmDepartments' => [],
            ];
        }

        return [
            'crmReady' => true,
            'crmAgents' => $this->inboxAudience() === 'admin'
                ? collect($assignment->eligibleAgents())->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->values()->all()
                : [],
            'crmTags' => WhatsAppTag::query()->orderBy('name')->get(['id', 'name', 'slug', 'color']),
            'crmStatuses' => WhatsAppConversation::STATUSES,
            'crmDepartments' => WhatsAppConversation::DEPARTMENTS,
        ];
    }

    /** @return array<string, mixed> */
    protected function inboxRoutesForView(?WhatsAppConversation $active): array
    {
        [, , $crm] = $this->inboxServices();
        $crmUrls = ($active && $crm->crmTablesReady()) ? $this->inboxCrmUrls($active) : [];

        return [
            'poll' => $this->inboxRoute('poll'),
            'conversationUrlTemplate' => $this->inboxRoute('conversation', ['conversation' => '__ID__']),
            'reply' => $active ? $this->inboxRoute('reply', $active) : null,
            'template' => $active ? $this->inboxRoute('template', $active) : null,
            'start' => $this->inboxRoute('start'),
            'templates' => $this->inboxRoute('templates'),
            'index' => $this->inboxRoute('index'),
            'crm' => $crmUrls,
        ];
    }

    /** @return array<string, string> */
    protected function inboxCrmUrls(WhatsAppConversation $conversation): array
    {
        $urls = [
            'status' => $this->inboxRoute('status', $conversation),
            'notes' => $this->inboxRoute('notes', $conversation),
            'tag' => rtrim($this->inboxRoute('tag', ['conversation' => $conversation->id, 'tag' => 0]), '/0'),
            'lead_stage' => $this->inboxRoute('lead-stage', $conversation),
        ];

        if ($this->inboxAudience() === 'admin') {
            $urls['transfer'] = $this->inboxRoute('transfer', $conversation);
            $urls['assign'] = $this->inboxRoute('assign', $conversation);
        }

        return $urls;
    }
}
