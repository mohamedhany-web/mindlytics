<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use App\Support\MetaSocialSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MetaSocialInboxService
{
    public function __construct(
        private MetaSocialGraphService $graph,
    ) {}

    public function tablesReady(): bool
    {
        return Schema::hasTable('meta_social_conversations')
            && Schema::hasTable('meta_social_messages');
    }

    /**
     * @return array{success: bool, synced?: int, error?: string}
     */
    public function syncConversationsForPage(MetaSocialPage $page, string $platform = 'messenger'): array
    {
        $token = (string) $page->page_access_token;
        if ($token === '') {
            return ['success' => false, 'error' => 'Page Access Token مفقود'];
        }

        $graphPlatform = $platform === MetaSocialConversation::PLATFORM_INSTAGRAM ? 'INSTAGRAM' : 'MESSENGER';

        try {
            $response = Http::timeout(45)->get("{$this->graph->graphUrl()}/{$page->page_id}/conversations", [
                'platform' => $graphPlatform,
                'fields' => 'id,updated_time,participants,snippet,unread_count',
                'limit' => 50,
                'access_token' => $token,
            ]);

            if (! $response->successful()) {
                return ['success' => false, 'error' => $this->graph->graphErrorMessage($response->json() ?? [], 'تعذّر مزامنة المحادثات')];
            }

            $synced = 0;
            foreach ($response->json('data') ?? [] as $thread) {
                $this->upsertConversationFromThread($page, $platform, $thread);
                $synced++;
            }

            return ['success' => true, 'synced' => $synced];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $thread
     */
    public function upsertConversationFromThread(MetaSocialPage $page, string $platform, array $thread): MetaSocialConversation
    {
        $participant = $this->resolveParticipant($thread, (string) $page->page_id);
        $updated = isset($thread['updated_time']) ? Carbon::parse($thread['updated_time']) : now();

        return MetaSocialConversation::query()->updateOrCreate(
            [
                'meta_social_page_id' => $page->id,
                'platform' => $platform,
                'participant_id' => $participant['id'],
            ],
            [
                'participant_name' => $participant['name'],
                'thread_id' => (string) ($thread['id'] ?? ''),
                'last_message_at' => $updated,
                'last_message_preview' => mb_substr((string) ($thread['snippet'] ?? ''), 0, 500),
                'unread_count' => (int) ($thread['unread_count'] ?? 0),
                'status' => MetaSocialConversation::STATUS_OPEN,
                'meta' => ['thread' => $thread],
            ],
        );
    }

    /**
     * @return array{success: bool, message?: MetaSocialMessage, error?: string}
     */
    public function sendReply(MetaSocialConversation $conversation, string $body, ?int $userId = null): array
    {
        $conversation->loadMissing('page');
        $page = $conversation->page;
        if (! $page) {
            return ['success' => false, 'error' => 'الصفحة غير موجودة'];
        }

        $result = $this->graph->sendTextMessage(
            $page,
            (string) $conversation->participant_id,
            $body,
            (string) $conversation->platform,
        );

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال'];
        }

        $message = MetaSocialMessage::query()->create([
            'meta_social_conversation_id' => $conversation->id,
            'meta_message_id' => $result['message_id'] ?? null,
            'direction' => MetaSocialMessage::DIRECTION_OUTBOUND,
            'message_type' => 'text',
            'body' => $body,
            'sent_by_user_id' => $userId,
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'last_message_preview' => mb_substr($body, 0, 500),
        ]);

        return ['success' => true, 'message' => $message];
    }

    public function markConversationRead(MetaSocialConversation $conversation): void
    {
        $conversation->update(['unread_count' => 0]);
    }

    /**
     * @param  array<string, mixed>  $messagingEvent
     */
    public function ingestMessagingEvent(MetaSocialPage $page, array $messagingEvent, string $platform): ?MetaSocialMessage
    {
        $senderId = (string) ($messagingEvent['sender']['id'] ?? '');
        if ($senderId === '' || $senderId === (string) $page->page_id) {
            return null;
        }

        $messagePayload = $messagingEvent['message'] ?? null;
        if (! is_array($messagePayload)) {
            return null;
        }

        $conversation = MetaSocialConversation::query()->firstOrCreate(
            [
                'meta_social_page_id' => $page->id,
                'platform' => $platform,
                'participant_id' => $senderId,
            ],
            [
                'participant_name' => null,
                'status' => MetaSocialConversation::STATUS_OPEN,
            ],
        );

        $body = (string) ($messagePayload['text'] ?? '');
        $type = 'text';
        $attachmentUrl = null;

        if (isset($messagePayload['attachments'][0]) && is_array($messagePayload['attachments'][0])) {
            $att = $messagePayload['attachments'][0];
            $type = (string) ($att['type'] ?? 'file');
            $attachmentUrl = $att['payload']['url'] ?? null;
        }

        $metaMessageId = (string) ($messagePayload['mid'] ?? '');
        if ($metaMessageId !== '' && MetaSocialMessage::query()->where('meta_message_id', $metaMessageId)->exists()) {
            return null;
        }

        $sentAt = isset($messagingEvent['timestamp'])
            ? Carbon::createFromTimestampMs((int) $messagingEvent['timestamp'])
            : now();

        $message = MetaSocialMessage::query()->create([
            'meta_social_conversation_id' => $conversation->id,
            'meta_message_id' => $metaMessageId ?: null,
            'direction' => MetaSocialMessage::DIRECTION_INBOUND,
            'message_type' => $type,
            'body' => $body,
            'attachment_url' => $attachmentUrl,
            'sent_at' => $sentAt,
            'meta' => ['event' => $messagingEvent],
        ]);

        $conversation->update([
            'last_message_at' => $sentAt,
            'last_message_preview' => mb_substr($message->displayBody(), 0, 500),
            'unread_count' => (int) $conversation->unread_count + 1,
            'status' => MetaSocialConversation::STATUS_OPEN,
        ]);

        return $message;
    }

    /**
     * @param  array<string, mixed>  $thread
     * @return array{id: string, name: ?string}
     */
    private function resolveParticipant(array $thread, string $pageId): array
    {
        $participants = $thread['participants']['data'] ?? [];
        if (! is_array($participants)) {
            return ['id' => 'unknown', 'name' => null];
        }

        foreach ($participants as $p) {
            $id = (string) ($p['id'] ?? '');
            if ($id !== '' && $id !== $pageId) {
                return ['id' => $id, 'name' => $p['name'] ?? null];
            }
        }

        $first = $participants[0] ?? [];

        return ['id' => (string) ($first['id'] ?? 'unknown'), 'name' => $first['name'] ?? null];
    }
}
