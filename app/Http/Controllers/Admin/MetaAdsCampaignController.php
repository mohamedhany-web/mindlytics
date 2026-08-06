<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MetaAds\MetaAdsGraphService;
use App\Support\MetaAdsSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaAdsCampaignController extends Controller
{
    public function index(MetaAdsGraphService $metaAds): View|RedirectResponse
    {
        if (! MetaAdsSettings::hasAccessToken()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اربط Meta من السوشيال ميديا أولاً، ثم اختر Ad Account.');
        }

        if (! MetaAdsSettings::isReady()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اختر حساب إعلانات (Ad Account) من إعدادات Meta Ads.');
        }

        $connection = $metaAds->connectionMeta();
        $list = $metaAds->listCampaigns(50);
        $campaigns = ($list['success'] ?? false) ? ($list['data'] ?? []) : [];
        $error = ($list['success'] ?? false) ? null : ($list['error'] ?? 'تعذر جلب الحملات');

        $active = 0;
        $paused = 0;
        foreach ($campaigns as $c) {
            $status = strtoupper((string) ($c['effective_status'] ?? $c['status'] ?? ''));
            if (in_array($status, ['ACTIVE'], true)) {
                $active++;
            } else {
                $paused++;
            }
        }

        return view('admin.marketing.meta-ads.campaigns.index', [
            'campaigns' => $campaigns,
            'connection' => $connection,
            'error' => $error,
            'stats' => [
                'total' => count($campaigns),
                'active' => $active,
                'paused' => $paused,
            ],
            'metaAds' => $metaAds,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (! MetaAdsSettings::hasAccessToken()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اربط Meta من السوشيال ميديا أولاً.');
        }

        if (! MetaAdsSettings::isReady()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اختر حساب إعلانات من الإعدادات.');
        }

        return view('admin.marketing.meta-ads.campaigns.create', [
            'defaults' => [
                'currency' => MetaAdsSettings::defaultCurrency(),
                'country' => MetaAdsSettings::defaultCountry(),
            ],
        ]);
    }

    public function store(Request $request, MetaAdsGraphService $metaAds): RedirectResponse
    {
        if (! MetaAdsSettings::isReady()) {
            return redirect()->route('admin.meta-ads.settings');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'objective' => ['required', 'string', 'in:OUTCOME_TRAFFIC,OUTCOME_LEADS,OUTCOME_SALES,OUTCOME_ENGAGEMENT,OUTCOME_AWARENESS'],
            'status' => ['required', 'string', 'in:PAUSED,ACTIVE'],
            'daily_budget' => ['required', 'numeric', 'min:1'],
            'age_min' => ['nullable', 'integer', 'min:13', 'max:65'],
            'age_max' => ['nullable', 'integer', 'min:13', 'max:65'],
            'genders' => ['nullable', 'string', 'in:all,male,female'],
            'countries' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $metaAds->createCampaignWithAdSet($validated);

        if (! ($result['success'] ?? false)) {
            $msg = $result['error'] ?? 'فشل إنشاء الحملة';
            if (! empty($result['campaign_id'])) {
                return redirect()
                    ->route('admin.meta-ads.campaigns.show', $result['campaign_id'])
                    ->with('error', $msg);
            }

            return back()->withInput()->with('error', $msg);
        }

        return redirect()
            ->route('admin.meta-ads.campaigns.show', $result['campaign_id'])
            ->with('success', 'تم إنشاء حملة Meta بنجاح.');
    }

    public function show(string $campaign, MetaAdsGraphService $metaAds): View|RedirectResponse
    {
        if (! MetaAdsSettings::isReady()) {
            return redirect()->route('admin.meta-ads.settings');
        }

        $result = $metaAds->getCampaign($campaign);
        if (! ($result['success'] ?? false)) {
            return redirect()
                ->route('admin.meta-ads.campaigns.index')
                ->with('error', $result['error'] ?? 'الحملة غير موجودة');
        }

        return view('admin.marketing.meta-ads.campaigns.show', [
            'campaign' => $result['data'],
            'metaAds' => $metaAds,
            'currency' => MetaAdsSettings::defaultCurrency(),
        ]);
    }

    public function pause(string $campaign, MetaAdsGraphService $metaAds): RedirectResponse
    {
        $result = $metaAds->setCampaignStatus($campaign, 'PAUSED');

        return redirect()
            ->back()
            ->with($result['success'] ?? false ? 'success' : 'error',
                ($result['success'] ?? false) ? 'تم إيقاف الحملة.' : ($result['error'] ?? 'فشل الإيقاف'));
    }

    public function resume(string $campaign, MetaAdsGraphService $metaAds): RedirectResponse
    {
        $result = $metaAds->setCampaignStatus($campaign, 'ACTIVE');

        return redirect()
            ->back()
            ->with($result['success'] ?? false ? 'success' : 'error',
                ($result['success'] ?? false) ? 'تم تشغيل الحملة.' : ($result['error'] ?? 'فشل التشغيل'));
    }

    public function updateBudget(Request $request, string $campaign, MetaAdsGraphService $metaAds): RedirectResponse
    {
        $validated = $request->validate([
            'daily_budget' => ['required', 'numeric', 'min:1'],
        ]);

        $result = $metaAds->updateCampaignDailyBudget($campaign, (float) $validated['daily_budget']);

        return redirect()
            ->route('admin.meta-ads.campaigns.show', $campaign)
            ->with($result['success'] ?? false ? 'success' : 'error',
                ($result['success'] ?? false) ? 'تم تحديث الميزانية اليومية.' : ($result['error'] ?? 'فشل التحديث'));
    }

    public function updateAudience(Request $request, string $campaign, MetaAdsGraphService $metaAds): RedirectResponse
    {
        $validated = $request->validate([
            'age_min' => ['nullable', 'integer', 'min:13', 'max:65'],
            'age_max' => ['nullable', 'integer', 'min:13', 'max:65'],
            'genders' => ['nullable', 'string', 'in:all,male,female'],
            'countries' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $metaAds->updateCampaignAudience($campaign, $validated);

        return redirect()
            ->route('admin.meta-ads.campaigns.show', $campaign)
            ->with($result['success'] ?? false ? 'success' : 'error',
                ($result['success'] ?? false) ? 'تم تحديث الجمهور.' : ($result['error'] ?? 'فشل التحديث'));
    }
}
