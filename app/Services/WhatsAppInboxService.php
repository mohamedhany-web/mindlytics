<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WhatsAppInboxService
{
    public function __construct(
        private WhatsAppService $whatsapp,
        private WhatsAppCloudService $cloud,
    ) {}

    /**
     * @param  array<string, mixed>  $msg
     * @param  array<string, mixed>  $metadata
     */
    public function recordInbound(array $msg, array $metadata = []): ?WhatsAppConversationMessage
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return null;
        }

        $from = (string) ($msg['from'] ?? '');
        if ($from === '') {
            return null;
        }

        $waMessageId = (string) ($msg['id'] ?? '');
        if ($waMessageId !== '' && WhatsAppConversationMessage::query()->where('whatsapp_message_id', $waMessageId)->exists()) {
            return null;
        }

        $phone = $this->whatsapp->formatPhoneNumber($from);
        [$body, $messageType] = $this->extractInboundContent($msg);

        return DB::transaction(function () use ($phone, $msg, $metadata, $waMessageId, $body, $messageType) {
            $conversation = WhatsAppConversation::query()->firstOrCreate(
                ['phone_number' => $phone],
                ['contact_name' => null, 'unread_count' => 0]
            );

            if (! $conversation->user_id) {
                $conversation->user_id = $this->guessUserId($phone);
            }

            $contactName = (string) ($msg['profile']['name'] ?? '');
            if ($contactName !== '' && $conversation->contact_name !== $contactName) {
                $conversation->contact_name = $contactName;
            }

            $message = WhatsAppConversationMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => WhatsAppConversationMessage::DIRECTION_INBOUND,
                'body' => $body,
                'message_type' => $messageType,
                'whatsapp_message_id' => $waMessageId ?: null,
                'status' => 'received',
                'payload' => [
                    'raw' => $msg,
                    'metadata' => $metadata,
                ],
            ]);

            $preview = mb_substr($body ?: $message->displayBody(), 0, 200);

            $conversation->update([
                'last_message_at' => now(),
                'last_message_preview' => $preview,
                'last_message_direction' => WhatsAppConversationMessage::DIRECTION_INBOUND,
                'unread_count' => (int) $conversation->unread_count + 1,
                'user_id' => $conversation->user_id,
                'contact_name' => $conversation->contact_name,
            ]);

            return $message;
        });
    }

    /**
     * @return array{0: ?string, 1: string}
     */
    private function extractInboundContent(array $msg): array
    {
        $type = (string) ($msg['type'] ?? 'text');

        return match ($type) {
            'text' => [(string) ($msg['text']['body'] ?? ''), 'text'],
            'button' => [(string) ($msg['button']['text'] ?? $msg['button']['payload'] ?? ''), 'button'],
            'interactive' => [$this->extractInteractiveText($msg['interactive'] ?? []), 'interactive'],
            'image' => [(string) ($msg['image']['caption'] ?? ''), 'image'],
            'document' => [(string) ($msg['document']['caption'] ?? $msg['document']['filename'] ?? ''), 'document'],
            'audio' => [null, 'audio'],
            'video' => [(string) ($msg['video']['caption'] ?? ''), 'video'],
            'sticker' => [null, 'sticker'],
            'location' => [$this->formatLocation($msg['location'] ?? []), 'location'],
            default => [null, $type],
        };
    }

    /**
     * @param  array<string, mixed>  $interactive
     */
    private function extractInteractiveText(array $interactive): string
    {
        $type = (string) ($interactive['type'] ?? '');

        if ($type === 'button_reply') {
            return (string) ($interactive['button_reply']['title'] ?? $interactive['button_reply']['id'] ?? '');
        }

        if ($type === 'list_reply') {
            return (string) ($interactive['list_reply']['title'] ?? $interactive['list_reply']['id'] ?? '');
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $location
     */
    private function formatLocation(array $location): string
    {
        $lat = $location['latitude'] ?? '';
        $lng = $location['longitude'] ?? '';
        $name = $location['name'] ?? '';

        return trim($name . ' (' . $lat . ', ' . $lng . ')');
    }

    private function guessUserId(string $normalizedPhone): ?int
    {
        $local = $normalizedPhone;
        if (str_starts_with($local, '20') && strlen($local) > 2) {
            $local = '0' . substr($local, 2);
        }

        $user = User::query()
            ->whereNotNull('phone')
            ->where(function ($q) use ($normalizedPhone, $local) {
                $q->where('phone', $normalizedPhone)
                    ->orWhere('phone', '+' . $normalizedPhone)
                    ->orWhere('phone', $local)
                    ->orWhere('phone', 'like', '%' . substr($normalizedPhone, -10));
            })
            ->first();

        return $user?->id;
    }

    public function mirrorOutboundWhatsAppMessage(WhatsAppMessage $log): ?WhatsAppConversationMessage
    {
        if (! Schema::hasTable('whatsapp_conversations')) {
            return null;
        }

        if (! in_array($log->status, ['sent', 'delivered', 'read'], true)) {
            return null;
        }

        $phone = $this->whatsapp->formatPhoneNumber((string) $log->phone_number);
        if ($phone === '') {
            return null;
        }

        $waMessageId = $log->whatsapp_message_id;
        if ($waMessageId && WhatsAppConversationMessage::query()->where('whatsapp_message_id', $waMessageId)->exists()) {
            return null;
        }

        return DB::transaction(function () use ($log, $phone, $waMessageId) {
            $conversation = WhatsAppConversation::query()->firstOrCreate(
                ['phone_number' => $phone],
                ['unread_count' => 0]
            );

            if (! $conversation->user_id) {
                $conversation->user_id = $log->user_id ?: $this->guessUserId($phone);
            }

            $sentAt = $log->sent_at ?? $log->created_at ?? now();

            $message = WhatsAppConversationMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => WhatsAppConversationMessage::DIRECTION_OUTBOUND,
                'body' => $log->message,
                'message_type' => $log->type ?: 'text',
                'whatsapp_message_id' => $waMessageId,
                'status' => in_array($log->status, ['delivered', 'read'], true) ? $log->status : 'sent',
                'sent_by_user_id' => $log->user_id,
                'template_name' => $log->template_name,
                'template_params' => $log->template_params,
                'sent_at' => $sentAt,
                'delivered_at' => $log->delivered_at,
                'read_at' => $log->read_at,
            ]);

            $this->touchConversationAfterMessage($conversation, $message, $sentAt);

            return $message;
        });
    }

    public function syncRecentOutboundLogs(int $limit = 500): int
    {
        if (! Schema::hasTable('whatsapp_conversations') || ! Schema::hasTable('whatsapp_messages')) {
            return 0;
        }

        $synced = 0;

        WhatsAppMessage::query()
            ->whereIn('status', ['sent', 'delivered', 'read'])
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->each(function (WhatsAppMessage $log) use (&$synced) {
                if ($this->mirrorOutboundWhatsAppMessage($log)) {
                    $synced++;
                }
            });

        return $synced;
    }

    public function isWithinServiceWindow(WhatsAppConversation $conversation): bool
    {
        $lastInbound = $conversation->messages()
            ->where('direction', WhatsAppConversationMessage::DIRECTION_INBOUND)
            ->latest('created_at')
            ->first();

        if (! $lastInbound) {
            return false;
        }

        return $lastInbound->created_at->greaterThan(now()->subHours(24));
    }

    /**
     * @return array{success: bool, message?: WhatsAppConversationMessage, error?: string}
     */
    public function sendTextReply(WhatsAppConversation $conversation, string $body, ?int $userId = null): array
    {
        $body = trim($body);
        if ($body === '') {
            return ['success' => false, 'error' => 'نص الرد فارغ'];
        }

        if (! $this->isWithinServiceWindow($conversation)) {
            return [
                'success' => false,
                'error' => 'انتهت نافذة الـ 24 ساعة — استخدم قالب Meta معتمد لبدء المحادثة (مثل hello_world).',
                'requires_template' => true,
            ];
        }

        $result = $this->whatsapp->sendTextReply($conversation->phone_number, $body, [
            'user_id' => $userId,
        ]);

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال'];
        }

        return [
            'success' => true,
            'message' => $this->recordOutbound($conversation, [
                'body' => $body,
                'message_type' => 'text',
                'whatsapp_message_id' => $result['whatsapp_id'] ?? null,
                'sent_by_user_id' => $userId,
            ]),
        ];
    }

    /**
     * @return array{success: bool, message?: WhatsAppConversationMessage, error?: string}
     */
    public function sendTemplateReply(
        WhatsAppConversation $conversation,
        string $templateName,
        string $languageCode = 'en_US',
        array $components = [],
        ?int $userId = null
    ): array {
        $templateName = trim($templateName);
        if ($templateName === '') {
            return ['success' => false, 'error' => 'اسم القالب مطلوب'];
        }

        $result = $this->whatsapp->sendTemplate(
            $conversation->phone_number,
            $templateName,
            $languageCode,
            $components,
            ['user_id' => $userId, 'skip_log' => false]
        );

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل إرسال القالب'];
        }

        return [
            'success' => true,
            'message' => $this->recordOutbound($conversation, [
                'body' => null,
                'message_type' => 'template',
                'template_name' => $templateName,
                'template_params' => ['language' => $languageCode, 'components' => $components],
                'whatsapp_message_id' => $result['whatsapp_id'] ?? null,
                'sent_by_user_id' => $userId,
            ]),
        ];
    }

    /**
     * @return array{success: bool, conversation?: WhatsAppConversation, message?: WhatsAppConversationMessage, error?: string}
     */
    public function startConversationWithTemplate(
        string $phone,
        string $templateName,
        string $languageCode = 'en_US',
        ?int $userId = null
    ): array {
        $normalized = $this->whatsapp->formatPhoneNumber($phone);

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone_number' => $normalized],
            ['unread_count' => 0]
        );

        $result = $this->sendTemplateReply($conversation, $templateName, $languageCode, [], $userId);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        return array_merge($result, ['conversation' => $conversation->fresh()]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function recordOutbound(WhatsAppConversation $conversation, array $data): WhatsAppConversationMessage
    {
        $waMessageId = $data['whatsapp_message_id'] ?? null;
        if ($waMessageId) {
            $existing = WhatsAppConversationMessage::query()
                ->where('whatsapp_message_id', $waMessageId)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $message = WhatsAppConversationMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => WhatsAppConversationMessage::DIRECTION_OUTBOUND,
            'body' => $data['body'] ?? null,
            'message_type' => $data['message_type'] ?? 'text',
            'whatsapp_message_id' => $data['whatsapp_message_id'] ?? null,
            'status' => 'sent',
            'sent_by_user_id' => $data['sent_by_user_id'] ?? null,
            'template_name' => $data['template_name'] ?? null,
            'template_params' => $data['template_params'] ?? null,
            'sent_at' => now(),
            'payload' => $data['payload'] ?? null,
        ]);

        $this->touchConversationAfterMessage($conversation, $message, $data['sent_at'] ?? now());

        return $message;
    }

    private function touchConversationAfterMessage(
        WhatsAppConversation $conversation,
        WhatsAppConversationMessage $message,
        \DateTimeInterface $sentAt
    ): void {
        $preview = mb_substr($message->displayBody(), 0, 200);
        $sentAtCarbon = $sentAt instanceof \Carbon\Carbon ? $sentAt : \Carbon\Carbon::parse($sentAt);

        if ($conversation->last_message_at && $conversation->last_message_at->greaterThan($sentAtCarbon)) {
            return;
        }

        $conversation->update([
            'last_message_at' => $sentAtCarbon,
            'last_message_preview' => $preview,
            'last_message_direction' => WhatsAppConversationMessage::DIRECTION_OUTBOUND,
            'user_id' => $conversation->user_id,
        ]);
    }

    public function markConversationRead(WhatsAppConversation $conversation): void
    {
        if ($conversation->unread_count > 0) {
            $conversation->update(['unread_count' => 0]);
        }
    }

    /**
     * @param  array<string, mixed>  $status
     */
    public function applyDeliveryStatus(string $waMessageId, array $status): void
    {
        if (! Schema::hasTable('whatsapp_conversation_messages')) {
            return;
        }

        $message = WhatsAppConversationMessage::query()
            ->where('whatsapp_message_id', $waMessageId)
            ->latest()
            ->first();

        if (! $message) {
            return;
        }

        $state = (string) ($status['status'] ?? '');
        $updates = [
            'payload' => array_merge($message->payload ?? [], ['webhook_status' => $status]),
        ];

        if ($state === 'delivered') {
            $updates['status'] = 'delivered';
            $updates['delivered_at'] = now();
        } elseif ($state === 'read') {
            $updates['status'] = 'read';
            $updates['read_at'] = now();
        } elseif ($state === 'failed') {
            $errorDetail = $status['errors'][0] ?? [];
            $updates['status'] = 'failed';
            $updates['error_message'] = is_array($errorDetail)
                ? $this->cloud->humanizeSendError(
                    $errorDetail,
                    (string) ($errorDetail['title'] ?? $errorDetail['message'] ?? 'فشل التسليم')
                )
                : 'فشل التسليم';
        } elseif ($state === 'sent') {
            $updates['status'] = 'sent';
        }

        $message->update($updates);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function serializeMessage(WhatsAppConversationMessage $message): array
    {
        return [
            'id' => $message->id,
            'direction' => $message->direction,
            'body' => $message->displayBody(),
            'message_type' => $message->message_type,
            'status' => $message->status,
            'template_name' => $message->template_name,
            'error_message' => $message->error_message,
            'sent_by' => $message->sentBy?->name,
            'created_at' => $message->created_at?->toIso8601String(),
            'created_at_human' => $message->created_at?->format('Y-m-d H:i'),
            'is_inbound' => $message->isInbound(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeConversation(WhatsAppConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'phone_number' => $conversation->phone_number,
            'display_name' => $conversation->displayName(),
            'formatted_phone' => $conversation->formattedPhone(),
            'last_message_preview' => $conversation->last_message_preview,
            'last_message_at' => $conversation->last_message_at?->toIso8601String(),
            'last_message_at_human' => $conversation->last_message_at?->diffForHumans(),
            'last_message_direction' => $conversation->last_message_direction,
            'unread_count' => $conversation->unread_count,
            'within_service_window' => $this->isWithinServiceWindow($conversation),
            'user_name' => $conversation->user?->name,
            'user_phone' => $conversation->user?->phone,
        ];
    }
}
