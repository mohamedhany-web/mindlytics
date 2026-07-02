<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Services\WhatsAppCloudService;
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
                ->with('user:id,name')
                ->orderByDesc('last_message_at')
                ->orderByDesc('updated_at');

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
        ));
    }

    public function templates(): JsonResponse
    {
        $result = $this->cloud->listApprovedTemplates();

        return response()->json($result);
    }

    public function poll(Request $request): JsonResponse
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return response()->json(['success' => false, 'error' => 'الجداول غير جاهزة — نفّذ migrate']);
        }

        $conversationId = (int) $request->query('conversation_id');
        $afterId = (int) $request->query('after_id', 0);

        $conversations = WhatsAppConversation::query()
            ->with('user:id,name')
            ->orderByDesc('last_message_at')
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
            'template_name' => 'required|string|max:200',
            'language_code' => 'nullable|string|max:20',
        ]);

        $result = $this->inbox->startConversationWithTemplate(
            $validated['phone'],
            $validated['template_name'],
            $validated['language_code'] ?? 'en_US',
            auth()->id()
        );

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل البدء'], 422);
        }

        /** @var WhatsAppConversation $conversation */
        $conversation = $result['conversation'];

        return response()->json([
            'success' => true,
            'redirect' => route('admin.whatsapp.inbox', ['conversation' => $conversation->id]),
            'conversation' => $this->inbox->serializeConversation($conversation),
        ]);
    }

    public function markRead(WhatsAppConversation $conversation): JsonResponse
    {
        $this->inbox->markConversationRead($conversation);

        return response()->json([
            'success' => true,
            'unread_total' => (int) WhatsAppConversation::query()->sum('unread_count'),
        ]);
    }
}
