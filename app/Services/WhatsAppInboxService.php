<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Cache;
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

        $type = (string) ($msg['type'] ?? 'text');
        if ($type === 'reaction') {
            return $this->recordInboundReaction($msg);
        }

        $phone = $this->whatsapp->formatPhoneNumber($from);
        [$body, $messageType] = $this->extractInboundContent($msg);
        $contextWaId = (string) ($msg['context']['id'] ?? '');
        $contextPreview = $this->contextPreviewForWaId($contextWaId);

        return DB::transaction(function () use ($phone, $msg, $metadata, $waMessageId, $body, $messageType, $contextWaId, $contextPreview) {
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
                'context_wa_message_id' => $contextWaId !== '' ? $contextWaId : null,
                'context_preview' => $contextPreview,
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

            $this->afterInboundMessage($conversation, $message);

            $inboundAt = now()->toIso8601String();
            Cache::put('whatsapp:webhook:last_inbound_at', $inboundAt, now()->addDays(90));
            \App\Support\WhatsAppCloudSettings::recordWebhookHit('inbound');

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
        if (! Schema::hasTable('whatsapp_conversations') || ! Schema::hasTable('whats_app_messages')) {
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
    public function sendTextReply(
        WhatsAppConversation $conversation,
        string $body,
        ?int $userId = null,
        ?string $contextWaMessageId = null,
        ?string $contextPreview = null,
    ): array {
        $body = trim($body);
        if ($body === '') {
            return ['success' => false, 'error' => 'نص الرد فارغ'];
        }

        $result = $this->whatsapp->sendTextReply($conversation->phone_number, $body, [
            'user_id' => $userId,
            'context_message_id' => $contextWaMessageId,
        ]);

        if (! ($result['success'] ?? false)) {
            $error = $result['error'] ?? 'فشل الإرسال';

            return [
                'success' => false,
                'error' => $error,
                'requires_template' => $this->errorRequiresTemplate($error),
            ];
        }

        return [
            'success' => true,
            'message' => $this->recordOutbound($conversation, [
                'body' => $body,
                'message_type' => 'text',
                'whatsapp_message_id' => $result['whatsapp_id'] ?? null,
                'context_wa_message_id' => $contextWaMessageId,
                'context_preview' => $contextPreview,
                'sent_by_user_id' => $userId,
            ]),
        ];
    }

    /**
     * @return array{success: bool, message?: WhatsAppConversationMessage, error?: string}
     */
    public function sendReaction(
        WhatsAppConversation $conversation,
        WhatsAppConversationMessage $target,
        string $emoji,
        ?int $userId = null,
    ): array {
        if (! $target->isInbound()) {
            return ['success' => false, 'error' => 'يمكن التفاعل فقط مع رسائل العميل (Meta Cloud API).'];
        }

        $waId = (string) ($target->whatsapp_message_id ?? '');
        if ($waId === '') {
            return ['success' => false, 'error' => 'لا يوجد معرّف Meta لهذه الرسالة.'];
        }

        $emoji = trim($emoji);
        if ($emoji === '') {
            return ['success' => false, 'error' => 'اختر إيموجي للتفاعل'];
        }

        $result = $this->whatsapp->sendReaction($conversation->phone_number, $waId, $emoji, [
            'user_id' => $userId,
        ]);

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل إرسال التفاعل'];
        }

        $target->update(['reaction_emoji' => $emoji]);

        return [
            'success' => true,
            'message' => $target->fresh('sentBy'),
        ];
    }

    /**
     * @param  array<string, mixed>  $msg
     */
    private function recordInboundReaction(array $msg): ?WhatsAppConversationMessage
    {
        $reaction = is_array($msg['reaction'] ?? null) ? $msg['reaction'] : [];
        $targetWaId = (string) ($reaction['message_id'] ?? '');
        if ($targetWaId === '') {
            return null;
        }

        $emoji = (string) ($reaction['emoji'] ?? '');
        $target = WhatsAppConversationMessage::query()->where('whatsapp_message_id', $targetWaId)->first();
        if (! $target) {
            return null;
        }

        $target->update(['reaction_emoji' => $emoji !== '' ? $emoji : null]);

        return $target->fresh();
    }

    private function contextPreviewForWaId(string $waId): ?string
    {
        if ($waId === '') {
            return null;
        }

        $parent = WhatsAppConversationMessage::query()->where('whatsapp_message_id', $waId)->first();
        if (! $parent) {
            return null;
        }

        return mb_substr($parent->displayBody(), 0, 200);
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
     * @return array{success: bool, conversation?: WhatsAppConversation, message?: WhatsAppConversationMessage, error?: string, requires_template?: bool}
     */
    public function startConversationWithMessage(
        string $phone,
        string $body,
        ?int $userId = null
    ): array {
        $normalized = $this->whatsapp->formatPhoneNumber($phone);

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone_number' => $normalized],
            ['unread_count' => 0]
        );

        if (! $conversation->user_id) {
            $conversation->user_id = $this->guessUserId($normalized);
            $conversation->save();
        }

        $result = $this->sendTextReply($conversation, $body, $userId);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        return array_merge($result, ['conversation' => $conversation->fresh(['user:id,name'])]);
    }

    private function errorRequiresTemplate(string $error): bool
    {
        $lower = mb_strtolower($error);

        return str_contains($lower, 'template')
            || str_contains($lower, '24 hour')
            || str_contains($lower, '24 ساعة')
            || str_contains($lower, 're-engagement')
            || str_contains($lower, 'قالب');
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
            'context_wa_message_id' => $data['context_wa_message_id'] ?? null,
            'context_preview' => $data['context_preview'] ?? null,
            'status' => 'sent',
            'sent_by_user_id' => $data['sent_by_user_id'] ?? null,
            'template_name' => $data['template_name'] ?? null,
            'template_params' => $data['template_params'] ?? null,
            'sent_at' => now(),
            'payload' => $data['payload'] ?? null,
        ]);

        $this->touchConversationAfterMessage($conversation, $message, $data['sent_at'] ?? now());
        $this->afterOutboundMessage($conversation, $message);

        return $message;
    }

    private function touchConversationAfterMessage(
        WhatsAppConversation $conversation,
        WhatsAppConversationMessage $message,
        \DateTimeInterface $sentAt
    ): void {
        $preview = mb_substr($message->displayBody(), 0, 200);

        $conversation->update([
            'last_message_at' => now(),
            'last_message_preview' => $preview,
            'last_message_direction' => $message->direction,
            'user_id' => $conversation->user_id,
            'contact_name' => $conversation->contact_name,
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
        $at = $message->created_at;

        return [
            'id' => $message->id,
            'direction' => $message->direction,
            'body' => $message->displayBody(),
            'message_type' => $message->message_type,
            'status' => $message->status,
            'template_name' => $message->template_name,
            'error_message' => $message->error_message,
            'sent_by' => $message->sentBy?->name,
            'whatsapp_message_id' => $message->whatsapp_message_id,
            'context_wa_message_id' => $message->context_wa_message_id,
            'context_preview' => $message->context_preview,
            'reaction_emoji' => $message->reaction_emoji,
            'created_at' => $at?->toIso8601String(),
            'created_at_human' => $at
                ? ($at->isToday() ? $at->format('H:i') : $at->format('d/m H:i'))
                : '',
            'is_inbound' => $message->isInbound(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeConversation(WhatsAppConversation $conversation, string $audience = 'admin'): array
    {
        $conversation->loadMissing(['user:id,name,phone', 'assignee:id,name', 'tags', 'contact', 'salesLead']);

        $payload = [
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

        if (app(WhatsAppCrmService::class)->crmTablesReady()) {
            $payload['crm'] = app(WhatsAppCrmService::class)->serializeCrm($conversation, $audience);
        }

        return $payload;
    }

    public function applyConversationFilters($query, array $filters = []): void
    {
        if (! empty($filters)) {
            $query->filterCrm($filters);
        }

        if (auth()->check()) {
            $query->visibleTo(auth()->user());
        }
    }

    private function afterInboundMessage(WhatsAppConversation $conversation, WhatsAppConversationMessage $message): void
    {
        try {
            $crm = app(WhatsAppCrmService::class);
            if (! $crm->crmTablesReady()) {
                return;
            }

            $conversation = $conversation->fresh();
            $crm->ensureContactForConversation($conversation);
            $crm->logEvent(
                $conversation->fresh(),
                \App\Models\WhatsAppConversationEvent::TYPE_MESSAGE_INBOUND,
                'رسالة واردة',
                mb_substr($message->displayBody(), 0, 200)
            );
            $crm->touchContactActivity($conversation->fresh());
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function afterOutboundMessage(WhatsAppConversation $conversation, WhatsAppConversationMessage $message): void
    {
        try {
            $crm = app(WhatsAppCrmService::class);
            if (! $crm->crmTablesReady()) {
                return;
            }

            $conversation = $conversation->fresh();
            $crm->ensureContactForConversation($conversation);

            $type = $message->template_name
                ? \App\Models\WhatsAppConversationEvent::TYPE_TEMPLATE_SENT
                : \App\Models\WhatsAppConversationEvent::TYPE_MESSAGE_OUTBOUND;

            $crm->logEvent(
                $conversation,
                $type,
                $message->template_name ? 'قالب Meta' : 'رد صادر',
                mb_substr($message->displayBody(), 0, 200),
                ['template_name' => $message->template_name]
            );

            $crm->touchContactActivity($conversation);
            $crm->logOutboundToSalesLead($conversation, $message->displayBody(), $message->sent_by_user_id);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
