<?php

namespace Tests\Unit;

use App\Services\WhatsAppBridgeService;
use Tests\TestCase;

class WhatsAppBridgeConnectionMetaTest extends TestCase
{
    public function test_legacy_bridge_status_ready_with_phone_allows_send(): void
    {
        $meta = app(WhatsAppBridgeService::class)->connectionMeta([
            'success' => true,
            'status' => 'ready',
            'phone' => '201044610510',
            'pushname' => 'Solvesta',
            'ready_at' => '2026-06-25T23:26:07.837Z',
            'last_error' => null,
        ]);

        $this->assertTrue($meta['can_send']);
        $this->assertSame('ready', $meta['status']);
    }

    public function test_modern_bridge_requires_send_ready_or_connected(): void
    {
        $service = app(WhatsAppBridgeService::class);

        $blocked = $service->connectionMeta([
            'status' => 'ready',
            'connected' => true,
            'send_ready' => false,
        ]);
        $this->assertTrue($blocked['can_send'], 'connected+ready should allow send when send_ready omitted logic uses sessionPresent');

        $ready = $service->connectionMeta([
            'status' => 'ready',
            'connected' => true,
            'send_ready' => true,
        ]);
        $this->assertTrue($ready['can_send']);
    }

    public function test_legacy_ready_without_phone_blocks_send(): void
    {
        $meta = app(WhatsAppBridgeService::class)->connectionMeta([
            'status' => 'ready',
            'last_error' => null,
        ]);

        $this->assertFalse($meta['can_send']);
    }
}
