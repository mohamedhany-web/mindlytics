<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PeerChatMessage;
use App\Models\PeerChatThread;
use App\Models\PeerConnectionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * محادثات مباشرة بين طلاب بعد قبول طلب التواصل.
 */
class StudentPeerChatController extends Controller
{
    public function threads(Request $request): JsonResponse
    {
        $me = $request->user();

        $peerIds = PeerConnectionRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($me) {
                $q->where('requester_id', $me->id)->orWhere('recipient_id', $me->id);
            })
            ->get()
            ->map(fn (PeerConnectionRequest $r) => (int) $r->requester_id === $me->id
                ? (int) $r->recipient_id
                : (int) $r->requester_id)
            ->unique()
            ->values();

        $users = User::query()
            ->whereIn('id', $peerIds)
            ->select('id', 'name', 'headline', 'profile_image', 'profile_image_disk')
            ->get()
            ->keyBy('id');

        $out = [];
        foreach ($peerIds as $pid) {
            $peer = $users->get($pid);
            if (! $peer) {
                continue;
            }
            $thread = PeerChatThread::findOrCreateForUsers($me->id, $pid);

            $last = PeerChatMessage::query()->where('thread_id', $thread->id)->orderByDesc('id')->first();

            $preview = '';
            if ($last) {
                $preview = match ($last->kind) {
                    'image' => '[image]',
                    'voice' => '[voice]',
                    default => (mb_strlen($last->body ?? '') > 120 ? mb_substr($last->body ?? '', 0, 120).'…' : ($last->body ?? '')),
                };
            }

            $out[] = [
                'peer_user_id' => $peer->id,
                'name' => $peer->name,
                'headline' => $peer->headline,
                'profile_image_url' => $peer->profile_image_url ?? null,
                'thread_id' => $thread->id,
                'last_message_preview' => $preview,
                'last_message_at' => $last?->created_at?->toIso8601String(),
            ];
        }

        usort($out, function ($a, $b) {
            $ta = $a['last_message_at'] ?? '';
            $tb = $b['last_message_at'] ?? '';

            return strcmp((string) $tb, (string) $ta);
        });

        return response()->json(['threads' => $out]);
    }

    public function messages(Request $request, User $peer): JsonResponse
    {
        $me = $request->user();
        if ($peer->id === $me->id) {
            return response()->json(['message' => 'Invalid', 'code' => 'self'], 400);
        }
        if (! $peer->isStudent() || ! $peer->is_active) {
            return response()->json(['message' => 'Not found', 'code' => 'not_found'], 404);
        }
        if (! $this->arePeersConnected($me->id, $peer->id)) {
            return response()->json(['message' => 'Not connected', 'code' => 'not_connected'], 403);
        }

        $thread = PeerChatThread::findOrCreateForUsers($me->id, $peer->id);

        $afterId = (int) $request->query('after_id', 0);
        $limit = min(max((int) $request->query('limit', 50), 1), 100);

        $q = PeerChatMessage::query()
            ->where('thread_id', $thread->id)
            ->with(['sender:id,name', 'replyTo.sender:id,name'])
            ->orderBy('id');

        if ($afterId > 0) {
            $q->where('id', '>', $afterId);
        }

        $items = $q->limit($limit)->get()->map(fn (PeerChatMessage $m) => $this->serializePeerMessage($m));

        return response()->json([
            'thread_id' => $thread->id,
            'peer_user_id' => $peer->id,
            'messages' => $items,
        ]);
    }

    public function send(Request $request, User $peer): JsonResponse
    {
        $me = $request->user();
        if ($peer->id === $me->id) {
            return response()->json(['message' => 'Invalid', 'code' => 'self'], 400);
        }
        if (! $peer->isStudent() || ! $peer->is_active) {
            return response()->json(['message' => 'Not found', 'code' => 'not_found'], 404);
        }
        if (! $this->arePeersConnected($me->id, $peer->id)) {
            return response()->json(['message' => 'Not connected', 'code' => 'not_connected'], 403);
        }

        $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'reply_to_id' => ['nullable', 'integer', 'exists:peer_chat_messages,id'],
            'image' => ['nullable', 'file', 'image', 'max:12288'],
            'voice' => ['nullable', 'file', 'max:25600', 'mimes:m4a,mp3,mpga,wav,ogg,weba,webm,aac'],
        ]);

        $body = trim((string) $request->input('body', ''));
        $replyToId = $request->input('reply_to_id') ? (int) $request->input('reply_to_id') : null;

        $thread = PeerChatThread::findOrCreateForUsers($me->id, $peer->id);

        if ($replyToId) {
            $parent = PeerChatMessage::query()->where('thread_id', $thread->id)->whereKey($replyToId)->first();
            if (! $parent) {
                throw ValidationException::withMessages(['reply_to_id' => ['Invalid reply target.']]);
            }
        }

        $kind = 'text';
        $meta = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("peer-chat/{$thread->id}", 'public');
            $kind = 'image';
            $meta = ['url' => Storage::disk('public')->url($path)];
        } elseif ($request->hasFile('voice')) {
            $path = $request->file('voice')->store("peer-chat/{$thread->id}/voice", 'public');
            $kind = 'voice';
            $meta = [
                'url' => Storage::disk('public')->url($path),
                'mime' => $request->file('voice')->getClientMimeType(),
            ];
        }

        if ($body === '' && $kind === 'text') {
            throw ValidationException::withMessages([
                'body' => ['Add text or attach an image or voice note.'],
            ]);
        }

        $msg = PeerChatMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $me->id,
            'reply_to_id' => $replyToId,
            'kind' => $kind,
            'body' => $body === '' ? '' : $body,
            'meta' => $meta,
        ]);

        $thread->update(['last_message_at' => $msg->created_at]);

        $msg->load(['sender:id,name', 'replyTo.sender:id,name']);

        return response()->json([
            'message' => $this->serializePeerMessage($msg),
        ], 201);
    }

    private function arePeersConnected(int $a, int $b): bool
    {
        return PeerConnectionRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($a, $b) {
                $q->where(function ($q2) use ($a, $b) {
                    $q2->where('requester_id', $a)->where('recipient_id', $b);
                })->orWhere(function ($q2) use ($a, $b) {
                    $q2->where('requester_id', $b)->where('recipient_id', $a);
                });
            })
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function serializePeerMessage(PeerChatMessage $m): array
    {
        $reply = null;
        if ($m->relationLoaded('replyTo') && $m->replyTo instanceof PeerChatMessage) {
            $p = $m->replyTo;
            $preview = $p->kind === 'text'
                ? (mb_strlen($p->body ?? '') > 160 ? mb_substr($p->body ?? '', 0, 160).'…' : ($p->body ?? ''))
                : '['.$p->kind.']';
            $senderName = '';
            if ($p->relationLoaded('sender') && $p->sender) {
                $senderName = $p->sender->name ?? '';
            }
            $reply = [
                'id' => $p->id,
                'user_name' => $senderName,
                'preview' => $preview,
            ];
        }

        return [
            'id' => $m->id,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->relationLoaded('sender') ? ($m->sender->name ?? '') : '',
            'body' => $m->body ?? '',
            'kind' => $m->kind ?: 'text',
            'meta' => $m->meta,
            'reply_to' => $reply,
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
