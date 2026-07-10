<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesTeamConversation;
use App\Models\SalesTeamMessage;
use App\Services\SalesTeamChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesTeamChatController extends Controller
{
    public function __construct(
        private SalesTeamChatService $chat
    ) {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (! $user || ! $user->isSalesStaff()) {
                return response()->json(['message' => 'غير مصرح'], 403);
            }

            return $next($request);
        });
    }

    public function bootstrap(): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        $teamChannel = $this->chat->ensureTeamChannel($team, $user);

        return response()->json([
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'is_manager' => (int) $team->manager_id === (int) $user->id,
            ],
            'members' => $this->chat->listMembers($user, $team),
            'conversations' => $this->chat->listConversations($user, $team)->values(),
            'team_conversation_id' => $teamChannel->id,
            'unread_total' => $this->chat->unreadCount($user, $team),
            'me' => ['id' => $user->id, 'name' => $user->name],
        ]);
    }

    public function conversations(): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);

        return response()->json([
            'conversations' => $this->chat->listConversations($user, $team)->values(),
            'unread_total' => $this->chat->unreadCount($user, $team),
        ]);
    }

    public function openTeam(): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        $conversation = $this->chat->ensureTeamChannel($team, $user);
        $conversation->load(['participants:id,name']);
        $last = \App\Models\SalesTeamMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with('user:id,name')
            ->latest('id')
            ->first();
        $conversation->setRelation('messages', collect($last ? [$last] : []));

        return response()->json([
            'conversation' => $this->chat->serializeConversation($conversation, $user),
        ]);
    }

    public function openDirect(Request $request): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $conversation = $this->chat->findOrCreateDirect($team, $user, (int) $data['user_id']);
        $conversation->load(['participants:id,name']);
        $last = \App\Models\SalesTeamMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with('user:id,name')
            ->latest('id')
            ->first();
        $conversation->setRelation('messages', collect($last ? [$last] : []));

        return response()->json([
            'conversation' => $this->chat->serializeConversation($conversation, $user),
        ]);
    }

    public function messages(Request $request, SalesTeamConversation $conversation): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        abort_unless((int) $conversation->sales_team_id === (int) $team->id, 403);

        $afterId = $request->filled('after_id') ? (int) $request->after_id : null;
        $messages = $this->chat->messages($user, $conversation, $afterId);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, SalesTeamConversation $conversation): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        abort_unless((int) $conversation->sales_team_id === (int) $team->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
            'reply_to_id' => ['nullable', 'integer'],
        ]);

        $message = $this->chat->sendMessage(
            $user,
            $conversation,
            $data['body'],
            isset($data['reply_to_id']) ? (int) $data['reply_to_id'] : null
        );

        $this->chat->markRead($user, $conversation);

        return response()->json([
            'message' => $this->chat->serializeMessage($message, $user),
        ]);
    }

    public function react(Request $request, SalesTeamMessage $message): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        $message->loadMissing('conversation');
        abort_unless((int) $message->conversation->sales_team_id === (int) $team->id, 403);

        $data = $request->validate([
            'emoji' => ['required', 'string', 'max:16'],
        ]);

        $result = $this->chat->toggleReaction($user, $message, $data['emoji']);

        return response()->json($result);
    }

    public function read(SalesTeamConversation $conversation): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        abort_unless((int) $conversation->sales_team_id === (int) $team->id, 403);

        $this->chat->markRead($user, $conversation);

        return response()->json([
            'ok' => true,
            'unread_total' => $this->chat->unreadCount($user, $team),
        ]);
    }

    public function destroyMessage(SalesTeamMessage $message): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);
        $message->loadMissing('conversation');
        abort_unless((int) $message->conversation->sales_team_id === (int) $team->id, 403);

        $this->chat->deleteMessage($user, $message, $team);

        return response()->json(['ok' => true]);
    }

    public function unread(): JsonResponse
    {
        $user = Auth::user();
        $team = $this->chat->teamOrFail($user);

        return response()->json([
            'unread_total' => $this->chat->unreadCount($user, $team),
        ]);
    }
}
