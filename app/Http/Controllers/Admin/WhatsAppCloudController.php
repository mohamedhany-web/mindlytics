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
            'access_token' => 'nullable|string|max:2000',
            'phone_number_id' => 'nullable|string|max:100',
            'business_account_id' => 'nullable|string|max:100',
            'enable_service' => 'nullable|boolean',
        ], [
            'app_id.required' => 'Meta App ID مطلوب',
            'app_secret.required' => 'Meta App Secret مطلوب',
            'api_url.required' => 'Graph API URL مطلوب',
        ]);

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

        $this->syncPhoneMetadataFromApi();

        return back()->with('success', 'تم حفظ إعدادات WhatsApp بنجاح.');
    }

    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'test_phone' => 'nullable|string|max:30',
            'test_message' => 'nullable|string|max:500',
            'access_token' => 'nullable|string|max:2000',
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
        return response()->json($this->cloud->connectionMeta());
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
