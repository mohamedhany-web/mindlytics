<?php

namespace App\Services;

use App\Models\SalesTeam;
use App\Models\SalesTeamConversation;
use App\Models\SalesTeamConversationParticipant;
use App\Models\SalesTeamMessage;
use App\Models\SalesTeamMessageReaction;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesTeamChatService
{
    public function __construct(
        private SalesTeamService $teamService
    ) {}

    public function teamOrFail(User $user): SalesTeam
    {
        $team = $this->teamService->teamFor($user);
        if (! $team) {
            throw new HttpResponseException(response()->json([
                'message' => 'لا يوجد فريق مبيعات مرتبط بحسابك.',
            ], 403));
        }

        return $team;
    }

    /** @return list<int> */
    public function staffUserIds(SalesTeam $team): array
    {
        return $team->allStaffUserIds();
    }

    public function assertSameTeam(User $user, SalesTeam $team): void
    {
        $ids = $this->staffUserIds($team);
        if (! in_array((int) $user->id, $ids, true)) {
            throw new HttpResponseException(response()->json([
                'message' => 'غير مصرح بالوصول لهذا الفريق.',
            ], 403));
        }
    }

    public function assertParticipant(User $user, SalesTeamConversation $conversation): void
    {
        $isParticipant = SalesTeamConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isParticipant) {
            throw new HttpResponseException(response()->json([
                'message' => 'لست مشاركاً في هذه المحادثة.',
            ], 403));
        }
    }

    public function ensureTeamChannel(SalesTeam $team, User $actor): SalesTeamConversation
    {
        $this->assertSameTeam($actor, $team);

        return DB::transaction(function () use ($team, $actor) {
            $conversation = SalesTeamConversation::query()
                ->where('sales_team_id', $team->id)
                ->where('type', SalesTeamConversation::TYPE_TEAM)
                ->first();

            if (! $conversation) {
                $conversation = SalesTeamConversation::query()->create([
                    'sales_team_id' => $team->id,
                    'type' => SalesTeamConversation::TYPE_TEAM,
                    'title' => 'قناة الفريق — '.$team->name,
                    'created_by' => $actor->id,
                ]);
            }

            $this->syncTeamParticipants($conversation, $team);

            return $conversation->fresh(['participants:id,name']);
        });
    }

    public function syncTeamParticipants(SalesTeamConversation $conversation, SalesTeam $team): void
    {
        $staffIds = $this->staffUserIds($team);
        $existing = SalesTeamConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($staffIds as $userId) {
            if (! in_array($userId, $existing, true)) {
                SalesTeamConversationParticipant::query()->create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    public function findOrCreateDirect(SalesTeam $team, User $actor, int $otherUserId): SalesTeamConversation
    {
        $this->assertSameTeam($actor, $team);
        $staffIds = $this->staffUserIds($team);

        if (! in_array($otherUserId, $staffIds, true)) {
            throw ValidationException::withMessages([
                'user_id' => 'يجب أن يكون المستلم عضواً في نفس الفريق.',
            ]);
        }

        if ($otherUserId === (int) $actor->id) {
            throw ValidationException::withMessages([
                'user_id' => 'لا يمكن بدء محادثة مع نفسك.',
            ]);
        }

        $existing = SalesTeamConversation::query()
            ->where('sales_team_id', $team->id)
            ->where('type', SalesTeamConversation::TYPE_DIRECT)
            ->whereHas('participantRows', fn ($q) => $q->where('user_id', $actor->id))
            ->whereHas('participantRows', fn ($q) => $q->where('user_id', $otherUserId))
            ->whereDoesntHave('participantRows', function ($q) use ($actor, $otherUserId) {
                $q->whereNotIn('user_id', [$actor->id, $otherUserId]);
            })
            ->first();

        if ($existing) {
            return $existing->load(['participants:id,name']);
        }

        return DB::transaction(function () use ($team, $actor, $otherUserId) {
            $conversation = SalesTeamConversation::query()->create([
                'sales_team_id' => $team->id,
                'type' => SalesTeamConversation::TYPE_DIRECT,
                'title' => null,
                'created_by' => $actor->id,
            ]);

            foreach ([$actor->id, $otherUserId] as $uid) {
                SalesTeamConversationParticipant::query()->create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $uid,
                ]);
            }

            return $conversation->load(['participants:id,name']);
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listConversations(User $user, SalesTeam $team): Collection
    {
        $this->assertSameTeam($user, $team);
        $this->ensureTeamChannel($team, $user);

        $conversations = SalesTeamConversation::query()
            ->where('sales_team_id', $team->id)
            ->whereHas('participantRows', fn ($q) => $q->where('user_id', $user->id))
            ->with(['participants:id,name'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        $lastByConversation = SalesTeamMessage::query()
            ->whereIn('conversation_id', $conversations->pluck('id'))
            ->with('user:id,name')
            ->orderByDesc('id')
            ->get()
            ->unique('conversation_id')
            ->keyBy('conversation_id');

        return $conversations->map(function (SalesTeamConversation $c) use ($user, $lastByConversation) {
            $c->setRelation('messages', collect($lastByConversation->get($c->id) ? [$lastByConversation->get($c->id)] : []));

            return $this->serializeConversation($c, $user);
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listMembers(User $user, SalesTeam $team): array
    {
        $this->assertSameTeam($user, $team);
        $ids = $this->staffUserIds($team);

        return User::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'is_me' => (int) $u->id === (int) $user->id,
                'is_manager' => (int) $u->id === (int) $team->manager_id,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function messages(User $user, SalesTeamConversation $conversation, ?int $afterId = null, int $limit = 50): array
    {
        $this->assertParticipant($user, $conversation);

        $query = SalesTeamMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with([
                'user:id,name',
                'replyTo:id,user_id,body',
                'replyTo.user:id,name',
                'reactions.user:id,name',
            ]);

        if ($afterId) {
            $query->where('id', '>', $afterId)->orderBy('id');
        } else {
            $query->orderByDesc('id')->limit($limit);
        }

        $rows = $query->get();
        if (! $afterId) {
            $rows = $rows->sortBy('id')->values();
        }

        return $rows->map(fn (SalesTeamMessage $m) => $this->serializeMessage($m, $user))->all();
    }

    public function sendMessage(
        User $user,
        SalesTeamConversation $conversation,
        string $body,
        ?int $replyToId = null
    ): SalesTeamMessage {
        $this->assertParticipant($user, $conversation);
        $body = trim($body);
        if ($body === '') {
            throw ValidationException::withMessages([
                'body' => 'اكتب رسالة.',
            ]);
        }
        if (mb_strlen($body) > 4000) {
            throw ValidationException::withMessages([
                'body' => 'الرسالة طويلة جداً.',
            ]);
        }

        if ($replyToId) {
            $reply = SalesTeamMessage::query()
                ->where('conversation_id', $conversation->id)
                ->whereKey($replyToId)
                ->first();
            if (! $reply) {
                throw ValidationException::withMessages([
                    'reply_to_id' => 'الرسالة المُراد الرد عليها غير موجودة.',
                ]);
            }
        }

        $message = SalesTeamMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $body,
            'reply_to_id' => $replyToId,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return $message->load([
            'user:id,name',
            'replyTo:id,user_id,body',
            'replyTo.user:id,name',
            'reactions.user:id,name',
        ]);
    }

    public function toggleReaction(User $user, SalesTeamMessage $message, string $emoji): array
    {
        $conversation = $message->conversation;
        $this->assertParticipant($user, $conversation);

        $emoji = trim($emoji);
        if ($emoji === '' || mb_strlen($emoji) > 16) {
            throw ValidationException::withMessages([
                'emoji' => 'إيموجي غير صالح.',
            ]);
        }

        $existing = SalesTeamMessageReaction::query()
            ->where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->where('emoji', $emoji)
            ->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            SalesTeamMessageReaction::query()->create([
                'message_id' => $message->id,
                'user_id' => $user->id,
                'emoji' => $emoji,
            ]);
            $added = true;
        }

        $message->load(['reactions.user:id,name']);

        return [
            'added' => $added,
            'message' => $this->serializeMessage($message, $user),
        ];
    }

    public function markRead(User $user, SalesTeamConversation $conversation): void
    {
        $this->assertParticipant($user, $conversation);

        SalesTeamConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }

    public function unreadCount(User $user, SalesTeam $team): int
    {
        $this->assertSameTeam($user, $team);

        $participantRows = SalesTeamConversationParticipant::query()
            ->where('user_id', $user->id)
            ->whereHas('conversation', fn ($q) => $q->where('sales_team_id', $team->id))
            ->get(['conversation_id', 'last_read_at']);

        $total = 0;
        foreach ($participantRows as $row) {
            $q = SalesTeamMessage::query()
                ->where('conversation_id', $row->conversation_id)
                ->where('user_id', '!=', $user->id);

            if ($row->last_read_at) {
                $q->where('created_at', '>', $row->last_read_at);
            }

            $total += $q->count();
        }

        return $total;
    }

    public function deleteMessage(User $user, SalesTeamMessage $message, SalesTeam $team): void
    {
        $conversation = $message->conversation()->first() ?? $message->conversation;
        if ((int) $conversation->sales_team_id !== (int) $team->id) {
            throw new HttpResponseException(response()->json([
                'message' => 'غير مصرح.',
            ], 403));
        }

        $isOwner = (int) $message->user_id === (int) $user->id;
        $isManager = (int) $team->manager_id === (int) $user->id;

        if ($isManager) {
            $this->assertSameTeam($user, $team);
        } else {
            $this->assertParticipant($user, $conversation);
            if (! $isOwner) {
                throw new HttpResponseException(response()->json([
                    'message' => 'لا يمكنك حذف هذه الرسالة.',
                ], 403));
            }
        }

        $message->delete();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeConversation(SalesTeamConversation $c, User $viewer): array
    {
        $last = $c->messages->first();
        $other = null;
        if ($c->isDirect()) {
            $other = $c->participants->first(fn (User $u) => (int) $u->id !== (int) $viewer->id);
        }

        $pivot = SalesTeamConversationParticipant::query()
            ->where('conversation_id', $c->id)
            ->where('user_id', $viewer->id)
            ->first();

        $unreadQ = SalesTeamMessage::query()
            ->where('conversation_id', $c->id)
            ->where('user_id', '!=', $viewer->id);
        if ($pivot?->last_read_at) {
            $unreadQ->where('created_at', '>', $pivot->last_read_at);
        }
        $unread = $unreadQ->count();

        return [
            'id' => $c->id,
            'type' => $c->type,
            'title' => $c->isTeamChannel()
                ? ($c->title ?: 'قناة الفريق')
                : ($other?->name ?? 'محادثة خاصة'),
            'is_team' => $c->isTeamChannel(),
            'other_user' => $other ? ['id' => $other->id, 'name' => $other->name] : null,
            'last_message' => $last ? [
                'id' => $last->id,
                'body' => $last->body,
                'user_name' => $last->user?->name,
                'created_at' => $last->created_at?->toIso8601String(),
            ] : null,
            'last_message_at' => $c->last_message_at?->toIso8601String(),
            'unread' => $unread,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeMessage(SalesTeamMessage $m, User $viewer): array
    {
        $grouped = [];
        foreach ($m->reactions as $r) {
            $key = $r->emoji;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'emoji' => $key,
                    'count' => 0,
                    'mine' => false,
                    'users' => [],
                ];
            }
            $grouped[$key]['count']++;
            $grouped[$key]['users'][] = $r->user?->name;
            if ((int) $r->user_id === (int) $viewer->id) {
                $grouped[$key]['mine'] = true;
            }
        }

        return [
            'id' => $m->id,
            'body' => $m->body,
            'user_id' => $m->user_id,
            'user_name' => $m->user?->name,
            'is_mine' => (int) $m->user_id === (int) $viewer->id,
            'reply_to' => $m->replyTo ? [
                'id' => $m->replyTo->id,
                'body' => $m->replyTo->body,
                'user_name' => $m->replyTo->user?->name,
            ] : null,
            'reactions' => array_values($grouped),
            'created_at' => $m->created_at?->toIso8601String(),
            'created_at_human' => $m->created_at?->format('H:i'),
        ];
    }
}
