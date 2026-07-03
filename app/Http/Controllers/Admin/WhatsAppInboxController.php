<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class WhatsAppInboxController extends Controller
{
    public function __construct(
        private WhatsAppInboxService $inbox,
        private WhatsAppCloudService $cloud,
        private WhatsAppCrmService $crm,
        private WhatsAppAssignmentService $assignment,
    ) {}

    public function index(Request $request): View
    {
        $connectionMeta = $this->cloud->connectionMeta();
        $tablesReady = Schema::hasTable('whatsapp_conversations');

        $activeConversation = null;
        $messages = collect();
        $withinWindow = false;

        if ($tablesReady) {
            $this->inbox->syncRecentOutboundLogs();

            $query = WhatsAppConversation::query()
                ->with(['user:id,name', 'assignee:id,name', 'tags'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('updated_at');

            $this->inbox->applyConversationFilters($query, $this->crmFilters($request));

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
                    ->with('user:id,name,phone')
                    ->find($activeId);
            } elseif ($conversations->isNotEmpty()) {
                $activeConversation = $conversations->first();
            }

            if ($activeConversation) {
                $this->inbox->markConversationRead($activeConversation);
                $withinWindow = $this->inbox->isWithinServiceWindow($activeConversation);
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

        $unreadTotal = $tablesReady
            ? (int) WhatsAppConversation::query()->sum('unread_count')
            : 0;

        $metaTemplates = [];
        $metaTemplatesError = null;
        if (WhatsAppCloudSettings::isSendConfigured()) {
            $tplResult = $this->cloud->listApprovedTemplates();
            $metaTemplates = $tplResult['templates'] ?? [];
            $metaTemplatesError = ($tplResult['success'] ?? false) ? null : ($tplResult['error'] ?? null);
        }

        return view('admin.whatsapp.inbox', compact(
            'connectionMeta',
            'conversations',
            'activeConversation',
            'messages',
            'tablesReady',
            'unreadTotal',
            'withinWindow',
            'metaTemplates',
            'metaTemplatesError',
        ) + $this->crmViewData());
    }

    public function templates(): JsonResponse
    {
        $result = $this->cloud->listApprovedTemplates();

        return response()->json($result);
    }

    public function showConversation(WhatsAppConversation $conversation): JsonResponse
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['success' => false, 'error' => 'الجداول غير جاهزة — نفّذ migrate'], 503);
        }

        $this->inbox->markConversationRead($conversation);
        $conversation->load('user:id,name,phone');

        $messages = $conversation->messages()
            ->with('sentBy:id,name')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => $this->inbox->serializeMessage($m));

        $notes = [];
        $timeline = [];
        if ($this->crm->crmTablesReady()) {
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
            $timeline = $this->crm->timeline($conversation);
        }

        return response()->json([
            'success' => true,
            'conversation' => $this->inbox->serializeConversation($conversation),
            'messages' => $messages,
            'notes' => $notes,
            'timeline' => $timeline,
            'within_service_window' => $this->inbox->isWithinServiceWindow($conversation),
            'reply_url' => route('admin.whatsapp.inbox.reply', $conversation),
            'template_url' => route('admin.whatsapp.inbox.template', $conversation),
            'crm_urls' => $this->crmUrls($conversation),
            'unread_total' => (int) WhatsAppConversation::query()->sum('unread_count'),
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['success' => false, 'error' => 'الجداول غير جاهزة — نفّذ migrate']);
        }

        $conversationId = (int) $request->query('conversation_id');
        $afterId = (int) $request->query('after_id', 0);

        $conversationsQuery = WhatsAppConversation::query()
            ->with(['user:id,name', 'assignee:id,name', 'tags'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at');

        $this->inbox->applyConversationFilters($conversationsQuery, $this->crmFilters($request));

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
            ->map(fn ($c) => $this->inbox->serializeConversation($c));

        $payload = [
            'success' => true,
            'unread_total' => (int) WhatsAppConversation::query()->sum('unread_count'),
            'conversations' => $conversations,
            'messages' => [],
        ];

        if ($conversationId > 0) {
            $conversation = WhatsAppConversation::query()->find($conversationId);
            if ($conversation) {
                $query = $conversation->messages()->with('sentBy:id,name')->orderBy('created_at')->orderBy('id');
                if ($afterId > 0) {
                    $query->where('id', '>', $afterId);
                }
                $payload['messages'] = $query->get()->map(fn ($m) => $this->inbox->serializeMessage($m));
                $payload['within_service_window'] = $this->inbox->isWithinServiceWindow($conversation);
            }
        }

        return response()->json($payload);
    }

    public function reply(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:4096',
        ]);

        $result = $this->inbox->sendTextReply(
            $conversation,
            $validated['body'],
            auth()->id()
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

        return response()->json([
            'success' => true,
            'message' => $this->inbox->serializeMessage($message->load('sentBy:id,name')),
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['user:id,name'])),
        ]);
    }

    public function sendTemplate(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:200',
            'language_code' => 'nullable|string|max:20',
        ]);

        $result = $this->inbox->sendTemplateReply(
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

        return response()->json([
            'success' => true,
            'message' => $this->inbox->serializeMessage($message->load('sentBy:id,name')),
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['user:id,name'])),
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'body' => 'nullable|string|max:4096',
            'template_name' => 'nullable|string|max:200',
            'language_code' => 'nullable|string|max:20',
        ]);

        $body = trim((string) ($validated['body'] ?? ''));
        $templateName = trim((string) ($validated['template_name'] ?? ''));

        if ($body !== '') {
            $result = $this->inbox->startConversationWithMessage(
                $validated['phone'],
                $body,
                auth()->id()
            );
        } elseif ($templateName !== '') {
            $result = $this->inbox->startConversationWithTemplate(
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

        $payload = [
            'success' => true,
            'redirect' => route('admin.whatsapp.inbox', ['conversation' => $conversation->id]),
            'conversation' => $this->inbox->serializeConversation($conversation),
        ];

        if (isset($result['message'])) {
            $payload['message'] = $this->inbox->serializeMessage($result['message']->load('sentBy:id,name'));
        }

        return response()->json($payload);
    }

    public function markRead(WhatsAppConversation $conversation): JsonResponse
    {
        $this->inbox->markConversationRead($conversation);

        return response()->json([
            'success' => true,
            'unread_total' => (int) WhatsAppConversation::query()->sum('unread_count'),
        ]);
    }

    public function updateStatus(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(WhatsAppConversation::STATUSES)),
        ]);

        $this->crm->updateStatus($conversation, $validated['status'], auth()->id());

        return response()->json([
            'success' => true,
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead'])),
            'timeline' => $this->crm->timeline($conversation->fresh()),
        ]);
    }

    public function transfer(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $this->crm->transfer($conversation, (int) $validated['assigned_to'], $validated['reason'] ?? null);

        return response()->json([
            'success' => true,
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead'])),
            'timeline' => $this->crm->timeline($conversation->fresh()),
        ]);
    }

    public function assign(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $this->crm->assign($conversation, (int) $validated['assigned_to'], auth()->id());

        return response()->json([
            'success' => true,
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead'])),
            'timeline' => $this->crm->timeline($conversation->fresh()),
        ]);
    }

    public function storeNote(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $note = $this->crm->addNote($conversation, $validated['body'], auth()->id());

        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'body' => $note->body,
                'author' => $note->author?->name,
                'created_at_human' => $note->created_at?->diffForHumans(),
            ],
            'timeline' => $this->crm->timeline($conversation->fresh()),
        ]);
    }

    public function syncTag(Request $request, WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        $validated = $request->validate([
            'attach' => 'required|boolean',
        ]);

        $this->crm->syncTag($conversation, $tag->id, (bool) $validated['attach'], auth()->id());

        return response()->json([
            'success' => true,
            'conversation' => $this->inbox->serializeConversation($conversation->fresh(['assignee', 'tags', 'contact', 'salesLead'])),
            'timeline' => $this->crm->timeline($conversation->fresh()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function crmFilters(Request $request): array
    {
        return array_filter([
            'status' => $request->query('status'),
            'department' => $request->query('department'),
            'assigned_to' => $request->query('assigned_to'),
            'mine' => $request->boolean('mine'),
            'tag_id' => $request->query('tag_id'),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /**
     * @return array<string, mixed>
     */
    private function crmViewData(): array
    {
        if (! $this->crm->crmTablesReady()) {
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
            'crmAgents' => collect($this->assignment->eligibleAgents())->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ])->values()->all(),
            'crmTags' => WhatsAppTag::query()->orderBy('name')->get(['id', 'name', 'slug', 'color']),
            'crmStatuses' => WhatsAppConversation::STATUSES,
            'crmDepartments' => WhatsAppConversation::DEPARTMENTS,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function crmUrls(WhatsAppConversation $conversation): array
    {
        return [
            'status' => route('admin.whatsapp.inbox.status', $conversation),
            'transfer' => route('admin.whatsapp.inbox.transfer', $conversation),
            'assign' => route('admin.whatsapp.inbox.assign', $conversation),
            'notes' => route('admin.whatsapp.inbox.notes', $conversation),
            'tag' => rtrim(route('admin.whatsapp.inbox.tag', ['conversation' => $conversation->id, 'tag' => 0]), '/0'),
        ];
    }
}
