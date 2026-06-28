<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppBusinessConnection;
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
            'embedded_signup_config_id' => 'nullable|string|max:100',
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
            'embedded_signup_config_id' => $validated['embedded_signup_config_id'] ?? '',
            'api_url' => rtrim($validated['api_url'], '/'),
            'webhook_verify_token' => $validated['webhook_verify_token'] ?? '',
            'access_token' => $validated['access_token'] ?? '',
            'phone_number_id' => $validated['phone_number_id'] ?? '',
            'business_account_id' => $validated['business_account_id'] ?? '',
        ]);

        return back()->with('success', 'تم حفظ إعدادات WhatsApp — تُخزَّن في المنصة وليس في ملف .env.');
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
        ]);

        if (! empty($validated['access_token']) || ! empty($validated['phone_number_id'])
            || ! empty($validated['app_id']) || ! empty($validated['app_secret']) || ! empty($validated['api_url'])) {
            WhatsAppCloudSettings::save(array_filter([
                'access_token' => $validated['access_token'] ?? null,
                'phone_number_id' => $validated['phone_number_id'] ?? null,
                'app_id' => $validated['app_id'] ?? null,
                'app_secret' => $validated['app_secret'] ?? null,
                'api_url' => isset($validated['api_url']) ? rtrim($validated['api_url'], '/') : null,
            ], fn ($v) => $v !== null && $v !== ''));
        }

        $result = $this->cloud->testConnection(
            $validated['test_phone'] ?? null,
            $validated['test_message'] ?? null
        );

        return response()->json($result);
    }

    public function completeEmbeddedSignup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:2000',
            'phone_number_id' => 'required|string|max:100',
            'waba_id' => 'required|string|max:100',
            'business_id' => 'nullable|string|max:100',
        ]);

        $result = $this->cloud->completeEmbeddedSignup($validated, (int) auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json($result, 422);
        }

        /** @var WhatsAppBusinessConnection $connection */
        $connection = $result['connection'];

        WhatsAppCloudSettings::save([
            'enabled' => true,
            'phone_number_id' => $connection->phone_number_id,
            'business_account_id' => (string) ($connection->waba_id ?? ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم ربط WhatsApp Business بنجاح.',
            'connection' => [
                'display_phone_number' => $connection->display_phone_number,
                'verified_display_name' => $connection->verified_display_name,
                'phone_number_id' => $connection->phone_number_id,
                'waba_id' => $connection->waba_id,
            ],
        ]);
    }

    public function disconnect(): RedirectResponse
    {
        $this->cloud->disconnect();

        return back()->with('success', 'تم فصل حساب WhatsApp Business من المنصة.');
    }

    public function statusJson(): JsonResponse
    {
        return response()->json($this->cloud->connectionMeta());
    }
}
