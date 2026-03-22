<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * تحقق أساسي من أن مسار صفحة التعلم لا يُرجع 500 (مثلاً متغيرات العرض الناقصة).
 */
class StudentLearnRouteTest extends TestCase
{
    public function test_learn_route_redirects_guest_to_login(): void
    {
        $response = $this->get('/my-courses/1/learn');

        $this->assertNotSame(500, $response->getStatusCode(), 'Learn route must not return 500 for guest');
        $response->assertRedirect();
    }
}
