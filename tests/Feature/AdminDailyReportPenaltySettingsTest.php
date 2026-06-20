<?php

namespace Tests\Feature;

use App\Support\SalesDailyReportSettings;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminDailyReportPenaltySettingsTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/employee-deductions/daily-report-penalty-settings')
            ->assertRedirect('/login');
    }

    public function test_sales_daily_report_settings_persist_to_storage(): void
    {
        Storage::fake('public');

        SalesDailyReportSettings::save([
            'enabled' => true,
            'work_days_only' => true,
            'deadline_time' => '22:30',
            'penalty_enabled' => true,
            'penalty_amount' => 75.5,
            'penalty_title' => 'غرامة تقرير يومي',
            'penalty_description' => 'عدم التسليم في الموعد',
            'penalty_type' => 'penalty',
            'penalty_status' => 'pending',
            'kpi_submission_target_pct' => 90.0,
        ]);

        $settings = SalesDailyReportSettings::all();

        $this->assertTrue($settings['enabled']);
        $this->assertTrue($settings['penalty_enabled']);
        $this->assertSame('22:30', $settings['deadline_time']);
        $this->assertSame(75.5, (float) $settings['penalty_amount']);
        $this->assertSame('غرامة تقرير يومي', $settings['penalty_title']);
    }
}
