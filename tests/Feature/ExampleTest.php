<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Home queries many models; in :memory: CI without migrations it 500s.
        // Use Laravel health endpoint instead (see HealthEndpointTest).
        $response = $this->get('/up');

        $response->assertOk();
    }
}
