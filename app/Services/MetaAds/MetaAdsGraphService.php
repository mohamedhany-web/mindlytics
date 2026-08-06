<?php

namespace App\Services\MetaAds;

use App\Support\MarketingWebAnalyticsSettings;
use App\Support\MetaAdsSettings;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Meta Marketing API client (campaigns / ad sets / budget / status).
 */
class MetaAdsGraphService
{
    public function graphUrl(): string
    {
        return MetaAdsSettings::apiUrl();
    }

    /**
     * @return array{success: bool, label: string, error?: string, account?: array<string, mixed>}
     */
    public function connectionMeta(): array
    {
        if (! MetaAdsSettings::isReady()) {
            return [
                'success' => false,
                'label' => 'غير مهيأ',
                'error' => 'فعّل Meta Ads وأدخل Ad Account ID + Access Token في الإعدادات.',
            ];
        }

        $result = $this->get(MetaAdsSettings::adAccountId(), [
            'fields' => 'id,name,account_status,currency,timezone_name,business_name',
        ]);

        if (! ($result['success'] ?? false)) {
            return [
                'success' => false,
                'label' => 'فشل الاتصال',
                'error' => $result['error'] ?? 'تعذر الوصول لحساب الإعلانات',
            ];
        }

        $account = $result['data'] ?? [];

        return [
            'success' => true,
            'label' => 'متصل — '.((string) ($account['name'] ?? MetaAdsSettings::adAccountId())),
            'account' => $account,
        ];
    }

    /**
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listCampaigns(int $limit = 50): array
    {
        $account = MetaAdsSettings::adAccountId();
        if ($account === '') {
            return ['success' => false, 'error' => 'Ad Account غير مضبوط'];
        }

        $result = $this->get($account.'/campaigns', [
            'fields' => 'id,name,status,effective_status,objective,daily_budget,lifetime_budget,created_time,updated_time,start_time,stop_time',
            'limit' => max(1, min(100, $limit)),
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        /** @var list<array<string, mixed>> $data */
        $data = $result['data']['data'] ?? [];

