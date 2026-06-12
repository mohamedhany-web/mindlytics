<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDailyReportPenaltySettingsTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/employee-deductions/daily-report-penalty-settings')
            ->assertRedirect('/login');
    }
}
