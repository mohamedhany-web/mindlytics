<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppMessage;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppInboxService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private WhatsAppInboxService $inbox,
    ) {}

    public function verify(Request $request)
    {
        $mode = (string) $request->query('hub_mode', '');
        $token = (string) $request->query('hub_verify_token', '');
        $challenge = (string) $request->query('hub_challenge', '');

        $expected = WhatsAppCloudSettings::webhookVerifyToken();

        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, $token)) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        abort(403, 'Webhook verification failed');
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook received', [
            'object' => $payload['object'] ?? null,
        ]);

        if (($payload['object'] ?? '') !== 'whatsapp_business_account') {
            return response()->json(['status' => 'ignored']);
        }

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $this->processStatuses($value['statuses'] ?? []);
                $this->processInboundMessages($value['messages'] ?? [], $value['metadata'] ?? [], $value['contacts'] ?? []);
            }
        }

        return response()->json(['status' => 'ok']);
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

            $this->inbox->applyDeliveryStatus($waId, $status);

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
    private function processInboundMessages(array $messages, array $metadata, array $contacts = []): void
    {
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

            $stored = $this->inbox->recordInbound($msg, $metadata);

            Log::info('WhatsApp inbound message', [
                'from' => $msg['from'] ?? null,
                'type' => $msg['type'] ?? null,
                'stored' => (bool) $stored,
                'phone_number_id' => $metadata['phone_number_id'] ?? null,
            ]);
        }
    }
}
