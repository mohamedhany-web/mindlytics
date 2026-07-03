<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppMessage;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppInboxService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $mode = $this->hubParam($request, 'mode');
        $token = $this->hubParam($request, 'verify_token');
        $challenge = $this->hubParam($request, 'challenge');
        $expected = WhatsAppCloudSettings::webhookVerifyToken();

        Log::info('WhatsApp webhook verify', [
            'mode' => $mode,
            'configured' => $expected !== '',
            'token_ok' => $expected !== '' && $token !== '' && hash_equals($expected, $token),
            'has_challenge' => $challenge !== '',
        ]);

        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, $token)) {
            return response($challenge, 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        return response('Forbidden', 403, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function handle(Request $request): Response
    {
        $inbox = app(WhatsAppInboxService::class);
        $payload = $this->decodeWebhookPayload($request);

        Log::info('WhatsApp webhook received', [
            'object' => $payload['object'] ?? null,
            'has_entry' => isset($payload['entry']),
        ]);

        $now = now()->toIso8601String();
        Cache::put('whatsapp:webhook:last_received_at', $now, now()->addDays(90));
        WhatsAppCloudSettings::recordWebhookHit('webhook');

        if (($payload['object'] ?? '') !== 'whatsapp_business_account') {
            return response()->json(['status' => 'ignored']);
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $this->processStatuses($value['statuses'] ?? []);
                $this->processInboundMessages(
                    $inbox,
                    $value['messages'] ?? [],
                    $value['metadata'] ?? [],
                    $value['contacts'] ?? []
                );
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeWebhookPayload(Request $request): array
    {
        $payload = $request->all();
        if (is_array($payload) && $payload !== []) {
            return $payload;
        }

        $raw = $request->getContent();
        if (! is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function hubParam(Request $request, string $suffix): string
    {
        $underscored = 'hub_' . $suffix;
        $dotted = 'hub.' . $suffix;

        $value = $request->query($underscored, $request->query($dotted));

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * @param  array<int, array<string, mixed>>  $statuses
     */
    private function processStatuses(array $statuses): void
    {
        foreach ($statuses as $status) {
            $waId = (string) ($status['id'] ?? '');
            if ($waId === '') {
                continue;
            }

            app(WhatsAppInboxService::class)->applyDeliveryStatus($waId, $status);

            $message = WhatsAppMessage::query()
                ->where('whatsapp_message_id', $waId)
                ->latest()
                ->first();

            if (! $message) {
                continue;
            }

            $state = (string) ($status['status'] ?? '');
            $updates = ['response_data' => array_merge($message->response_data ?? [], ['webhook_status' => $status])];

            if ($state === 'delivered') {
                $updates['status'] = 'delivered';
                $updates['delivered_at'] = now();
            } elseif ($state === 'read') {
                $updates['status'] = 'read';
                $updates['read_at'] = now();
            } elseif ($state === 'failed') {
                $errorDetail = $status['errors'][0] ?? [];
                $errorMessage = is_array($errorDetail)
                    ? app(WhatsAppCloudService::class)->humanizeSendError(
                        $errorDetail,
                        (string) ($errorDetail['title'] ?? $errorDetail['message'] ?? 'فشل التسليم')
                    )
                    : 'فشل التسليم';

                $updates['status'] = 'failed';
                $updates['error_message'] = $errorMessage;
            }

            $message->update($updates);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<string, mixed>  $metadata
     * @param  array<int, array<string, mixed>>  $contacts
     */
    private function processInboundMessages(
        WhatsAppInboxService $inbox,
        array $messages,
        array $metadata,
        array $contacts = []
    ): void {
        $contactNames = [];
        foreach ($contacts as $contact) {
            $waId = (string) ($contact['wa_id'] ?? '');
            $name = (string) ($contact['profile']['name'] ?? '');
            if ($waId !== '' && $name !== '') {
                $contactNames[$waId] = $name;
            }
        }

        foreach ($messages as $msg) {
            $from = (string) ($msg['from'] ?? '');
            if ($from !== '' && isset($contactNames[$from])) {
                $msg['profile'] = ['name' => $contactNames[$from]];
            }

            $stored = $inbox->recordInbound($msg, $metadata);

            Log::info('WhatsApp inbound message', [
                'from' => $msg['from'] ?? null,
                'type' => $msg['type'] ?? null,
                'stored' => (bool) $stored,
                'phone_number_id' => $metadata['phone_number_id'] ?? null,
            ]);
        }
    }
}
