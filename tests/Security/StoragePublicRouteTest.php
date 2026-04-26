<?php

namespace Tests\Security;

use Tests\TestCase;

/**
 * Public file proxy must not escape storage/app/public.
 */
class StoragePublicRouteTest extends TestCase
{
    public function test_traversal_style_storage_request_returns_404(): void
    {
        $response = $this->get('/storage/../../.env');

        $this->assertContains($response->getStatusCode(), [403, 404], 'Traversal outside storage must be blocked');
    }
}
