<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MetaAds\MetaAdsGraphService;
use App\Support\MetaAdsSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaAdsSettingsController extends Controller
{
    public function edit(MetaAdsGraphService $metaAds): View
    {
        $connection = null;
        if (MetaAdsSettings::isReady()) {
            $connection = $metaAds->connectionMeta();
        }

        return view('admin.marketing.meta-ads.settings', [
            'settings' => MetaAdsSettings::formValues(),
            'connection' => $connection,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'ad_account_id' => ['nullable', 'string', 'max:64'],
            'access_token' => ['nullable', 'string', 'max:5000'],
            'api_url' => ['nullable', 'string', 'max:255'],
            'default_currency' => ['nullable', 'string', 'max:8'],
            'default_country' => ['nullable', 'string', 'max:8'],
            'page_id' => ['nullable', 'string', 'max:64'],
            'instagram_actor_id' => ['nullable', 'string', 'max:64'],
        ]);

        MetaAdsSettings::save([
            'enabled' => $request->boolean('enabled'),
            'ad_account_id' => $validated['ad_account_id'] ?? '',
            'access_token' => $validated['access_token'] ?? '',
            'api_url' => $validated['api_url'] ?? 'https://graph.facebook.com/v21.0',
            'default_currency' => $validated['default_currency'] ?? 'EGP',
            'default_country' => $validated['default_country'] ?? 'EG',
            'page_id' => $validated['page_id'] ?? '',
            'instagram_actor_id' => $validated['instagram_actor_id'] ?? '',
        ]);

        return redirect()
            ->route('admin.meta-ads.settings')
            ->with('success', 'تم حفظ إعدادات Meta Ads.');
    }

    public function test(MetaAdsGraphService $metaAds): RedirectResponse
    {
        $result = $metaAds->connectionMeta();

        if ($result['success'] ?? false) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('success', $result['label'] ?? 'الاتصال ناجح');
        }

        return redirect()
            ->route('admin.meta-ads.settings')
            ->with('error', $result['error'] ?? 'فشل اختبار الاتصال');
    }
}
