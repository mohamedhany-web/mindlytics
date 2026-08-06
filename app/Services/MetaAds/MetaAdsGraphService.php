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
     * @return array{success: bool, label: string, error?: string, account?: array<string, mixed>, token_source?: string}
     */
    public function connectionMeta(): array
    {
        $tokenSource = MetaAdsSettings::tokenSource();
        if (! MetaAdsSettings::hasAccessToken()) {
            return [
                'success' => false,
                'label' => 'غير مربوط',
                'error' => 'اربط حساب Meta من السوشيال ميديا أولاً (أو أعد تسجيل الدخول بصلاحيات الإعلانات).',
                'token_source' => $tokenSource,
            ];
        }

        if (! MetaAdsSettings::isEnabled()) {
            return [
                'success' => false,
                'label' => 'Meta Ads معطّل',
                'error' => 'فعّل Meta Ads من الإعدادات.',
                'token_source' => $tokenSource,
            ];
        }

        $accountId = MetaAdsSettings::adAccountId();
        if ($accountId === '') {
            return [
                'success' => false,
                'label' => 'اختر حساب إعلانات',
                'error' => 'التوكن جاهز من السوشيال — اختر Ad Account من القائمة أدناه.',
                'token_source' => $tokenSource,
            ];
        }

        $result = $this->get($accountId, [
            'fields' => 'id,name,account_status,currency,timezone_name,business_name',
        ]);

        if (! ($result['success'] ?? false)) {
            return [
                'success' => false,
                'label' => 'فشل الاتصال بحساب الإعلانات',
                'error' => ($result['error'] ?? 'تعذر الوصول').' — إن ظهر خطأ صلاحيات، أعد ربط Meta Social بعد إضافة ads_management و ads_read.',
                'token_source' => $tokenSource,
            ];
        }

        $account = $result['data'] ?? [];

        return [
            'success' => true,
            'label' => 'متصل — '.((string) ($account['name'] ?? $accountId)),
            'account' => $account,
            'token_source' => $tokenSource,
        ];
    }

    /**
     * List ad accounts available to the connected Meta user.
     * Tries personal adaccounts + business owned/client accounts.
     *
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string, permissions?: list<string>}
     */
    public function listAdAccounts(int $limit = 50): array
    {
        if (! MetaAdsSettings::hasAccessToken()) {
            return ['success' => false, 'error' => 'لا يوجد توكن — اربط Meta Social أولاً'];
        }

        $limit = max(1, min(100, $limit));
        $byId = [];
        $errors = [];

        $personal = $this->get('me/adaccounts', [
            'fields' => 'id,account_id,name,account_status,currency,timezone_name,business',
            'limit' => $limit,
        ]);
        if ($personal['success'] ?? false) {
            foreach (($personal['data']['data'] ?? []) as $row) {
                if (is_array($row)) {
                    $this->indexAdAccount($byId, $row, 'personal');
                }
            }
        } else {
            $errors[] = 'me/adaccounts: '.($personal['error'] ?? 'فشل');
        }

        // Nested edge as alternate shape
        $meNested = $this->get('me', [
            'fields' => 'id,name,adaccounts.limit('.$limit.'){id,account_id,name,account_status,currency,timezone_name}',
        ]);
        if ($meNested['success'] ?? false) {
            foreach (($meNested['data']['adaccounts']['data'] ?? []) as $row) {
                if (is_array($row)) {
                    $this->indexAdAccount($byId, $row, 'personal');
                }
            }
        }

        $businesses = $this->get('me/businesses', [
            'fields' => 'id,name',
            'limit' => 25,
        ]);
        if ($businesses['success'] ?? false) {
            foreach (($businesses['data']['data'] ?? []) as $biz) {
                $bizId = (string) ($biz['id'] ?? '');
                if ($bizId === '') {
                    continue;
                }
                foreach (['owned_ad_accounts', 'client_ad_accounts'] as $edge) {
                    $res = $this->get($bizId.'/'.$edge, [
                        'fields' => 'id,account_id,name,account_status,currency,timezone_name',
                        'limit' => $limit,
                    ]);
                    if (! ($res['success'] ?? false)) {
                        $errors[] = $edge.'@'.$bizId.': '.($res['error'] ?? 'فشل');
                        continue;
                    }
                    foreach (($res['data']['data'] ?? []) as $row) {
                        if (is_array($row)) {
                            $this->indexAdAccount($byId, $row, $edge, (string) ($biz['name'] ?? $bizId));
                        }
                    }
                }
            }
        } else {
            $errors[] = 'me/businesses: '.($businesses['error'] ?? 'فشل');
        }

        $permissions = $this->grantedPermissionNames();

        if ($byId === []) {
            $hint = 'لم يُعثر على حسابات إعلانات.';
            if ($permissions !== [] && ! in_array('ads_management', $permissions, true) && ! in_array('ads_read', $permissions, true)) {
                $hint .= ' التوكن الحالي بدون صلاحيات ads_read / ads_management — أعد ربط Meta Social للموافقة على صلاحيات الإعلانات.';
            } elseif ($errors !== []) {
                $hint .= ' '.implode(' | ', array_slice($errors, 0, 3));
            }

            return [
                'success' => false,
                'error' => $hint,
                'permissions' => $permissions,
                'data' => [],
            ];
        }

        $data = array_values($byId);
        usort($data, static fn ($a, $b) => strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? '')));

        return [
            'success' => true,
            'data' => $data,
            'permissions' => $permissions,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $byId
     * @param  array<string, mixed>  $row
     */
    protected function indexAdAccount(array &$byId, array $row, string $source, string $businessName = ''): void
    {
        $id = MetaAdsSettings::normalizeAdAccountId((string) ($row['id'] ?? $row['account_id'] ?? ''));
        if ($id === '') {
            return;
        }

        $existing = $byId[$id] ?? [];
        $byId[$id] = array_merge($existing, $row, [
            'id' => $id,
            'account_id' => preg_replace('/^act_/', '', $id),
            'source' => $source,
            'business_name' => $businessName !== '' ? $businessName : ($existing['business_name'] ?? ($row['business']['name'] ?? '')),
        ]);
    }

    /**
     * @return list<string>
     */
    public function grantedPermissionNames(): array
    {
        $res = $this->get('me/permissions');
        if (! ($res['success'] ?? false)) {
            return [];
        }

        $names = [];
        foreach (($res['data']['data'] ?? []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            if (($row['status'] ?? '') === 'granted' && ! empty($row['permission'])) {
                $names[] = (string) $row['permission'];
            }
        }

        return $names;
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
