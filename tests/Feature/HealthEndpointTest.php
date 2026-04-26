<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Health check should not depend on application tables (unlike home /).
 */
class HealthEndpointTest extends TestCase
{
    public function test_up_endpoint_returns_success(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
    }
}
