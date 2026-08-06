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
        $settings = MetaAdsSettings::formValues();
        $connection = $metaAds->connectionMeta();
        $adAccounts = [];
        $adAccountsError = null;

        if (MetaAdsSettings::hasAccessToken()) {
            $listed = $metaAds->listAdAccounts();
            if ($listed['success'] ?? false) {
                $adAccounts = $listed['data'] ?? [];
            } else {
                $adAccountsError = $listed['error'] ?? null;
            }
        }

        return view('admin.marketing.meta-ads.settings', [
            'settings' => $settings,
            'connection' => $connection,
            'adAccounts' => $adAccounts,
            'adAccountsError' => $adAccountsError,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'ad_account_id' => ['nullable', 'string', 'max:64'],
            'default_currency' => ['nullable', 'string', 'max:8'],
            'default_country' => ['nullable', 'string', 'max:8'],
            'page_id' => ['nullable', 'string', 'max:64'],
            'instagram_actor_id' => ['nullable', 'string', 'max:64'],
        ]);

        MetaAdsSettings::save([
            'enabled' => $request->boolean('enabled'),
            'ad_account_id' => $validated['ad_account_id'] ?? '',
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
