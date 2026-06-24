<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PlatformErrorLogPageTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/platform-errors')
            ->assertRedirect('/login');
    }

    public function test_admin_can_load_platform_errors_page(): void
    {
        $admin = User::query()->whereIn('role', ['admin', 'super_admin'])->first();

        if (! $admin) {
            $this->markTestSkipped('No admin user in database.');
        }

        $response = $this->actingAs($admin)->get('/admin/platform-errors');

        $response->assertOk();
        $response->assertSee('مراقبة أخطاء المنصة', false);
    }
}
