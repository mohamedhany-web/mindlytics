<?php

namespace App\Domains\Messaging\Services;

use App\Domains\Marketing\Support\MarketingWebAnalyticsSettings;
use App\Domains\Messaging\Support\MetaAdsSettings;
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
            'fields' => 'id,name,status,effective_status,daily_budget,lifetime_budget,targeting,optimization_goal,destination_type,promoted_object,billing_event,start_time,end_time',
            'limit' => 50,
        ]);

        if (! ($result['success'] ?? false)) {
            return $result;
        }

        /** @var list<array<string, mixed>> $data */
        $data = $result['data']['data'] ?? [];

        return ['success' => true, 'data' => $data];
    }

    /**
     * Ads under a Meta campaign (for lead pull / referral matching).
     *
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listAdsForCampaign(string $campaignId, int $limit = 100): array
    {
        $campaignId = trim($campaignId);
        if ($campaignId === '') {
            return ['success' => false, 'error' => 'معرّف الحملة مطلوب'];
        }

        // لا تستخدم creative{lead_gen_form_id} كحقل مباشر — غير موجود على AdCreative (#100)
        $withCreative = $this->paginateGet($campaignId.'/ads', [
            'fields' => 'id,name,status,effective_status,adset_id,campaign_id,creative{id,name,object_story_spec{link_data{call_to_action{type,value}},video_data{call_to_action{type,value}},template_data{call_to_action{type,value}}}}',
            'limit' => max(1, min(100, $limit)),
        ], 500);

        if ($withCreative['success'] ?? false) {
            return $withCreative;
        }

        // مسار أبسط إن فشل حقل creative لأي سبب
        return $this->paginateGet($campaignId.'/ads', [
            'fields' => 'id,name,status,effective_status,adset_id,campaign_id',
            'limit' => max(1, min(100, $limit)),
        ], 500);
    }

    /**
     * استخراج lead gen form id من creative إن وُجد داخل object_story_spec / asset_feed_spec.
     *
     * @param  array<string, mixed>  $ad
     */
    public function extractLeadGenFormIdFromAd(array $ad): ?string
    {
        $creative = is_array($ad['creative'] ?? null) ? $ad['creative'] : [];

        $candidates = [
            data_get($creative, 'object_story_spec.link_data.call_to_action.value.lead_gen_form_id'),
            data_get($creative, 'object_story_spec.video_data.call_to_action.value.lead_gen_form_id'),
            data_get($creative, 'object_story_spec.template_data.call_to_action.value.lead_gen_form_id'),
        ];

        $assetFeed = $creative['asset_feed_spec'] ?? null;
        if (is_array($assetFeed)) {
            foreach (($assetFeed['call_to_actions'] ?? []) as $cta) {
                if (! is_array($cta)) {
                    continue;
                }
                $candidates[] = data_get($cta, 'value.lead_gen_form_id');
            }
        }

        foreach ($candidates as $id) {
            $id = trim((string) $id);
            if ($id !== '') {
                return $id;
            }
        }

        return null;
    }

    /**
     * نوع CTA من إعلان ميتا (LEAD / MESSAGE / LEARN_MORE ...).
     *
     * @param  array<string, mixed>  $ad
     */
    public function extractAdCtaType(array $ad): ?string
    {
        $creative = is_array($ad['creative'] ?? null) ? $ad['creative'] : [];
        $types = [
            data_get($creative, 'object_story_spec.link_data.call_to_action.type'),
            data_get($creative, 'object_story_spec.video_data.call_to_action.type'),
            data_get($creative, 'object_story_spec.template_data.call_to_action.type'),
        ];

        foreach ($types as $type) {
            $type = strtoupper(trim((string) $type));
            if ($type !== '') {
                return $type;
            }
        }

        return null;
    }

    /**
     * ملخص نوع الحملة من objective + adsets + CTAs.
     *
     * @param  list<array<string, mixed>>  $ads
     * @return array{objective: ?string, optimization_goals: list<string>, destination_types: list<string>, cta_types: list<string>, looks_like_lead_form: bool, looks_like_messaging: bool}
     */
    public function summarizeCampaignLeadCapability(string $campaignId, array $ads = []): array
    {
        $objective = null;
        $goals = [];
        $destinations = [];
        $campaign = $this->getCampaign($campaignId);
        if ($campaign['success'] ?? false) {
            $objective = (string) (($campaign['data']['objective'] ?? '') ?: '') ?: null;
            foreach (($campaign['data']['adsets'] ?? []) as $adset) {
                if (! is_array($adset)) {
                    continue;
                }
                $goal = strtoupper(trim((string) ($adset['optimization_goal'] ?? '')));
                if ($goal !== '') {
                    $goals[$goal] = true;
                }
                $dest = strtoupper(trim((string) ($adset['destination_type'] ?? '')));
                if ($dest !== '') {
                    $destinations[$dest] = true;
                }
            }
        }

        $ctaTypes = [];
        foreach ($ads as $ad) {
            if (! is_array($ad)) {
                continue;
            }
            $cta = $this->extractAdCtaType($ad);
            if ($cta) {
                $ctaTypes[$cta] = true;
            }
        }

        $goalList = array_keys($goals);
        $destList = array_keys($destinations);
        $ctaList = array_keys($ctaTypes);

        $looksLikeLeadForm = in_array(strtoupper((string) $objective), ['OUTCOME_LEADS', 'LEAD_GENERATION'], true)
            || in_array('LEAD_GENERATION', $goalList, true)
            || in_array('QUALITY_LEAD', $goalList, true)
            || (bool) array_intersect($ctaList, ['SIGN_UP', 'APPLY_NOW', 'GET_OFFER', 'SUBSCRIBE', 'LEAD_FORM', 'GET_QUOTE']);

        $looksLikeMessaging = in_array(strtoupper((string) $objective), ['OUTCOME_ENGAGEMENT', 'MESSAGES', 'OUTCOME_AWARENESS'], true)
            || (bool) array_intersect($goalList, ['CONVERSATIONS', 'REPLIES', 'LINK_CLICKS', 'REACH', 'IMPRESSIONS', 'POST_ENGAGEMENT'])
            || (bool) array_intersect($destList, ['MESSENGER', 'WHATSAPP', 'INSTAGRAM_DIRECT', 'PHONE_CALL'])
            || (bool) array_intersect($ctaList, ['MESSAGE_PAGE', 'WHATSAPP_MESSAGE', 'CALL_NOW', 'LEARN_MORE', 'SHOP_NOW', 'WATCH_MORE', 'CONTACT_US']);

        // لو فيه فورم صريح نلغي اعتبارها messaging فقط
        foreach ($ads as $ad) {
            if (is_array($ad) && $this->extractLeadGenFormIdFromAd($ad)) {
                $looksLikeLeadForm = true;
                break;
            }
        }

        return [
            'objective' => $objective,
            'optimization_goals' => $goalList,
            'destination_types' => $destList,
            'cta_types' => $ctaList,
            'looks_like_lead_form' => $looksLikeLeadForm,
            'looks_like_messaging' => $looksLikeMessaging && ! $looksLikeLeadForm,
        ];
    }

    /**
     * ليدات الحساب المفلترة بحملة معيّنة (مسار احتياطي).
     *
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listLeadsForCampaign(string $campaignId, int $limit = 100): array
    {
        $account = MetaAdsSettings::adAccountId();
        $campaignId = trim($campaignId);
        if ($account === '' || $campaignId === '') {
            return ['success' => false, 'error' => 'Ad Account أو الحملة غير مضبوطين'];
        }

        $filtering = json_encode([
            [
                'field' => 'campaign.id',
                'operator' => 'EQUAL',
                'value' => $campaignId,
            ],
        ], JSON_UNESCAPED_SLASHES);

        return $this->paginateGet($account.'/leads', [
            'fields' => 'id,created_time,ad_id,ad_name,adset_id,adset_name,campaign_id,campaign_name,form_id,is_organic,field_data',
            'filtering' => $filtering ?: '[]',
            'limit' => max(1, min(100, $limit)),
        ], 2000);
    }

    /**
     * Lead Ads submissions for a single ad.
     *
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listLeadsForAd(string $adId, int $limit = 100): array
    {
        $adId = trim($adId);
        if ($adId === '') {
            return ['success' => false, 'error' => 'معرّف الإعلان مطلوب'];
        }

        return $this->paginateGet($adId.'/leads', [
            'fields' => 'id,created_time,ad_id,adset_id,campaign_id,form_id,field_data',
            'limit' => max(1, min(100, $limit)),
        ], 2000);
    }

    /**
     * Lead Ads submissions for a leadgen form.
     *
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    public function listLeadsForForm(string $formId, int $limit = 100, ?string $accessToken = null): array
    {
        $formId = trim($formId);
        if ($formId === '') {
            return ['success' => false, 'error' => 'معرّف الفورم مطلوب'];
        }

        return $this->paginateGet($formId.'/leads', [
            'fields' => 'id,created_time,ad_id,adset_id,campaign_id,form_id,is_organic,field_data',
            'limit' => max(1, min(100, $limit)),
        ], 2000, $accessToken);
    }

    /**
     * فورم Lead Ads للصفحات (أو حساب الإعلانات كاحتياطي).
     *
     * @param  list<string>  $pageIds
     * @return array{success: bool, data?: list<array{id: string, name?: string, page_id?: string, token?: ?string}>, error?: string}
     */
    public function listLeadgenForms(array $pageIds = []): array
    {
        $formsById = [];
        $errors = [];

        foreach ($pageIds as $pageId) {
            $pageId = trim((string) $pageId);
            if ($pageId === '') {
                continue;
            }

            $pageToken = null;
            try {
                $page = \App\Domains\Messaging\Models\MetaSocialPage::query()
                    ->where('page_id', $pageId)
                    ->where('is_active', true)
                    ->first(['id', 'page_id', 'page_access_token']);
                $pageToken = $page?->page_access_token ? trim((string) $page->page_access_token) : null;
            } catch (\Throwable) {
                $pageToken = null;
            }

            $result = $this->paginateGet($pageId.'/leadgen_forms', [
                'fields' => 'id,name,status,leads_count,created_time',
                'limit' => 100,
            ], 300, $pageToken);

            if (! ($result['success'] ?? false)) {
                $errors[] = $pageId.': '.($result['error'] ?? 'فشل');
                continue;
            }

            foreach (($result['data'] ?? []) as $row) {
                if (! is_array($row) || blank($row['id'] ?? null)) {
                    continue;
                }
                $id = (string) $row['id'];
                $formsById[$id] = [
                    'id' => $id,
                    'name' => (string) ($row['name'] ?? $id),
                    'page_id' => $pageId,
                    'token' => $pageToken,
                    'leads_count' => $row['leads_count'] ?? null,
                ];
            }
        }

        if ($formsById === []) {
            $account = MetaAdsSettings::adAccountId();
            if ($account !== '') {
                $accountForms = $this->paginateGet($account.'/leadgen_forms', [
                    'fields' => 'id,name,status,leads_count,page_id',
                    'limit' => 100,
                ], 300);
                if ($accountForms['success'] ?? false) {
                    foreach (($accountForms['data'] ?? []) as $row) {
                        if (! is_array($row) || blank($row['id'] ?? null)) {
                            continue;
                        }
                        $id = (string) $row['id'];
                        $formsById[$id] = [
                            'id' => $id,
                            'name' => (string) ($row['name'] ?? $id),
                            'page_id' => (string) ($row['page_id'] ?? ''),
                            'token' => null,
                            'leads_count' => $row['leads_count'] ?? null,
                        ];
                    }
                } elseif (! empty($accountForms['error'])) {
                    $errors[] = 'ad_account: '.$accountForms['error'];
                }
            }
        }

        if ($formsById === []) {
            return [
                'success' => false,
                'error' => $errors !== []
                    ? 'تعذر جلب فورم الليدز: '.implode(' | ', array_slice($errors, 0, 3))
                    : 'لا توجد leadgen forms على الصفحات/الحساب',
                'data' => [],
            ];
        }

        return ['success' => true, 'data' => array_values($formsById)];
    }

    /**
     * Page IDs to scan for leadgen forms (settings + active Meta Social pages).
     *
     * @return list<string>
     */
    public function leadgenPageIds(): array
    {
        $ids = [];
        $configured = trim((string) MetaAdsSettings::pageId());
        if ($configured !== '') {
            $ids[] = $configured;
        }

        try {
            $pageIds = \App\Domains\Messaging\Models\MetaSocialPage::query()
                ->where('is_active', true)
                ->pluck('page_id')
                ->map(fn ($id) => trim((string) $id))
                ->filter()
                ->all();
            foreach ($pageIds as $pageId) {
                $ids[] = $pageId;
            }
        } catch (\Throwable) {
            //
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{success: bool, data?: list<array<string, mixed>>, error?: string}
     */
    protected function paginateGet(string $path, array $query, int $maxItems = 500, ?string $accessToken = null): array
    {
        $rows = [];
        $nextUrl = null;
        $pages = 0;
        $token = trim((string) ($accessToken ?: MetaAdsSettings::accessToken()));
        if ($token === '') {
            return ['success' => false, 'error' => 'Access Token مفقود'];
        }

        do {
            $pages++;
            if ($nextUrl) {
                try {
                    $response = Http::timeout(45)->get($nextUrl);
                    $json = $response->json();
                    if (! is_array($json) || ! $response->successful() || isset($json['error'])) {
                        $error = $this->graphErrorMessage(is_array($json) ? $json : [], 'طلب Meta فشل');

                        return $rows === []
                            ? ['success' => false, 'error' => $error]
                            : ['success' => true, 'data' => $rows, 'error' => $error];
                    }
                    $pageData = $json;
                } catch (\Throwable $e) {
                    return $rows === []
                        ? ['success' => false, 'error' => $e->getMessage()]
                        : ['success' => true, 'data' => $rows, 'error' => $e->getMessage()];
                }
            } else {
                $result = $this->requestWithToken('get', $path, $query, $token);
                if (! ($result['success'] ?? false)) {
                    return $result;
                }
                $pageData = $result['data'] ?? [];
            }

            foreach (($pageData['data'] ?? []) as $row) {
                if (is_array($row)) {
                    $rows[] = $row;
                    if (count($rows) >= $maxItems) {
                        return ['success' => true, 'data' => $rows];
                    }
                }
            }

            $nextUrl = null;
            $pagingNext = $pageData['paging']['next'] ?? null;
            if (is_string($pagingNext) && $pagingNext !== '') {
                $nextUrl = $pagingNext;
            }
        } while ($nextUrl && $pages < 50);

        return ['success' => true, 'data' => $rows];
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
        return $this->requestWithToken($method, $path, $params, MetaAdsSettings::accessToken());
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    protected function requestWithToken(string $method, string $path, array $params, string $token): array
    {
        $token = trim($token);
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
