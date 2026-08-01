<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MetaSocialInboxService
{
    public function __construct(
        private MetaSocialGraphService $graph,
        private MetaSocialCrmService $crm,
    ) {}

    public function tablesReady(): bool
    {
        return Schema::hasTable('meta_social_conversations')
            && Schema::hasTable('meta_social_messages');
    }

    /**
     * مزامنة كل المحادثات وكل الرسائل المتاحة من Graph (مع pagination).
     *
     * @return array{success: bool, synced?: int, messages?: int, error?: string}
     */
    public function syncConversationsForPage(MetaSocialPage $page, string $platform = 'messenger'): array
    {
        $token = (string) $page->page_access_token;
        if ($token === '') {
            return ['success' => false, 'error' => 'Page Access Token مفقود'];
        }

        $graphPlatform = $platform === MetaSocialConversation::PLATFORM_INSTAGRAM ? 'INSTAGRAM' : 'MESSENGER';

        try {
            $url = "{$this->graph->graphUrl()}/{$page->page_id}/conversations";
            $params = [
                'platform' => $graphPlatform,
                'fields' => 'id,updated_time,participants,snippet,unread_count',
                'limit' => 100,
                'access_token' => $token,
            ];

            $synced = 0;
            $messagesImported = 0;
            $pagesFetched = 0;
            $maxConversationPages = 50; // حماية: حتى ~5000 محادثة

            while ($url && $pagesFetched < $maxConversationPages) {
                $response = $pagesFetched === 0
                    ? Http::timeout(60)->get($url, $params)
                    : Http::timeout(60)->get($url);

                if (! $response->successful()) {
                    return [
                        'success' => false,
                        'error' => $this->graph->graphErrorMessage($response->json() ?? [], 'تعذّر مزامنة المحادثات'),
                        'synced' => $synced,
                        'messages' => $messagesImported,
                    ];
                }

                $json = $response->json() ?? [];
                foreach ($json['data'] ?? [] as $thread) {
                    if (! is_array($thread)) {
                        continue;
                    }
                    $conversation = $this->upsertConversationFromThread($page, $platform, $thread);
                    $msgResult = $this->syncAllMessagesForConversation($conversation);
                    $messagesImported += (int) ($msgResult['imported'] ?? 0);
                    $synced++;
                }

                $url = $json['paging']['next'] ?? null;
                $pagesFetched++;
                // بعد الصفحة الأولى نستخدم next كاملة (فيها access_token)
                $params = [];
            }

            return [
                'success' => true,
                'synced' => $synced,
                'messages' => $messagesImported,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * جلب كل رسائل محادثة واحدة من Graph حرفياً (كل الصفحات).
     *
     * @return array{success: bool, imported?: int, total?: int, error?: string}
     */
    public function syncAllMessagesForConversation(MetaSocialConversation $conversation): array
    {
        $conversation->loadMissing('page');
        $page = $conversation->page;
        if (! $page || ! $page->page_access_token) {
            return ['success' => false, 'error' => 'الصفحة أو التوكن غير متاح'];
        }

        $threadId = (string) ($conversation->thread_id ?: '');
        if ($threadId === '') {
            return ['success' => false, 'error' => 'لا يوجد thread_id — زامن المحادثات أولاً'];
        }

        try {
            $url = "{$this->graph->graphUrl()}/{$threadId}/messages";
            $params = [
                'fields' => 'id,message,from,created_time,attachments{image_data,file_url,type,payload}',
                'limit' => 100,
                'access_token' => (string) $page->page_access_token,
            ];

            $imported = 0;
            $seen = 0;
            $pagesFetched = 0;
            $maxMessagePages = 100; // حماية: حتى ~10000 رسالة لكل محادثة

            while ($url && $pagesFetched < $maxMessagePages) {
                $response = $pagesFetched === 0
                    ? Http::timeout(60)->get($url, $params)
                    : Http::timeout(60)->get($url);

                if (! $response->successful()) {
                    // بعض المحادثات قد ترفض الرسائل القديمة — نكمل بما لدينا
                    if ($pagesFetched === 0) {
                        return [
                            'success' => false,
                            'error' => $this->graph->graphErrorMessage($response->json() ?? [], 'تعذّر جلب الرسائل'),
                            'imported' => $imported,
                        ];
                    }
                    break;
                }

                $json = $response->json() ?? [];
                $batch = $json['data'] ?? [];
                if (! is_array($batch) || $batch === []) {
                    break;
                }

                $seen += count($batch);
                $imported += $this->storeGraphMessages($conversation, $page, $batch);

                $url = $json['paging']['next'] ?? null;
                $pagesFetched++;
                $params = [];
            }

            // حدّث معاينة آخر رسالة من أحدث سجل محلي
            $latest = MetaSocialMessage::query()
                ->where('meta_social_conversation_id', $conversation->id)
                ->orderByDesc('sent_at')
                ->orderByDesc('id')
                ->first();

            if ($latest) {
                $conversation->update([
                    'last_message_at' => $latest->sent_at ?? $conversation->last_message_at,
                    'last_message_preview' => mb_substr($latest->displayBody(), 0, 500),
                ]);
            }

            return [
                'success' => true,
                'imported' => $imported,
                'total' => $seen,
            ];
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

        $conversation = MetaSocialConversation::query()->firstOrNew([
            'meta_social_page_id' => $page->id,
            'platform' => $platform,
            'participant_id' => $participant['id'],
        ]);

        $conversation->fill([
            'thread_id' => (string) ($thread['id'] ?? $conversation->thread_id ?? ''),
            'last_message_at' => $updated,
            'last_message_preview' => mb_substr((string) ($thread['snippet'] ?? $conversation->last_message_preview ?? ''), 0, 500),
            'unread_count' => (int) ($thread['unread_count'] ?? $conversation->unread_count ?? 0),
            'status' => $conversation->status ?: MetaSocialConversation::STATUS_OPEN,
            'meta' => array_merge($conversation->meta ?? [], ['thread' => $thread]),
        ]);

        // لا نمسح الاسم الموجود بقيمة فارغة من المزامنة
        if (! empty($participant['name'])) {
            $conversation->participant_name = $participant['name'];
        }

        $conversation->save();

        return $conversation;
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     */
    public function storeGraphMessages(MetaSocialConversation $conversation, MetaSocialPage $page, array $messages): int
    {
        if ($messages === []) {
            return 0;
        }

        // Graph يرجع الأحدث أولاً — نخزّن بأي ترتيب؛ العرض يعتمد على sent_at,id
        $imported = 0;

        foreach ($messages as $msg) {
            if (! is_array($msg)) {
                continue;
            }

            $mid = (string) ($msg['id'] ?? '');
            if ($mid !== '' && MetaSocialMessage::query()->where('meta_message_id', $mid)->exists()) {
                continue;
            }

            $fromId = (string) ($msg['from']['id'] ?? '');
            $direction = ($fromId !== '' && $fromId === (string) $page->page_id)
                ? MetaSocialMessage::DIRECTION_OUTBOUND
                : MetaSocialMessage::DIRECTION_INBOUND;

            $body = (string) ($msg['message'] ?? '');
            $type = 'text';
            $attachmentUrl = null;

            $attachments = $msg['attachments']['data'] ?? $msg['attachments'] ?? null;
            if (is_array($attachments) && isset($attachments[0]) && is_array($attachments[0])) {
                $att = $attachments[0];
                if (! empty($att['image_data']['url'])) {
                    $type = 'image';
                    $attachmentUrl = $att['image_data']['url'];
                } elseif (! empty($att['file_url'])) {
                    $type = (string) ($att['type'] ?? 'file');
                    $attachmentUrl = $att['file_url'];
                } elseif (! empty($att['payload']['url'])) {
                    $type = (string) ($att['type'] ?? 'file');
                    $attachmentUrl = $att['payload']['url'];
                }
            }

            $sentAt = isset($msg['created_time']) ? Carbon::parse($msg['created_time']) : now();

            try {
                MetaSocialMessage::query()->create([
                    'meta_social_conversation_id' => $conversation->id,
                    'meta_message_id' => $mid !== '' ? $mid : null,
                    'direction' => $direction,
                    'message_type' => $type,
                    'body' => $body,
                    'attachment_url' => $attachmentUrl,
                    'sent_at' => $sentAt,
                    'meta' => ['graph_message' => $msg],
                ]);
                $imported++;
            } catch (QueryException) {
                // unique race — تجاهل التكرار
            }
        }

        return $imported;
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

        $metaMessageId = $result['message_id'] ?? null;
        if ($metaMessageId && MetaSocialMessage::query()->where('meta_message_id', $metaMessageId)->exists()) {
            $message = MetaSocialMessage::query()->where('meta_message_id', $metaMessageId)->first();
        } else {
            try {
                $message = MetaSocialMessage::query()->create([
                    'meta_social_conversation_id' => $conversation->id,
                    'meta_message_id' => $metaMessageId,
                    'direction' => MetaSocialMessage::DIRECTION_OUTBOUND,
                    'message_type' => 'text',
                    'body' => $body,
                    'sent_by_user_id' => $userId,
                    'sent_at' => now(),
                ]);
            } catch (QueryException) {
                $message = MetaSocialMessage::query()->where('meta_message_id', $metaMessageId)->first();
            }
        }

        $conversation->update([
            'last_message_at' => now(),
            'last_message_preview' => mb_substr($body, 0, 500),
        ]);

        if ($this->crm->crmReady()) {
            $this->crm->logOutboundToSalesLead($conversation, $body, $userId);
        }

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

        // تجاهل echo للرسائل الصادرة من الصفحة لتجنب التكرار
        if (! empty($messagePayload['is_echo'])) {
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

        if (! $conversation->participant_name && $this->crm->crmReady()) {
            $this->crm->enrichParticipantProfile($conversation);
            $conversation->refresh();
        }

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

        try {
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
        } catch (QueryException) {
            return null;
        }

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
