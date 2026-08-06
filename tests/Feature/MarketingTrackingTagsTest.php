<?php

namespace Tests\Feature;

use App\Support\MarketingWebAnalyticsSettings;
use Tests\TestCase;

class MarketingTrackingTagsTest extends TestCase
{
    private string $settingsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsPath = storage_path('app/site/marketing_web_analytics.json');
        if (is_file($this->settingsPath)) {
            @unlink($this->settingsPath);
        }
    }

    protected function tearDown(): void
    {
        if (is_file($this->settingsPath)) {
            @unlink($this->settingsPath);
        }
        parent::tearDown();
    }

    public function test_gtm_snippet_renders_when_container_id_is_set(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => 'GTM-TEST123',
            'clarity_project_id' => '',
            'meta_pixel_id' => '',
            'meta_pixel_enabled' => false,
        ]);

        $html = view('components.tracking-tags', ['placement' => 'head'])->render();

        $this->assertStringContainsString('GTM-TEST123', $html);
        $this->assertStringContainsString('googletagmanager.com/gtm.js', $html);
        $this->assertStringContainsString('window.dataLayer', $html);
    }

    public function test_gtm_snippet_hidden_when_container_id_empty(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => '',
            'clarity_project_id' => '',
            'meta_pixel_id' => '',
            'meta_pixel_enabled' => false,
        ]);

        $html = view('components.tracking-tags', ['placement' => 'head'])->render();

        $this->assertStringNotContainsString('googletagmanager.com/gtm.js', $html);
        $this->assertStringContainsString('window.dataLayer', $html);
    }

    public function test_clarity_snippet_renders_when_project_id_set(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => '',
            'clarity_project_id' => 'claritytest',
            'meta_pixel_id' => '',
            'meta_pixel_enabled' => false,
        ]);

        $html = view('components.tracking-tags', ['placement' => 'head'])->render();

        $this->assertStringContainsString('clarity.ms/tag/', $html);
        $this->assertStringContainsString('claritytest', $html);
    }

    public function test_meta_pixel_snippet_renders_when_enabled(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => '',
            'clarity_project_id' => '',
            'meta_pixel_id' => '9876543210',
            'meta_pixel_enabled' => true,
        ]);

        $html = view('components.tracking-tags', ['placement' => 'head'])->render();

        $this->assertStringContainsString('fbevents.js', $html);
        $this->assertStringContainsString('9876543210', $html);
        $this->assertStringContainsString("fbq('track', 'PageView')", $html);
    }

    public function test_tracking_disabled_renders_nothing(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => false,
            'gtm_container_id' => 'GTM-TEST123',
            'clarity_project_id' => 'claritytest',
            'meta_pixel_id' => '111',
            'meta_pixel_enabled' => true,
        ]);

        $head = view('components.tracking-tags', ['placement' => 'head'])->render();
        $body = view('components.tracking-tags', ['placement' => 'body'])->render();

        $this->assertSame('', trim($head));
        $this->assertSame('', trim($body));
    }

    public function test_gtm_noscript_in_body_placement(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => 'GTM-BODY99',
        ]);

        $html = view('components.tracking-tags', ['placement' => 'body'])->render();

        $this->assertStringContainsString('GTM-BODY99', $html);
        $this->assertStringContainsString('googletagmanager.com/ns.html', $html);
    }

    public function test_settings_persist_gtm_ga4_clarity_and_meta(): void
    {
        MarketingWebAnalyticsSettings::save([
            'enabled' => true,
            'gtm_container_id' => 'GTM-ADMIN1',
            'ga4_measurement_id' => 'G-ADMIN1',
            'clarity_project_id' => 'clr1',
            'meta_pixel_id' => '555666777',
            'meta_pixel_enabled' => true,
            'currency' => 'EGP',
            'item_brand' => 'Mindlytics',
        ]);

        $all = MarketingWebAnalyticsSettings::all();
        $this->assertSame('GTM-ADMIN1', $all['gtm_container_id']);
        $this->assertSame('G-ADMIN1', $all['ga4_measurement_id']);
        $this->assertSame('clr1', $all['clarity_project_id']);
        $this->assertSame('555666777', $all['meta_pixel_id']);
        $this->assertTrue(MarketingWebAnalyticsSettings::metaPixelEnabled());
    }
}