<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * رسائل المجموعة للطالب (نص، رد، صورة، ملف صوتي) — واجهة JSON للموبايل.
 */
class StudentGroupChatController extends Controller
{
    public function messages(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();
        if (! $this->isActiveGroupMember($user->id, $group)) {
            return response()->json(['message' => 'Forbidden', 'code' => 'forbidden'], 403);
        }

        $afterId = (int) $request->query('after_id', 0);
        $limit = min(max((int) $request->query('limit', 50), 1), 100);

        $q = GroupMessage::query()
            ->where('group_id', $group->id)
            ->with(['user:id,name', 'replyTo.user:id,name'])
            ->orderBy('id');

        if ($afterId > 0) {
            $q->where('id', '>', $afterId);
        }

        $items = $q->limit($limit)->get()->map(fn (GroupMessage $m) => $this->serializeGroupMessage($m));

        return response()->json([
            'group_id' => $group->id,
            'messages' => $items,
        ]);
    }

    public function send(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();
        if (! $this->isActiveGroupMember($user->id, $group)) {
            return response()->json(['message' => 'Forbidden', 'code' => 'forbidden'], 403);
        }

        $request->validate([
            'body' => ['nullable', 'string', 'max:4000'],
            'reply_to_id' => ['nullable', 'integer', 'exists:group_messages,id'],
            'image' => ['nullable', 'file', 'image', 'max:12288'],
            'voice' => ['nullable', 'file', 'max:25600', 'mimes:m4a,mp3,mpga,wav,ogg,weba,webm,aac'],
        ]);

        $body = trim((string) $request->input('body', ''));
        $replyToId = $request->input('reply_to_id') ? (int) $request->input('reply_to_id') : null;

        if ($replyToId) {
            $parent = GroupMessage::query()->where('group_id', $group->id)->whereKey($replyToId)->first();
            if (! $parent) {
                throw ValidationException::withMessages(['reply_to_id' => ['Invalid reply target.']]);
            }
        }

        $kind = 'text';
        $meta = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("group-chat/{$group->id}", 'public');
            $kind = 'image';
            $meta = [
                'url' => Storage::disk('public')->url($path),
            ];
        } elseif ($request->hasFile('voice')) {
            $path = $request->file('voice')->store("group-chat/{$group->id}/voice", 'public');
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

        $msg = GroupMessage::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'body' => $body === '' ? '' : $body,
            'reply_to_id' => $replyToId,
            'kind' => $kind,
            'meta' => $meta,
        ]);
        $msg->load(['user:id,name', 'replyTo.user:id,name']);

        return response()->json([
            'message' => $this->serializeGroupMessage($msg),
        ], 201);
    }

    private function isActiveGroupMember(int $userId, Group $group): bool
    {
        if ($group->status !== 'active') {
            return false;
        }

        return $group->members()->where('users.id', $userId)->exists();
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeGroupMessage(GroupMessage $m): array
    {
        $reply = null;
        if ($m->relationLoaded('replyTo') && $m->replyTo instanceof GroupMessage) {
            $p = $m->replyTo;
            $preview = $p->kind === 'text'
                ? (mb_strlen($p->body ?? '') > 160 ? mb_substr($p->body ?? '', 0, 160).'…' : ($p->body ?? ''))
                : '['.$p->kind.']';
            $reply = [
                'id' => $p->id,
                'user_name' => $p->relationLoaded('user') ? ($p->user->name ?? '') : '',
                'preview' => $preview,
            ];
        }

        return [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'user_name' => $m->relationLoaded('user') ? ($m->user->name ?? '') : '',
            'body' => $m->body ?? '',
            'kind' => $m->kind ?: 'text',
            'meta' => $m->meta,
            'reply_to' => $reply,
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
