<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppCloudService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsAppCloudController extends Controller
{
    public function __construct(
        private WhatsAppCloudService $cloud,
    ) {}

    public function saveSettings(Request $request): RedirectResponse
    {
        $hasSecret = WhatsAppCloudSettings::hasAppSecret();

        $validated = $request->validate([
            'app_id' => 'required|string|max:100',
            'app_secret' => [$hasSecret ? 'nullable' : 'required', 'string', 'max:500'],
            'api_url' => 'required|url|max:500',
            'webhook_verify_token' => 'nullable|string|max:200',
            'access_token' => 'nullable|string|max:4096',
            'phone_number_id' => 'nullable|string|max:100',
            'business_account_id' => 'nullable|string|max:100',
            'enable_service' => 'nullable|boolean',
        ], [
            'app_id.required' => 'Meta App ID مطلوب',
            'app_secret.required' => 'Meta App Secret مطلوب',
            'api_url.required' => 'Graph API URL مطلوب',
        ]);

        $newToken = trim((string) ($validated['access_token'] ?? ''));

        WhatsAppCloudSettings::save([
            'enabled' => $request->boolean('enable_service'),
            'app_id' => $validated['app_id'],
            'app_secret' => $validated['app_secret'] ?? '',
            'api_url' => rtrim($validated['api_url'], '/'),
            'webhook_verify_token' => $validated['webhook_verify_token'] ?? '',
            'access_token' => $validated['access_token'] ?? '',
            'phone_number_id' => $validated['phone_number_id'] ?? '',
            'business_account_id' => $validated['business_account_id'] ?? '',
        ]);

        if ($newToken !== '') {
            $this->cloud->disconnect();
        }

        $this->syncPhoneMetadataFromApi();

        $flash = 'تم حفظ إعدادات WhatsApp بنجاح.';
        if (WhatsAppCloudSettings::isSendConfigured() && WhatsAppCloudSettings::webhookVerifyToken() !== '') {
            $sub = $this->cloud->ensureWebhookSubscription();
            if ($sub['success'] ?? false) {
                $flash .= ' تم طلب اشتراك Webhook من Meta.';
            } elseif (! empty($sub['error'])) {
                return back()
                    ->with('success', $flash)
                    ->with('warning', 'تعذّر اشتراك Webhook تلقائياً: ' . $sub['error'] . ' — أكمل الربط يدوياً من Meta Developers → WhatsApp → Configuration.');
            }
        }

        return back()->with('success', $flash);
    }

    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'test_phone' => 'nullable|string|max:30',
            'test_message' => 'nullable|string|max:500',
            'access_token' => 'nullable|string|max:4096',
            'phone_number_id' => 'nullable|string|max:100',
            'app_id' => 'nullable|string|max:100',
            'app_secret' => 'nullable|string|max:500',
            'api_url' => 'nullable|url|max:500',
            'enable_service' => 'nullable|boolean',
        ]);

        $pending = array_filter([
            'enabled' => $request->has('enable_service') ? $request->boolean('enable_service') : null,
            'access_token' => $validated['access_token'] ?? null,
            'phone_number_id' => $validated['phone_number_id'] ?? null,
            'app_id' => $validated['app_id'] ?? null,
            'app_secret' => $validated['app_secret'] ?? null,
            'api_url' => isset($validated['api_url']) ? rtrim($validated['api_url'], '/') : null,
        ], fn ($v) => $v !== null && $v !== '');

        if ($pending !== []) {
            WhatsAppCloudSettings::save($pending);
        }

        $result = $this->cloud->testConnection(
            $validated['test_phone'] ?? null,
            $validated['test_message'] ?? null
        );

        if ($result['success'] ?? false) {
            $this->syncPhoneMetadataFromApi();
        }

        return response()->json($result);
    }

    public function disconnect(): RedirectResponse
    {
        $this->cloud->disconnect();

        WhatsAppCloudSettings::save([
            'access_token' => '',
            'phone_number_id' => '',
            'business_account_id' => '',
            'display_phone_number' => '',
            'verified_display_name' => '',
            'enabled' => false,
        ]);

        return back()->with('success', 'تم مسح بيانات الربط وتعطيل الإرسال.');
    }

    public function statusJson(): JsonResponse
    {
        $meta = $this->cloud->connectionMeta();
        $meta['webhook'] = $this->cloud->webhookDiagnostics();

        return response()->json($meta);
    }

    public function resubscribeWebhook(): JsonResponse
    {
        $result = $this->cloud->ensureWebhookSubscription();
        $diagnostics = $this->cloud->webhookDiagnostics();

        return response()->json([
            'success' => (bool) ($result['success'] ?? false),
            'error' => $result['error'] ?? null,
            'webhook' => $diagnostics,
        ]);
    }

    private function syncPhoneMetadataFromApi(): void
    {
        if (! WhatsAppCloudSettings::isSendConfigured()) {
            return;
        }

        $verify = $this->cloud->verifyApiAccess();
        if (! ($verify['success'] ?? false)) {
            return;
        }

        WhatsAppCloudSettings::save([
            'display_phone_number' => $verify['data']['display_phone_number'] ?? '',
            'verified_display_name' => $verify['data']['verified_name'] ?? '',
        ]);
    }
}
