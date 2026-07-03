<?php

namespace App\Services;

use App\Support\WhatsAppCloudSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudGroupService
{
    public function __construct(
        private WhatsAppCloudService $cloud,
    ) {}

    public function isConfigured(): bool
    {
        $creds = $this->cloud->resolveCredentials();

        return $creds['access_token'] !== '' && $creds['phone_number_id'] !== '';
    }

    /**
     * @return array{success: bool, connected?: bool, error?: ?string, label?: string, notes?: array<int, string>}
     */
    public function connectionStatus(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'connected' => false,
                'error' => 'إعدادات Meta Cloud غير مكتملة — أكمل Access Token و Phone Number ID.',
                'label' => 'غير مربوط',
                'notes' => $this->eligibilityNotes(),
            ];
        }

        $ready = $this->cloud->canSendNow();
        $connected = (bool) ($ready['success'] ?? false);

        return [
            'success' => $connected,
            'connected' => $connected,
            'error' => $connected ? null : ($ready['error'] ?? 'Meta Cloud غير جاهز'),
            'label' => $connected ? 'متصل — Meta Cloud API' : 'غير جاهز',
            'notes' => $this->eligibilityNotes(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function eligibilityNotes(): array
    {
        return [
            'مجموعات واتساب تعمل عبر Meta Cloud Groups API (وليس الجسر).',
            'يتطلب حساب Official Business Account (OBA) من Meta.',
            'لا يمكن إضافة أرقام يدوياً — تُرسل دعوة عبر قالب Group Invite معتمد.',
            'الحد الأقصى 8 مشاركين في المجموعة (باستثناء رقم النشاط).',
            'فعّل webhooks: group_lifecycle_update, group_participants_update, group_settings_update, group_status_update.',
        ];
    }

    /**
     * @return array{success: bool, error?: string, group_id?: string, invite_link?: ?string, data?: array<string, mixed>}
     */
    public function createGroup(
        string $subject,
        ?string $description = null,
        string $joinApprovalMode = 'auto_approve',
    ): array {
        $payload = [
            'messaging_product' => 'whatsapp',
            'subject' => $subject,
            'join_approval_mode' => $joinApprovalMode === 'approval_required' ? 'approval_required' : 'auto_approve',
        ];

        if ($description !== null && trim($description) !== '') {
            $payload['description'] = $description;
        }

        $creds = $this->cloud->resolveCredentials();
        $result = $this->request('post', "{$creds['phone_number_id']}/groups", $payload);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $data = is_array($result['data'] ?? null) ? $result['data'] : [];
        $groupId = (string) ($data['id'] ?? $data['group_id'] ?? '');

        if ($groupId === '') {
            return ['success' => false, 'error' => 'لم يُرجع Meta معرّف المجموعة — تحقق من صلاحية OBA.'];
        }

        $invite = $this->getInviteLink($groupId);
        $inviteLink = ($invite['success'] ?? false) ? ($invite['invite_link'] ?? null) : (string) ($data['invite_link'] ?? null);

        return [
            'success' => true,
            'group_id' => $groupId,
            'invite_link' => $inviteLink,
            'data' => $data,
        ];
    }

    /**
     * @return array{success: bool, error?: string, group?: array<string, mixed>, invite_link?: ?string}
     */
    public function getGroup(string $groupId): array
    {
        $fields = 'subject,description,join_approval_mode,participants,total_participant_count,suspended,creation_timestamp';
        $result = $this->request('get', rawurlencode($groupId), [], ['fields' => $fields]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $data = is_array($result['data'] ?? null) ? $result['data'] : [];
        $invite = $this->getInviteLink($groupId);

        return [
            'success' => true,
            'group' => $data,
            'invite_link' => ($invite['success'] ?? false) ? ($invite['invite_link'] ?? null) : null,
        ];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function updateGroup(string $groupId, array $payload): array
    {
        $body = ['messaging_product' => 'whatsapp'];

        if (isset($payload['subject']) && trim((string) $payload['subject']) !== '') {
            $body['subject'] = trim((string) $payload['subject']);
        }
        if (array_key_exists('description', $payload)) {
            $body['description'] = (string) ($payload['description'] ?? '');
        }

        return $this->request('post', rawurlencode($groupId), $body);
    }

    /**
     * @return array{success: bool, error?: string, invite_link?: ?string}
     */
    public function getInviteLink(string $groupId): array
    {
        $result = $this->request('get', rawurlencode($groupId) . '/invite_link');

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $data = is_array($result['data'] ?? null) ? $result['data'] : [];

        return [
            'success' => true,
            'invite_link' => $data['invite_link'] ?? null,
        ];
    }

    /**
     * @return array{success: bool, error?: string, invite_link?: ?string}
     */
    public function resetInviteLink(string $groupId): array
    {
        $result = $this->request('post', rawurlencode($groupId) . '/invite_link', [
            'messaging_product' => 'whatsapp',
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $data = is_array($result['data'] ?? null) ? $result['data'] : [];

        return [
            'success' => true,
            'invite_link' => $data['invite_link'] ?? null,
        ];
    }

    /**
     * @param  array<int, string>  $phones
     * @return array{success: bool, error?: string}
     */
    public function removeParticipants(string $groupId, array $phones): array
    {
        $participants = [];
        foreach ($phones as $phone) {
            $phone = trim((string) $phone);
            if ($phone !== '') {
                $participants[] = ['user' => $phone];
            }
        }

        if ($participants === []) {
            return ['success' => false, 'error' => 'لا توجد أرقام لإزالتها.'];
        }

        return $this->request('delete', rawurlencode($groupId) . '/participants', [
            'messaging_product' => 'whatsapp',
            'participants' => $participants,
        ]);
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function deleteGroup(string $groupId): array
    {
        return $this->request('delete', rawurlencode($groupId));
    }

    /**
     * @param  array<int, array<string, mixed>>  $extraBodyParameters
     * @return array{success: bool, error?: string, whatsapp_id?: ?string}
     */
    public function sendGroupInviteTemplate(
        string $phone,
        string $groupId,
        string $templateName,
        string $languageCode = 'en',
        array $extraBodyParameters = [],
    ): array {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'Meta Cloud غير مضبوط.'];
        }

        $ready = $this->cloud->canSendNow();
        if (! ($ready['success'] ?? false)) {
            return ['success' => false, 'error' => $ready['error'] ?? 'Meta Cloud غير جاهز'];
        }

        $parameters = array_merge(
            [['type' => 'group_id', 'group_id' => $groupId]],
            $extraBodyParameters
        );

        $creds = $this->cloud->resolveCredentials();

        try {
            $response = Http::withToken($creds['access_token'])
                ->timeout(60)
                ->post("{$this->cloud->graphUrl()}/{$creds['phone_number_id']}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => ['code' => $languageCode],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => $parameters,
                            ],
                        ],
                    ],
                ]);

            $body = $response->json() ?? [];
            $metaError = is_array($body['error'] ?? null) ? $body['error'] : null;
            $waMessageId = $body['messages'][0]['id'] ?? null;
            $accepted = $response->successful() && is_string($waMessageId) && $waMessageId !== '';

            if ($accepted) {
                return ['success' => true, 'whatsapp_id' => $waMessageId];
            }

            return [
                'success' => false,
                'error' => $this->cloud->humanizeSendError(
                    $metaError,
                    (string) ($metaError['message'] ?? 'فشل إرسال دعوة المجموعة')
                ),
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp group invite template error', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => $this->cloud->humanizeMetaError($e->getMessage())];
        }
    }

    /**
     * @return array{success: bool, error?: string, data?: array<string, mixed>}
     */
    private function request(string $method, string $path, array $payload = [], array $query = []): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'Meta Cloud غير مضبوط.'];
        }

        $creds = $this->cloud->resolveCredentials();
        $url = $this->cloud->graphUrl() . '/' . ltrim($path, '/');

        try {
            $client = Http::withToken($creds['access_token'])
                ->acceptJson()
                ->timeout(90);

            $response = match (strtolower($method)) {
                'get' => $client->get($url, $query),
                'delete' => $payload === []
                    ? $client->delete($url, $query)
                    : $client->withBody(json_encode($payload), 'application/json')->delete($url),
                default => $client->post($url, $payload),
            };

            $body = $response->json();
            if (! is_array($body)) {
                return ['success' => false, 'error' => 'استجابة غير صالحة من Meta'];
            }

            if ($response->successful()) {
                return ['success' => true, 'data' => $body];
            }

            $error = is_array($body['error'] ?? null) ? $body['error'] : [];

            return [
                'success' => false,
                'error' => $this->cloud->humanizeSendError(
                    $error,
                    (string) ($error['message'] ?? 'فشل الطلب: HTTP ' . $response->status())
                ),
                'data' => $body,
            ];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Cloud group API error', ['path' => $path, 'error' => $e->getMessage()]);

            return ['success' => false, 'error' => $this->cloud->humanizeMetaError($e->getMessage())];
        }
    }
}
