<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                    'media' => $this->extractMediaMeta($msg),
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

            $params = is_array($log->template_params) ? $log->template_params : [];
            $contactName = trim((string) ($params['contact_name'] ?? ''));
            if ($contactName === '') {
                $responseMeta = is_array($log->response_data) ? $log->response_data : [];
                $contactName = trim((string) ($responseMeta['contact_name'] ?? ''));
            }

            if (! $conversation->user_id) {
                $conversation->user_id = $this->guessUserId($phone);
            }

            if ($contactName === '') {
                $lead = app(WhatsAppCrmService::class)->findLeadByPhone($phone);
                $contactName = trim((string) ($lead?->name ?? ''));
                if ($lead && ! $conversation->sales_lead_id) {
                    $conversation->sales_lead_id = $lead->id;
                }
            }

            if ($contactName !== '' && (! $conversation->contact_name || ! $conversation->isCustomerDisplayName($conversation->contact_name))) {
                $conversation->contact_name = $contactName;
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
            $conversation->save();

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
        ?int $userId = null,
        array $components = []
    ): array {
        $normalized = $this->whatsapp->formatPhoneNumber($phone);

        $conversation = WhatsAppConversation::query()->firstOrCreate(
            ['phone_number' => $normalized],
            ['unread_count' => 0]
        );

        $result = $this->sendTemplateReply($conversation, $templateName, $languageCode, $components, $userId);

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

    /**
     * @return array{success: bool, message?: WhatsAppConversationMessage, error?: string, requires_template?: bool}
     */
    public function sendMediaReply(
        WhatsAppConversation $conversation,
        UploadedFile $file,
        ?int $userId = null,
        ?string $caption = null,
        bool $voiceNote = false,
    ): array {
        $mime = (string) $file->getMimeType();
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $waType = $this->resolveWaMediaType($mime, $extension);
        if ($waType === null) {
            return ['success' => false, 'error' => 'نوع الملف غير مدعوم. استخدم صورة (jpg/png) أو صوت (ogg/mp3/m4a/aac).'];
        }

        $tempPath = $file->getRealPath();
        if (! $tempPath) {
            return ['success' => false, 'error' => 'تعذّر قراءة الملف'];
        }

        if ((int) $file->getSize() <= 0) {
            return ['success' => false, 'error' => 'الملف فارغ — سجّل لثانية على الأقل ثم أعد المحاولة.'];
        }

        $uploadPath = $tempPath;
        $uploadMime = $mime;
        $uploadFilename = $file->getClientOriginalName();
        $cleanup = null;

        if ($voiceNote || ($waType === 'audio' && $this->shouldConvertToOggOpus($mime, $extension))) {
            $prepared = $this->prepareOggOpusVoiceNote($tempPath, $mime, $extension, $voiceNote);
            if (isset($prepared['error'])) {
                return ['success' => false, 'error' => $prepared['error']];
            }
            $uploadPath = $prepared['path'];
            $uploadMime = $prepared['mime'];
            $uploadFilename = $prepared['filename'];
            $cleanup = $prepared['cleanup'] ?? null;
            $waType = 'audio';
        }

        $upload = $this->cloud->uploadMediaFile($uploadPath, $uploadMime, $waType, $uploadFilename);
        if ($cleanup && is_file($cleanup) && $uploadPath !== $cleanup) {
            @unlink($cleanup);
        }

        if (! ($upload['success'] ?? false)) {
            return ['success' => false, 'error' => $upload['error'] ?? 'فشل رفع الوسائط'];
        }

        $mediaId = (string) ($upload['media_id'] ?? '');
        $cacheContent = @file_get_contents($uploadPath) ?: '';
        if ($cleanup && is_file($cleanup)) {
            @unlink($cleanup);
        }

        $result = $this->whatsapp->sendMediaReply(
            $conversation->phone_number,
            $waType,
            $mediaId,
            ['user_id' => $userId],
            $caption,
        );

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
            'message' => tap($this->recordOutbound($conversation, [
                'body' => $caption,
                'message_type' => $waType,
                'whatsapp_message_id' => $result['whatsapp_id'] ?? null,
                'sent_by_user_id' => $userId,
                'payload' => [
                    'media' => [
                        'id' => $mediaId,
                        'mime_type' => $uploadMime,
                        'filename' => $uploadFilename,
                        'local' => true,
                    ],
                ],
            ]), function (WhatsAppConversationMessage $message) use ($cacheContent, $uploadMime) {
                if ($cacheContent !== '') {
                    $cacheKey = 'whatsapp/media/' . $message->id . $this->mediaCacheExtension(['mime_type' => $uploadMime]);
                    Storage::disk('local')->put($cacheKey, $cacheContent);
                }
            }),
        ];
    }

    public function streamMessageMedia(WhatsAppConversationMessage $message): StreamedResponse
    {
        $media = $this->mediaMetaForMessage($message);
        if ($media === null) {
            abort(404);
        }

        $disk = Storage::disk('local');
        $cacheKey = 'whatsapp/media/' . $message->id . $this->mediaCacheExtension($media);
        if ($disk->exists($cacheKey)) {
            return response()->stream(function () use ($disk, $cacheKey) {
                echo $disk->get($cacheKey);
            }, 200, [
                'Content-Type' => $media['mime_type'] ?? 'application/octet-stream',
                'Cache-Control' => 'private, max-age=86400',
            ]);
        }

        $mediaId = (string) ($media['id'] ?? '');
        if ($mediaId === '') {
            abort(404);
        }

        $download = $this->cloud->downloadMediaContent($mediaId);
        if (! ($download['success'] ?? false)) {
            abort(404, $download['error'] ?? 'تعذّر تحميل الوسائط');
        }

        $mime = (string) ($download['mime_type'] ?? $media['mime_type'] ?? 'application/octet-stream');
        $content = (string) ($download['content'] ?? '');
        $disk->put($cacheKey, $content);

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=86400',
        ]);
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
     * @return array<string, mixed>
     */
    public function serializeMessage(WhatsAppConversationMessage $message, ?string $mediaUrl = null): array
    {
        $at = $message->created_at;
        $media = $this->mediaMetaForMessage($message);

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
            'media' => $media ? [
                'url' => $mediaUrl,
                'mime_type' => $media['mime_type'] ?? null,
                'filename' => $media['filename'] ?? null,
                'kind' => $this->mediaKindForType($message->message_type),
            ] : null,
            'created_at' => $at?->toIso8601String(),
            'created_at_human' => $at
                ? ($at->isToday() ? $at->format('H:i') : $at->format('d/m H:i'))
                : '',
            'is_inbound' => $message->isInbound(),
        ];
    }

    public function messageHasMedia(WhatsAppConversationMessage $message): bool
    {
        return $this->mediaMetaForMessage($message) !== null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function mediaMetaForMessage(WhatsAppConversationMessage $message): ?array
    {
        $payload = is_array($message->payload) ? $message->payload : [];
        if (is_array($payload['media'] ?? null) && ! empty($payload['media']['id'])) {
            return $payload['media'];
        }

        return $this->extractMediaMeta(is_array($payload['raw'] ?? null) ? $payload['raw'] : []);
    }

    /**
     * @param  array<string, mixed>  $msg
     * @return array<string, mixed>|null
     */
    private function extractMediaMeta(array $msg): ?array
    {
        $type = (string) ($msg['type'] ?? '');
        $key = match ($type) {
            'image', 'audio', 'video', 'document', 'sticker' => $type,
            default => null,
        };

        if ($key === null || ! is_array($msg[$key] ?? null)) {
            return null;
        }

        $block = $msg[$key];
        $id = (string) ($block['id'] ?? '');
        if ($id === '') {
            return null;
        }

        return [
            'id' => $id,
            'mime_type' => $block['mime_type'] ?? null,
            'filename' => $block['filename'] ?? null,
            'sha256' => $block['sha256'] ?? null,
        ];
    }

    private function mediaKindForType(?string $messageType): string
    {
        return match ($messageType) {
            'image', 'sticker' => 'image',
            'audio' => 'audio',
            'video' => 'video',
            'document' => 'document',
            default => 'file',
        };
    }

    /**
     * @param  array<string, mixed>  $media
     */
    private function mediaCacheExtension(array $media): string
    {
        $mime = strtolower((string) ($media['mime_type'] ?? ''));
        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            return '.jpg';
        }
        if (str_contains($mime, 'png')) {
            return '.png';
        }
        if (str_contains($mime, 'ogg')) {
            return '.ogg';
        }
        if (str_contains($mime, 'mpeg') || str_contains($mime, 'mp3')) {
            return '.mp3';
        }
        if (str_contains($mime, 'mp4')) {
            return '.m4a';
        }
        if (str_contains($mime, 'webp')) {
            return '.webp';
        }

        return '.bin';
    }

    private function resolveWaMediaType(string $mime, ?string $extension = null): ?string
    {
        $mime = strtolower($mime);
        $extension = strtolower((string) $extension);

        if (str_starts_with($mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return 'image';
        }

        if (
            str_starts_with($mime, 'audio/')
            || in_array($extension, ['ogg', 'opus', 'mp3', 'm4a', 'aac', 'amr', 'webm'], true)
        ) {
            return 'audio';
        }

        if (str_starts_with($mime, 'video/') || in_array($extension, ['mp4', '3gp'], true)) {
            return 'video';
        }

        return null;
    }

    private function shouldConvertToOggOpus(string $mime, string $extension): bool
    {
        $mime = strtolower($mime);
        $extension = strtolower($extension);

        if (str_contains($mime, 'ogg') || $extension === 'ogg') {
            return false;
        }

        // تنسيقات تسجيل المتصفح (WebM / MP4) — تحتاج تحويل لرسالة صوتية
        return str_contains($mime, 'webm')
            || str_contains($mime, 'mp4')
            || in_array($extension, ['webm', 'm4a', 'mp4'], true);
    }

    /**
     * تحضير ملف OGG/Opus لرسالة صوتية (Voice Note) في واتساب.
     *
     * @return array{path: string, mime: string, filename: string, cleanup?: string}|array{error: string}
     */
    private function prepareOggOpusVoiceNote(
        string $inputPath,
        string $mime,
        string $extension,
        bool $required,
    ): array {
        $mime = strtolower($mime);
        $extension = strtolower($extension);

        if (str_contains($mime, 'ogg') || $extension === 'ogg') {
            return [
                'path' => $inputPath,
                'mime' => 'audio/ogg',
                'filename' => 'voice-' . time() . '.ogg',
                'cleanup' => null,
            ];
        }

        $converted = $this->convertToOggOpus($inputPath);
        if (isset($converted['path'])) {
            return [
                'path' => $converted['path'],
                'mime' => 'audio/ogg',
                'filename' => 'voice-' . time() . '.ogg',
                'cleanup' => $converted['path'],
            ];
        }

        if ($required) {
            return [
                'error' => 'لإرسال رسالة صوتية (Voice Note) يجب تحويل التسجيل إلى OGG/Opus — ثبّت ffmpeg على السيرفر أو عيّن FFMPEG_PATH في .env',
            ];
        }

        return ['error' => 'تعذّر تحويل الملف الصوتي. جرّب ملف OGG أو ثبّت ffmpeg.'];
    }

    /**
     * @return array{path: string}|array{error: string}
     */
    private function convertToOggOpus(string $inputPath): array
    {
        $ffmpeg = $this->findFfmpegBinary();
        if ($ffmpeg === null) {
            return ['error' => 'ffmpeg_missing'];
        }

        $out = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wa_voice_' . uniqid('', true) . '.ogg';
        $command = [
            $ffmpeg,
            '-y',
            '-i',
            $inputPath,
            '-c:a',
            'libopus',
            '-b:a',
            '24k',
            '-ar',
            '48000',
            '-ac',
            '1',
            '-application',
            'voip',
            '-t',
            '300',
            $out,
        ];

        if (! $this->runProcessWithTimeout($command, 45)) {
            if (is_file($out)) {
                @unlink($out);
            }

            return ['error' => 'conversion_failed'];
        }

        if (! is_file($out) || filesize($out) === 0) {
            if (is_file($out)) {
                @unlink($out);
            }

            return ['error' => 'conversion_failed'];
        }

        return ['path' => $out];
    }

    /**
     * @return array{path: string}|array{error: string}
     */
    private function convertWebmToOgg(string $webmPath): array
    {
        return $this->convertToOggOpus($webmPath);
    }

    /**
     * @param  list<string>  $command
     */
    private function runProcessWithTimeout(array $command, int $timeoutSeconds): bool
    {
        if ($command === []) {
            return false;
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($command, $descriptors, $pipes);
        if (! is_resource($process)) {
            return false;
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $deadline = time() + max(1, $timeoutSeconds);
        $status = proc_get_status($process);

        while ($status['running']) {
            if (time() >= $deadline) {
                proc_terminate($process);
                break;
            }
            usleep(100_000);
            $status = proc_get_status($process);
        }

        foreach ([$pipes[1], $pipes[2]] as $pipe) {
            while (is_resource($pipe) && ! feof($pipe)) {
                fread($pipe, 8192);
            }
            if (is_resource($pipe)) {
                fclose($pipe);
            }
        }

        $exitCode = proc_close($process);

        return $exitCode === 0;
    }

    public function voiceNoteConversionReady(): bool
    {
        try {
            return $this->findFfmpegBinary() !== null;
        } catch (\Throwable) {
            return false;
        }
    }

    private function shellExecDisabled(): bool
    {
        if (! function_exists('shell_exec')) {
            return true;
        }

        $disabled = ini_get('disable_functions');
        if (! is_string($disabled) || trim($disabled) === '') {
            return false;
        }

        return in_array('shell_exec', array_map('trim', explode(',', $disabled)), true);
    }

    private function findFfmpegBinary(): ?string
    {
        $candidates = [];
        $envPath = trim((string) env('FFMPEG_PATH', ''));
        if ($envPath !== '') {
            $candidates[] = $envPath;
        }

        $bundledLinux = base_path('bin/ffmpeg/linux-amd64/ffmpeg');
        if (is_file($bundledLinux)) {
            $candidates[] = $bundledLinux;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $bundledWin = base_path('bin/ffmpeg/win-amd64/ffmpeg.exe');
            if (is_file($bundledWin)) {
                $candidates[] = $bundledWin;
            }
            $candidates[] = 'C:\\ffmpeg\\bin\\ffmpeg.exe';
            $candidates[] = 'C:\\xampp\\ffmpeg\\bin\\ffmpeg.exe';
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                if (! is_executable($candidate) && PHP_OS_FAMILY !== 'Windows') {
                    @chmod($candidate, 0755);
                }

                return $candidate;
            }
        }

        if ($this->shellExecDisabled()) {
            return null;
        }

        $candidates[] = 'ffmpeg';

        foreach ($candidates as $candidate) {
            if (str_contains($candidate, '\\') || str_contains($candidate, '/')) {
                if (is_file($candidate)) {
                    return $candidate;
                }

                continue;
            }

            $which = PHP_OS_FAMILY === 'Windows'
                ? @shell_exec('where ' . $candidate . ' 2>NUL')
                : @shell_exec('which ' . $candidate . ' 2>/dev/null');

            $which = trim((string) $which);
            if ($which !== '') {
                $line = trim(explode("\n", $which)[0]);

                return $line !== '' ? $line : $candidate;
            }
        }

        return null;
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

            app(WhatsAppQueueService::class)->handleAfterInbound($conversation->fresh(), $message);
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