        return ['success' => true, 'data' => $data];
    }

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function getCampaign(string $campaignId): array
    {
        $result = $this->get($campaignId, [
            'fields' => 'id,name,status,effective_status,objective,daily_budget,lifetime_budget,created_time,updated_time',
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        $campaign = $result['data'] ?? [];
        $adsets = $this->listAdSets($campaignId);
        $campaign['adsets'] = ($adsets['success'] ?? false) ? ($adsets['data'] ?? []) : [];

        return ['success' => true, 'data' => $campaign];
    }

    /**
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listAdSets(string $campaignId): array
    {
        $result = $this->get($campaignId.'/adsets', [
            'fields' => 'id,name,status,effective_status,daily_budget,lifetime_budget,targeting,optimization_goal,billing_event,start_time,end_time',
            'limit' => 25,
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        /** @var list<array<string, mixed>> $data */
        $data = $result['data']['data'] ?? [];

        return ['success' => true, 'data' => $data];
    }

    /**
     * Create a campaign + one ad set with simplified targeting.
     *
     * @param  array{
     *   name: string,
     *   objective?: string,
     *   status?: string,
     *   daily_budget: float|int|string,
     *   age_min?: int,
     *   age_max?: int,
     *   genders?: string,
     *   countries?: string,
     *   optimization_goal?: string
     * }  $input
     * @return array{success: bool, campaign_id?: string, adset_id?: string, error?: string}
     */
    public function createCampaignWithAdSet(array $input): array
    {
        $account = MetaAdsSettings::adAccountId();
        if ($account === '') {
            return ['success' => false, 'error' => 'Ad Account غير مضبوط'];
        }

        $name = trim((string) ($input['name'] ?? ''));
        if ($name === '') {
            return ['success' => false, 'error' => 'اسم الحملة مطلوب'];
        }

        $objective = (string) ($input['objective'] ?? 'OUTCOME_TRAFFIC');
        $status = strtoupper((string) ($input['status'] ?? 'PAUSED'));
        if (! in_array($status, ['ACTIVE', 'PAUSED'], true)) {
            $status = 'PAUSED';
        }

        $budgetMinor = $this->toMinorUnits((float) ($input['daily_budget'] ?? 0));
        if ($budgetMinor < 100) {
            return ['success' => false, 'error' => 'الميزانية اليومية يجب أن تكون على الأقل 1.00'];
        }

        $campaign = $this->post($account.'/campaigns', [
            'name' => $name,
            'objective' => $objective,
            'status' => $status,
            'special_ad_categories' => json_encode([]),
        ]);

        if (! ($campaign['success'] ?? false)) {
            return ['success' => false, 'error' => $campaign['error'] ?? 'فشل إنشاء الحملة'];
        }

        $campaignId = (string) ($campaign['data']['id'] ?? '');
        if ($campaignId === '') {
            return ['success' => false, 'error' => 'Meta لم تُرجع معرف الحملة'];
        }

        $targeting = $this->buildTargeting($input);
        $optimization = (string) ($input['optimization_goal'] ?? $this->defaultOptimizationGoal($objective));

        $adsetPayload = [
            'name' => $name.' — Ad Set',
            'campaign_id' => $campaignId,
            'daily_budget' => $budgetMinor,
            'billing_event' => 'IMPRESSIONS',
            'optimization_goal' => $optimization,
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'targeting' => json_encode($targeting, JSON_UNESCAPED_UNICODE),
            'status' => $status,
        ];

        $pixelId = MarketingWebAnalyticsSettings::metaPixelId();
        if ($pixelId !== '' && in_array($optimization, ['OFFSITE_CONVERSIONS', 'VALUE'], true)) {
            $adsetPayload['promoted_object'] = json_encode([
                'pixel_id' => $pixelId,
                'custom_event_type' => 'PURCHASE',
            ], JSON_UNESCAPED_UNICODE);
        } elseif (MetaAdsSettings::pageId() !== '' && in_array($objective, ['OUTCOME_TRAFFIC', 'OUTCOME_ENGAGEMENT', 'OUTCOME_AWARENESS'], true)) {
            // Page optional for some objectives; leave blank if not set
        }

        $adset = $this->post($account.'/adsets', $adsetPayload);
        if (! ($adset['success'] ?? false)) {
            // Campaign created but ad set failed — still return campaign id with error note
            return [
                'success' => false,
                'campaign_id' => $campaignId,
                'error' => 'تم إنشاء الحملة لكن فشل Ad Set: '.($adset['error'] ?? 'خطأ غير معروف'),
            ];
        }

        return [
            'success' => true,
            'campaign_id' => $campaignId,
            'adset_id' => (string) ($adset['data']['id'] ?? ''),
        ];
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function setCampaignStatus(string $campaignId, string $status): array
    {
        $status = strtoupper($status);
        if (! in_array($status, ['ACTIVE', 'PAUSED'], true)) {
            return ['success' => false, 'error' => 'حالة غير صالحة'];
        }

        return $this->post($campaignId, ['status' => $status]);
    }

    /**
     * Update daily budget on the first ad set of a campaign (minor units).
     *
     * @return array{success: bool, adset_id?: string, error?: string}
     */
    public function updateCampaignDailyBudget(string $campaignId, float $dailyBudgetMajor): array
    {
        $minor = $this->toMinorUnits($dailyBudgetMajor);
        if ($minor < 100) {
            return ['success' => false, 'error' => 'الميزانية اليومية يجب أن تكون على الأقل 1.00'];
        }

        $adsets = $this->listAdSets($campaignId);
        if (! ($adsets['success'] ?? false)) {
            return ['success' => false, 'error' => $adsets['error'] ?? 'تعذر جلب مجموعات الإعلانات'];
        }

        $first = $adsets['data'][0] ?? null;
        if (! is_array($first) || empty($first['id'])) {
            return ['success' => false, 'error' => 'لا توجد مجموعة إعلانات (Ad Set) لتحديث الميزانية'];
        }

        $adsetId = (string) $first['id'];
        $result = $this->post($adsetId, ['daily_budget' => $minor]);
        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل تحديث الميزانية'];
        }

        return ['success' => true, 'adset_id' => $adsetId];
    }

    /**
     * Update targeting on the first ad set.
     *
     * @param  array{age_min?: int, age_max?: int, genders?: string, countries?: string}  $input
     * @return array{success: bool, adset_id?: string, error?: string}
     */
    public function updateCampaignAudience(string $campaignId, array $input): array
    {
        $adsets = $this->listAdSets($campaignId);
        if (! ($adsets['success'] ?? false)) {
            return ['success' => false, 'error' => $adsets['error'] ?? 'تعذر جلب مجموعات الإعلانات'];
        }

        $first = $adsets['data'][0] ?? null;
        if (! is_array($first) || empty($first['id'])) {
            return ['success' => false, 'error' => 'لا توجد مجموعة إعلانات لتحديث الجمهور'];
        }

        $adsetId = (string) $first['id'];
        $targeting = $this->buildTargeting($input);
        $result = $this->post($adsetId, [
            'targeting' => json_encode($targeting, JSON_UNESCAPED_UNICODE),
        ]);

        if (! ($result['success'] ?? false)) {
            return ['success' => false, 'error' => $result['error'] ?? 'فشل تحديث الجمهور'];
        }

        return ['success' => true, 'adset_id' => $adsetId];
    }

    public function fromMinorUnits(int|string|null $minor): float
    {
        return round(((int) $minor) / 100, 2);
    }

    public function toMinorUnits(float $major): int
    {
        return (int) round(max(0, $major) * 100);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function buildTargeting(array $input): array
    {
        $ageMin = (int) ($input['age_min'] ?? 18);
        $ageMax = (int) ($input['age_max'] ?? 65);
        $ageMin = max(13, min(65, $ageMin));
        $ageMax = max($ageMin, min(65, $ageMax));

        $countriesRaw = (string) ($input['countries'] ?? MetaAdsSettings::defaultCountry());
        $countries = array_values(array_filter(array_map(
            static fn ($c) => strtoupper(trim($c)),
            preg_split('/[,\s]+/', $countriesRaw) ?: []
        )));
        if ($countries === []) {
            $countries = [MetaAdsSettings::defaultCountry()];
        }

        $targeting = [
            'age_min' => $ageMin,
            'age_max' => $ageMax,
            'geo_locations' => [
                'countries' => $countries,
            ],
        ];

        $genders = strtolower(trim((string) ($input['genders'] ?? 'all')));
        if ($genders === 'male') {
            $targeting['genders'] = [1];
        } elseif ($genders === 'female') {
            $targeting['genders'] = [2];
        }

        return $targeting;
    }

    public function defaultOptimizationGoal(string $objective): string
    {
        return match ($objective) {
            'OUTCOME_LEADS' => 'LEAD_GENERATION',
            'OUTCOME_SALES' => 'OFFSITE_CONVERSIONS',
            'OUTCOME_ENGAGEMENT' => 'POST_ENGAGEMENT',
            'OUTCOME_AWARENESS' => 'REACH',
            default => 'LINK_CLICKS',
        };
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    protected function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, $query);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    protected function post(string $path, array $payload = []): array
    {
        return $this->request('post', $path, $payload);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    protected function request(string $method, string $path, array $params = []): array
    {
        $token = MetaAdsSettings::accessToken();
        if ($token === '') {
            return ['success' => false, 'error' => 'Access Token مفقود'];
        }

        $url = $this->graphUrl().'/'.ltrim($path, '/');
        $params['access_token'] = $token;

        try {
            /** @var Response $response */
            $response = $method === 'get'
                ? Http::timeout(45)->get($url, $params)
                : Http::asForm()->timeout(45)->post($url, $params);

            $json = $response->json();
            if (! is_array($json)) {
                $json = [];
            }

            if (! $response->successful() || isset($json['error'])) {
                $error = $this->graphErrorMessage($json, 'طلب Meta فشل');
                Log::warning('Meta Ads API error', [
                    'method' => $method,
                    'path' => $path,
                    'status' => $response->status(),
                    'error' => $error,
                ]);

                return ['success' => false, 'error' => $error];
            }

            return ['success' => true, 'data' => $json];
        } catch (\Throwable $e) {
            Log::error('Meta Ads API exception', ['path' => $path, 'message' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $json
     */
    protected function graphErrorMessage(array $json, string $fallback): string
    {
        $err = $json['error'] ?? null;
        if (is_array($err)) {
            $msg = (string) ($err['message'] ?? '');
            $code = $err['code'] ?? null;
            $sub = $err['error_subcode'] ?? null;
            if ($msg !== '') {
                $extra = [];
                if ($code !== null) {
                    $extra[] = 'code='.$code;
                }
                if ($sub !== null) {
                    $extra[] = 'sub='.$sub;
                }

                return $extra !== [] ? $msg.' ('.implode(', ', $extra).')' : $msg;
            }
        }

        return $fallback;
    }
}
