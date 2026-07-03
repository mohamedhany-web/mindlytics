<?php

namespace App\Services;

use App\Support\WhatsAppBridgeSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppGroupBridgeService
{
    public function __construct(
        private WhatsAppBridgeService $bridge,
    ) {}

    public function isAvailable(): bool
    {
        return WhatsAppBridgeSettings::isBridgeConfigured();
    }

    /**
     * @return array{success: bool, connected?: bool, error?: string, data?: array<string, mixed>}
     */
    public function connectionStatus(): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'connected' => false,
                'error' => 'جسر الواتساب غير مضبوط — اطلب من الإدارة ضبط Bridge URL و Token.',
            ];
        }

        $status = $this->bridge->getStatus();
        $connected = ($status['data']['status'] ?? '') === 'ready';

        return [
            'success' => (bool) ($status['success'] ?? false),
            'connected' => $connected,
            'error' => $connected ? null : ($status['error'] ?? ($status['data']['last_error'] ?? 'الجلسة غير متصلة')),
            'data' => $status['data'] ?? [],
        ];
    }

    /**
     * @param  array<int, string>  $participants
     * @return array{success: bool, error?: string, group?: array<string, mixed>, invite_link?: ?string}
     */
    public function createGroup(
        string $subject,
        array $participants,
        ?string $description = null,
        bool $announceOnly = false,
        bool $restrict = false,
    ): array {
        return $this->request('post', '/api/groups/create', [
            'subject' => $subject,
            'participants' => array_values($participants),
            'description' => $description,
            'announce_only' => $announceOnly,
            'restrict' => $restrict,
        ]);
    }

    /**
     * @return array{success: bool, error?: string, group?: array<string, mixed>, invite_link?: ?string}
     */
    public function getGroup(string $jid): array
    {
        return $this->request('get', '/api/groups/' . rawurlencode($jid));
    }

    /**
     * @return array{success: bool, error?: string, group?: array<string, mixed>}
     */
    public function updateGroup(string $jid, array $payload): array
    {
        return $this->request('patch', '/api/groups/' . rawurlencode($jid), $payload);
    }

    /**
     * @param  array<int, string>  $participants
     * @return array{success: bool, error?: string, group?: array<string, mixed>}
     */
    public function addParticipants(string $jid, array $participants): array
    {
        return $this->request('post', '/api/groups/' . rawurlencode($jid) . '/participants', [
            'participants' => array_values($participants),
        ]);
    }

    /**
     * @param  array<int, string>  $participants
     * @return array{success: bool, error?: string, group?: array<string, mixed>}
     */
    public function removeParticipants(string $jid, array $participants): array
    {
        return $this->request('delete', '/api/groups/' . rawurlencode($jid) . '/participants', [
            'participants' => array_values($participants),
        ]);
    }

    /**
     * @return array{success: bool, error?: string, invite_link?: ?string}
     */
    public function inviteLink(string $jid): array
    {
        return $this->request('get', '/api/groups/' . rawurlencode($jid) . '/invite');
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function leaveGroup(string $jid): array
    {
        return $this->request('post', '/api/groups/' . rawurlencode($jid) . '/leave');
    }

    /**
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->isAvailable()) {
            return ['success' => false, 'error' => 'جسر الواتساب غير مضبوط.'];
        }

        $url = rtrim(WhatsAppBridgeSettings::bridgeUrl(), '/') . $path;

        try {
            $client = Http::withToken(WhatsAppBridgeSettings::bridgeToken())
                ->acceptJson()
                ->timeout(120);

            $response = match (strtolower($method)) {
                'get' => $client->get($url),
                'patch' => $client->patch($url, $payload),
                'delete' => $client->withBody(json_encode($payload), 'application/json')->delete($url),
                default => $client->post($url, $payload),
            };

            $body = $response->json();
            if (! is_array($body)) {
                return ['success' => false, 'error' => 'استجابة غير صالحة من الجسر'];
            }

            if ($response->successful() && ($body['success'] ?? false)) {
                return $body;
            }

            return [
                'success' => false,
                'error' => (string) ($body['error'] ?? 'فشل الطلب: HTTP ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp group bridge error', ['path' => $path, 'error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
