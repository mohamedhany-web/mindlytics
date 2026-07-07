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
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        if ($conversation->isOwnedBySalesAgent($userId)) {
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

        try {
            $connectionMeta = $cloud->connectionMeta();
            if (! isset($connectionMeta['webhook'])) {
                $connectionMeta['webhook'] = $cloud->webhookDiagnostics();
            }
        } catch (\Throwable $e) {
            report($e);
            $connectionMeta = [
                'success' => false,
                'can_send' => false,
                'label' => 'تعذّر التحقق من الربط',
                'last_error' => 'حدث خطأ أثناء تحميل حالة الواتساب',
                'webhook' => ['issues' => [], 'tips' => [], 'meta' => []],
            ];
        }
        $tablesReady = Schema::hasTable('whatsapp_conversations');

        $activeConversation = null;
        $messages = collect();
        $withinWindow = false;

        if ($tablesReady) {
            try {
                $inbox->syncRecentOutboundLogs();
            } catch (\Throwable $e) {
                report($e);
            }

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
            ->map(fn ($m) => $this->serializeInboxMessage($m));

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
            'reply_url' => $this->safeInboxRoute('reply', $conversation),
            'react_url' => $this->safeInboxRoute('react', $conversation),
            'media_url' => $this->safeInboxRoute('media-send', $conversation),
            'template_url' => $this->safeInboxRoute('template', $conversation),
            'crm_urls' => $this->safeInboxCrmUrls($conversation),
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
                $payload['messages'] = $query->get()->map(fn ($m) => $this->serializeInboxMessage($m));
                $payload['within_service_window'] = $inbox->isWithinServiceWindow($conversation);
            }
        }

        return response()->json($payload);
    }

    public function inboxReply(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'body' => 'required|string|max:4096',
            'context_message_id' => 'nullable|integer|exists:whatsapp_conversation_messages,id',
        ]);

        $contextWaId = null;
        $contextPreview = null;
        if (! empty($validated['context_message_id'])) {
            $contextMsg = WhatsAppConversationMessage::query()->findOrFail((int) $validated['context_message_id']);
            if ((int) $contextMsg->conversation_id !== (int) $conversation->id) {
                return response()->json(['success' => false, 'error' => 'الرسالة المرجعية لا تنتمي لهذه المحادثة'], 422);
            }
            $contextWaId = (string) ($contextMsg->whatsapp_message_id ?? '');
            $contextPreview = mb_substr($contextMsg->displayBody(), 0, 200);
            if ($contextWaId === '') {
                return response()->json(['success' => false, 'error' => 'لا يمكن الرد على رسالة بدون معرّف Meta'], 422);
            }
        }

        $result = $inbox->sendTextReply(
            $conversation,
            $validated['body'],
            auth()->id(),
            $contextWaId,
            $contextPreview,
        );

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
            'message' => $this->serializeInboxMessage($message->load('sentBy:id,name')),
            'conversation' => $inbox->serializeConversation($conversation, $this->inboxAudience()),
        ]);
    }

    public function inboxSendMedia(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        try {
            [$inbox] = $this->inboxServices();
            $this->authorizeInboxConversation($conversation);

            $voiceNote = (bool) $request->boolean('voice_note');
            $caption = null;
            $uploadedFile = null;

            if ($request->filled('audio_base64')) {
                $validated = $request->validate([
                    'audio_base64' => 'required|string|max:25000000',
                    'audio_mime' => 'nullable|string|max:128',
                    'audio_name' => 'nullable|string|max:255',
                    'caption' => 'nullable|string|max:1024',
                    'voice_note' => 'nullable|boolean',
                ]);
                $voiceNote = (bool) $request->boolean('voice_note');
                $caption = $validated['caption'] ?? null;
                $uploadedFile = $this->inboxUploadedFileFromBase64(
                    (string) $validated['audio_base64'],
                    (string) ($validated['audio_mime'] ?? 'application/octet-stream'),
                    (string) ($validated['audio_name'] ?? 'voice.webm'),
                );
                if ($uploadedFile === null) {
                    return response()->json([
                        'success' => false,
                        'error' => 'تعذّر قراءة الملف الصوتي',
                    ], 422);
                }
            } else {
                $validated = $request->validate([
                    'file' => 'required|file|max:16384',
                    'caption' => 'nullable|string|max:1024',
                    'voice_note' => 'nullable|boolean',
                ]);
                $voiceNote = (bool) $request->boolean('voice_note');
                $caption = $validated['caption'] ?? null;
                $uploadedFile = $request->file('file');
            }

            $result = $inbox->sendMediaReply(
                $conversation,
                $uploadedFile,
                auth()->id(),
                $caption,
                $voiceNote,
            );

            if (! ($result['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'فشل إرسال الوسائط',
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
                'message' => $this->serializeInboxMessage($message->load('sentBy:id,name')),
                'conversation' => $inbox->serializeConversation($conversation, $this->inboxAudience()),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'error' => config('app.debug')
                    ? mb_substr($e->getMessage(), 0, 300)
                    : 'تعذّر إرسال الوسائط — حاول مرة أخرى أو أرفق ملفاً صوتياً.',
            ], 500);
        }
    }

    public function inboxMessageMedia(
        WhatsAppConversation $conversation,
        WhatsAppConversationMessage $message,
    ): StreamedResponse {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        if ((int) $message->conversation_id !== (int) $conversation->id) {
            abort(404);
        }

        if (! $inbox->messageHasMedia($message)) {
            abort(404);
        }

        return $inbox->streamMessageMedia($message);
    }

    public function inboxReact(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'message_id' => 'required|integer|exists:whatsapp_conversation_messages,id',
            'emoji' => 'required|string|max:16',
        ]);

        $target = WhatsAppConversationMessage::query()->findOrFail((int) $validated['message_id']);
        if ((int) $target->conversation_id !== (int) $conversation->id) {
            return response()->json(['success' => false, 'error' => 'الرسالة لا تنتمي لهذه المحادثة'], 422);
        }

        $result = $inbox->sendReaction($conversation, $target, $validated['emoji'], auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل التفاعل'], 422);
        }

        /** @var WhatsAppConversationMessage $message */
        $message = $result['message'];

        return response()->json([
            'success' => true,
            'message' => $this->serializeInboxMessage($message->load('sentBy:id,name')),
        ]);
    }

    public function inboxSendTemplate(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        [$inbox] = $this->inboxServices();
        $this->authorizeInboxConversation($conversation);

        $validated = $request->validate([
            'template_name' => 'required|string|max:200',
            'language_code' => 'required|string|max:20',
            'template_variables' => 'nullable|array',
            'template_variables.*' => 'nullable|string|max:500',
        ]);

        $build = app(\App\Services\WhatsAppTemplateService::class)->buildSendComponents(
            $validated['template_name'],
            $validated['language_code'],
            $request->input('template_variables', [])
        );

        if (isset($build['error'])) {
            return response()->json(['success' => false, 'error' => $build['error']], 422);
        }

        $result = $inbox->sendTemplateReply(
            $conversation,
            $validated['template_name'],
            $validated['language_code'],
            $build['components'],
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
            'message' => $this->serializeInboxMessage($message->load('sentBy:id,name')),
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
            'template_variables' => 'nullable|array',
            'template_variables.*' => 'nullable|string|max:500',
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
            $language = trim((string) ($validated['language_code'] ?? ''));
            if ($language === '') {
                return response()->json(['success' => false, 'error' => 'لغة القالب مطلوبة'], 422);
            }

            $build = app(\App\Services\WhatsAppTemplateService::class)->buildSendComponents(
                $templateName,
                $language,
                $request->input('template_variables', [])
            );

            if (isset($build['error'])) {
                return response()->json(['success' => false, 'error' => $build['error']], 422);
            }

            $result = $inbox->startConversationWithTemplate(
                $validated['phone'],
                $templateName,
                $language,
                auth()->id(),
                $build['components']
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
            $payload['message'] = $this->serializeInboxMessage($result['message']->load('sentBy:id,name'));
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
                ? rescue(function () use ($assignment) {
                    return collect($assignment->eligibleAgents())->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->values()->all();
                }, [], false)
                : [],
            'crmTags' => rescue(fn () => WhatsAppTag::query()->orderBy('name')->get(['id', 'name', 'slug', 'color']), collect(), false),
            'crmStatuses' => WhatsAppConversation::STATUSES,
            'crmDepartments' => WhatsAppConversation::DEPARTMENTS,
        ];
    }

    /** @return array<string, mixed> */
    protected function inboxRoutesForView(?WhatsAppConversation $active): array
    {
        [, , $crm] = $this->inboxServices();
        $crmUrls = ($active && $crm->crmTablesReady()) ? $this->safeInboxCrmUrls($active) : [];

        return [
            'poll' => $this->safeInboxRoute('poll'),
            'conversationUrlTemplate' => $this->safeInboxRoute('conversation', ['conversation' => '__ID__']),
            'reply' => $active ? $this->safeInboxRoute('reply', $active) : null,
            'react' => $active ? $this->safeInboxRoute('react', $active) : null,
            'media' => $active ? $this->safeInboxRoute('media-send', $active) : null,
            'template' => $active ? $this->safeInboxRoute('template', $active) : null,
            'start' => $this->safeInboxRoute('start'),
            'templates' => $this->safeInboxRoute('templates'),
            'index' => $this->safeInboxRoute('index'),
            'crm' => $crmUrls,
        ];
    }

    protected function safeInboxRoute(string $action, mixed ...$params): ?string
    {
        try {
            return $this->inboxRoute($action, ...$params);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** @return array<string, string> */
    protected function safeInboxCrmUrls(WhatsAppConversation $conversation): array
    {
        try {
            return $this->inboxCrmUrls($conversation);
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }

    protected function inboxUploadedFileFromBase64(string $payload, string $mime, string $name): ?UploadedFile
    {
        if (str_contains($payload, ',')) {
            $payload = explode(',', $payload, 2)[1];
        }

        $binary = base64_decode($payload, true);
        if ($binary === false || $binary === '') {
            return null;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'wa_audio_');
        if ($tmp === false) {
            return null;
        }

        if (file_put_contents($tmp, $binary) === false) {
            @unlink($tmp);

            return null;
        }

        $safeName = preg_replace('/[^\w.\-]+/u', '_', $name) ?: 'voice.webm';

        return new UploadedFile($tmp, $safeName, $mime, null, true);
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

    protected function serializeInboxMessage(WhatsAppConversationMessage $message): array
    {
        try {
            [$inbox] = $this->inboxServices();
            $mediaUrl = $inbox->messageHasMedia($message)
                ? $this->safeInboxRoute('media', ['conversation' => $message->conversation_id, 'message' => $message->id])
                : null;

            return $inbox->serializeMessage($message, $mediaUrl);
        } catch (\Throwable $e) {
            report($e);

            return [
                'id' => $message->id,
                'direction' => $message->direction,
                'body' => $message->displayBody(),
                'message_type' => $message->message_type,
                'status' => $message->status,
                'created_at_human' => '',
                'is_inbound' => $message->isInbound(),
            ];
        }
    }
}
