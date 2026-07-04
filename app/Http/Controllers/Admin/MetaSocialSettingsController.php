<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Support\MetaSocialSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MetaSocialSettingsController extends Controller
{
    public function __construct(
        private MetaSocialGraphService $graph,
    ) {}

    public function edit()
    {
        $config = MetaSocialSettings::formValues();
        $connectionMeta = $this->graph->connectionMeta();
        $webhookStatus = MetaSocialSettings::webhookStatus();

        return view('admin.meta-social.settings', compact('config', 'connectionMeta', 'webhookStatus'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'app_id' => 'required|string|max:64',
            'app_secret' => 'nullable|string|max:255',
            'api_url' => 'required|url|max:255',
            'webhook_verify_token' => 'required|string|max:128',
            'oauth_scopes' => 'nullable|string|max:2000',
        ]);

        MetaSocialSettings::save([
            'enabled' => $request->boolean('enabled'),
            'app_id' => $validated['app_id'],
            'app_secret' => $validated['app_secret'] ?? '',
            'api_url' => rtrim($validated['api_url'], '/'),
            'webhook_verify_token' => $validated['webhook_verify_token'],
            'oauth_scopes' => $validated['oauth_scopes'] ?? implode(',', MetaSocialSettings::defaultOAuthScopes()),
        ]);

        return back()->with('success', 'تم حفظ إعدادات Meta Social');
    }

    public function syncWebhook(): RedirectResponse
    {
        $result = $this->graph->syncAppWebhookSubscription();

        return back()->with(
            ($result['success'] ?? false) ? 'success' : 'error',
            ($result['success'] ?? false) ? 'تم مزامنة Webhook مع Meta' : ($result['error'] ?? 'فشل')
        );
    }

    public function testConnection(): JsonResponse
    {
        return response()->json($this->graph->connectionMeta());
    }
}
