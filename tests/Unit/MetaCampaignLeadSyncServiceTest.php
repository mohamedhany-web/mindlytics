<?php

namespace Tests\Unit;

use App\Domains\Messaging\Services\MetaAdsGraphService;
use App\Domains\Messaging\Services\MetaCampaignLeadSyncService;
use App\Domains\Messaging\Services\MetaSocialCrmService;
use Mockery;
use Tests\TestCase;

class MetaCampaignLeadSyncServiceTest extends TestCase
{
    public function test_parse_field_data_extracts_name_phone_email(): void
    {
        $service = new MetaCampaignLeadSyncService(
            Mockery::mock(MetaAdsGraphService::class),
            Mockery::mock(MetaSocialCrmService::class),
        );

        $parsed = $service->parseFieldData([
            ['name' => 'full_name', 'values' => ['أحمد محمد']],
            ['name' => 'phone_number', 'values' => ['0100 123 4567']],
            ['name' => 'email', 'values' => ['ahmed@example.com']],
        ]);

        $this->assertSame('أحمد محمد', $parsed['name']);
        $this->assertSame('01001234567', $parsed['phone']);
        $this->assertSame('ahmed@example.com', $parsed['email']);
    }

    public function test_parse_field_data_composes_first_last_name(): void
    {
        $service = new MetaCampaignLeadSyncService(
            Mockery::mock(MetaAdsGraphService::class),
            Mockery::mock(MetaSocialCrmService::class),
        );

        $parsed = $service->parseFieldData([
            ['name' => 'first_name', 'values' => ['Sara']],
            ['name' => 'last_name', 'values' => ['Ali']],
            ['name' => 'mobile', 'values' => ['+201112223334']],
        ]);

        $this->assertSame('Sara Ali', $parsed['name']);
        $this->assertSame('+201112223334', $parsed['phone']);
    }

    public function test_extract_lead_gen_form_id_from_creative_spec(): void
    {
        $formId = (new MetaAdsGraphService)->extractLeadGenFormIdFromAd([
            'id' => '111',
            'creative' => [
                'object_story_spec' => [
                    'link_data' => [
                        'call_to_action' => [
                            'value' => ['lead_gen_form_id' => '999888'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame('999888', $formId);
        $this->assertNull((new MetaAdsGraphService)->extractLeadGenFormIdFromAd(['id' => '1']));
    }
}
