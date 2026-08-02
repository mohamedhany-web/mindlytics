<?php

namespace Tests\Unit;

use App\Services\MetaSocial\MetaSocialContactCaptureService;
use Tests\TestCase;

class MetaSocialContactCaptureServiceTest extends TestCase
{
    public function test_extracts_egyptian_phone_and_email(): void
    {
        $svc = new MetaSocialContactCaptureService;
        $text = 'مساء الخير رقمى 01012345678 والإيميل sales@mindlytics-academy.com';

        $got = $svc->extractFromText($text);

        $this->assertSame('+201012345678', $got['phone']);
        $this->assertSame('sales@mindlytics-academy.com', $got['email']);
    }

    public function test_normalizes_plus_twenty_phone(): void
    {
        $svc = new MetaSocialContactCaptureService;

        $this->assertSame('+201112223334', $svc->extractPhone('كلمني على +20 111 222 3334'));
    }
}
