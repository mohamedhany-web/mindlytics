<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialPage;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MetaSocialInboxController extends Controller
{
    public function __construct(
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
    ) {}

    public function index(Request $request)
    {
        $tablesReady = $this->inbox->tablesReady();
        $connectionMeta = $this->graph->connectionMeta();

        $pages = $tablesReady
            ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get()
            : collect();

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');

        $conversations = collect();
        $activeConversation = null;
        $messages = collect();

        if ($tablesReady) {
            $query = MetaSocialConversation::query()
                ->with('page')
                ->orderByDesc('last_message_at');

            if ($pageId > 0) {
                $query->where('meta_social_page_id', $pageId);
            }

            $conversations = $query->limit(100)->get();

            if ($conversationId > 0) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'messages.sentBy'])
                    ->find($conversationId);
            } elseif ($conversations->isNotEmpty()) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'messages.sentBy'])
                    ->find($conversations->first()->id);
            }

            if ($activeConversation) {
                $this->inbox->markConversationRead($activeConversation);
                $messages = $activeConversation->messages;
            }
        }

        $unreadTotal = $tablesReady ? (int) MetaSocialConversation::sum('unread_count') : 0;

        $connected = (bool) ($connectionMeta['can_use'] ?? false);

        return view('admin.meta-social.inbox', compact(
            'tablesReady',
            'connectionMeta',
            'connected',
            'pages',
            'pageId',
            'conversations',
            'activeConversation',
            'messages',
            'unreadTotal',
        ))->with([
            'waImmersiveInbox' => true,
            'waInboxTitle' => 'محادثات السوشيال',
            'waInboxSubtitle' => 'Messenger · Instagram — Facebook Pages',
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
                'sent_at_human' => $message->sent_at?->diffForHumans(),
                'author' => auth()->user()?->name,
            ],
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        if (! $this->inbox->tablesReady()) {
            return response()->json(['success' => false], 503);
        }

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');

        $unreadTotal = (int) MetaSocialConversation::sum('unread_count');

        $payload = [
            'success' => true,
            'unread_total' => $unreadTotal,
        ];

        if ($conversationId > 0) {
            $conversation = MetaSocialConversation::query()->with('messages.sentBy')->find($conversationId);
            if ($conversation) {
                $payload['message_count'] = $conversation->messages->count();
                $payload['messages'] = $conversation->messages->map(fn ($m) => [
                    'id' => $m->id,
                    'body' => $m->displayBody(),
                    'direction' => $m->direction,
                    'message_type' => $m->message_type,
                    'attachment_url' => $m->attachment_url,
                    'author' => $m->sentBy?->name,
                    'sent_at_human' => $m->sent_at?->format('H:i') ?? $m->created_at?->format('H:i'),
                ]);
            }
        }

        if ($pageId > 0) {
            $payload['conversations'] = MetaSocialConversation::query()
                ->where('meta_social_page_id', $pageId)
                ->orderByDesc('last_message_at')
                ->limit(50)
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->displayName(),
                    'platform' => $c->platformLabel(),
                    'preview' => $c->last_message_preview,
                    'unread' => $c->unread_count,
                    'last_at' => $c->last_message_at?->diffForHumans(),
                ]);
        }

        return response()->json($payload);
    }
}
