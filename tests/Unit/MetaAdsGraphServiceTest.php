<?php

namespace Tests\Unit;

use App\Services\MetaAds\MetaAdsGraphService;
use App\Support\MetaAdsSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MetaAdsGraphServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        MetaAdsSettings::clearCache();
        MetaAdsSettings::save([
            'enabled' => true,
            'ad_account_id' => 'act_999',
            'access_token' => 'test-token-xyz',
            'api_url' => 'https://graph.facebook.com/v21.0',
            'default_currency' => 'EGP',
            'default_country' => 'EG',
        ]);
    }

    protected function tearDown(): void
    {
        MetaAdsSettings::clearCache();
        parent::tearDown();
    }

    public function test_normalize_and_ready_settings(): void
    {
        $this->assertSame('act_999', MetaAdsSettings::adAccountId());
        $this->assertTrue(MetaAdsSettings::isReady());
        $this->assertSame('act_12345', MetaAdsSettings::normalizeAdAccountId('12345'));
    }

    public function test_build_targeting_and_budget_units(): void
    {
        $service = new MetaAdsGraphService;
        $this->assertSame(15000, $service->toMinorUnits(150));
        $this->assertSame(150.0, $service->fromMinorUnits(15000));

        $targeting = $service->buildTargeting([
            'age_min' => 20,
            'age_max' => 40,
            'genders' => 'female',
            'countries' => 'EG, SA',
        ]);

        $this->assertSame(20, $targeting['age_min']);
        $this->assertSame(40, $targeting['age_max']);
        $this->assertSame([2], $targeting['genders']);
        $this->assertSame(['EG', 'SA'], $targeting['geo_locations']['countries']);
    }

    public function test_list_campaigns_parses_graph_response(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'data' => [
                    ['id' => '1', 'name' => 'Summer', 'status' => 'PAUSED', 'objective' => 'OUTCOME_TRAFFIC'],
                ],
            ], 200),
        ]);

        $result = (new MetaAdsGraphService)->listCampaigns();

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertSame('Summer', $result['data'][0]['name']);
    }

    public function test_list_ad_accounts_merges_personal_and_business(): void
    {
        Http::fake([
            'graph.facebook.com/*/me/adaccounts*' => Http::response([
                'data' => [
                    ['id' => 'act_111', 'name' => 'Personal Ads', 'account_status' => 1, 'currency' => 'EGP'],
                ],
            ], 200),
            'graph.facebook.com/*/me?*' => Http::response(['id' => '1', 'name' => 'User'], 200),
            'graph.facebook.com/*/me/businesses*' => Http::response([
                'data' => [['id' => 'biz_1', 'name' => 'Mind Biz']],
            ], 200),
            'graph.facebook.com/*/biz_1/owned_ad_accounts*' => Http::response([
                'data' => [
                    ['id' => 'act_222', 'name' => 'Biz Ads', 'account_status' => 1, 'currency' => 'USD'],
                ],
            ], 200),
            'graph.facebook.com/*/biz_1/client_ad_accounts*' => Http::response(['data' => []], 200),
            'graph.facebook.com/*/me/permissions*' => Http::response([
                'data' => [
                    ['permission' => 'ads_read', 'status' => 'granted'],
                    ['permission' => 'ads_management', 'status' => 'granted'],
                ],
            ], 200),
        ]);

        $result = (new MetaAdsGraphService)->listAdAccounts();

        $this->assertTrue($result['success']);
        $ids = collect($result['data'])->pluck('id')->all();
        $this->assertContains('act_111', $ids);
        $this->assertContains('act_222', $ids);
    }
}
