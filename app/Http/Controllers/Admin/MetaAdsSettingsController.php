<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MetaAds\MetaAdsGraphService;
use App\Support\MetaAdsSettings;
use App\Support\MetaSocialSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaAdsSettingsController extends Controller
{
    public function edit(MetaAdsGraphService $metaAds): View
    {
        // Ensure next Meta Social OAuth includes ads scopes
        MetaSocialSettings::ensureAdsScopesPersisted();

        $settings = MetaAdsSettings::formValues();
        $connection = $metaAds->connectionMeta();
        $adAccounts = [];
        $adAccountsError = null;
        $permissions = [];
        $needsAdsReauth = false;

        if (MetaAdsSettings::hasAccessToken()) {
            $listed = $metaAds->listAdAccounts(100);
            $adAccounts = $listed['data'] ?? [];
            $permissions = $listed['permissions'] ?? [];
            if (! ($listed['success'] ?? false) || $adAccounts === []) {
                $adAccountsError = $listed['error'] ?? 'لم يتم العثور على حسابات.';
            }
            $needsAdsReauth = ! in_array('ads_read', $permissions, true)
                && ! in_array('ads_management', $permissions, true);
        }

        return view('admin.marketing.meta-ads.settings', [
            'settings' => $settings,
            'connection' => $connection,
            'adAccounts' => $adAccounts,
            'adAccountsError' => $adAccountsError,
            'permissions' => $permissions,
            'needsAdsReauth' => $needsAdsReauth,
            'oauthLoginUrl' => route('admin.meta-social.oauth.redirect'),
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
            'enabled' => $request->boolean('enabled', true),
            'ad_account_id' => $validated['ad_account_id'] ?? '',
            'default_currency' => $validated['default_currency'] ?? 'EGP',
            'default_country' => $validated['default_country'] ?? 'EG',
            'page_id' => $validated['page_id'] ?? '',
            'instagram_actor_id' => $validated['instagram_actor_id'] ?? '',
        ]);

        $account = MetaAdsSettings::adAccountId();
        $msg = $account !== ''
            ? 'تم حفظ الحساب: '.$account.' — يمكنك فتح الحملات الآن.'
            : 'تم حفظ الإعدادات. اختر حساب إعلانات من القائمة.';

        return redirect()
            ->route('admin.meta-ads.settings')
            ->with('success', $msg);
    }

    public function selectAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ad_account_id' => ['required', 'string', 'max:64'],
        ]);

        MetaAdsSettings::save([
            'enabled' => true,
            'ad_account_id' => $validated['ad_account_id'],
        ]);

        return redirect()
            ->route('admin.meta-ads.campaigns.index')
            ->with('success', 'تم اختيار حساب الإعلانات. جاري تحميل الحملات…');
    }

    public function test(MetaAdsGraphService $metaAds): RedirectResponse
    {
        $listed = $metaAds->listAdAccounts(5);
        if (($listed['success'] ?? false) && ! empty($listed['data'])) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('success', 'تم جلب '.count($listed['data']).' حساب إعلانات من Meta.');
        }

        $result = $metaAds->connectionMeta();
        if ($result['success'] ?? false) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('success', $result['label'] ?? 'الاتصال ناجح');
        }

        return redirect()
            ->route('admin.meta-ads.settings')
            ->with('error', $listed['error'] ?? $result['error'] ?? 'فشل اختبار الاتصال');
    }
}
