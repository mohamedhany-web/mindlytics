<?php

namespace Tests\Security;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_public_response_includes_core_security_headers(): void
    {
        $response = $this->get('/up');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy');
        $this->assertNotEmpty($response->headers->get('Referrer-Policy'));
    }
}
