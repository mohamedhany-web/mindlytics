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

    public function test_set_campaign_status_posts_to_graph(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['success' => true], 200),
        ]);

        $result = (new MetaAdsGraphService)->setCampaignStatus('111', 'PAUSED');

        $this->assertTrue($result['success']);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/111')
                && $request['status'] === 'PAUSED'
                && $request['access_token'] === 'test-token-xyz';
        });
    }
}
