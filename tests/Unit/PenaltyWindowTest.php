<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\PenaltyWindow;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenaltyWindowTest extends TestCase
{
    private function employee(?string $hireDate, ?string $terminationDate = null): User
    {
        $user = new User;
        $user->forceFill([
            'id' => 999,
            'name' => 'Test Employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => $hireDate,
            'termination_date' => $terminationDate,
        ]);

        return $user;
    }

    public function test_days_before_hire_date_are_not_employed(): void
    {
        $employee = $this->employee('2026-06-19');

        $this->assertFalse($employee->isEmployedOn(Carbon::parse('2026-06-10')));
        $this->assertFalse($employee->isEmployedOn(Carbon::parse('2026-06-18')));
        $this->assertTrue($employee->isEmployedOn(Carbon::parse('2026-06-19')));
        $this->assertTrue($employee->isEmployedOn(Carbon::parse('2026-07-01')));
    }

    public function test_days_after_termination_are_not_employed(): void
    {
        $employee = $this->employee('2026-01-01', '2026-06-30');

        $this->assertTrue($employee->isEmployedOn(Carbon::parse('2026-06-30')));
        $this->assertFalse($employee->isEmployedOn(Carbon::parse('2026-07-01')));
    }

    public function test_employee_without_hire_date_is_always_employed(): void
    {
        $employee = $this->employee(null);

        $this->assertTrue($employee->isEmployedOn(Carbon::parse('2020-01-01')));
    }

    public function test_pre_hire_dates_are_not_chargeable(): void
    {
        Storage::fake('public');
        $employee = $this->employee('2026-06-19');

        $this->assertFalse(PenaltyWindow::isChargeable(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-06-10')
        ));

        $this->assertTrue(PenaltyWindow::isChargeable(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-06-25')
        ));
    }

    public function test_effective_from_blocks_earlier_dates(): void
    {
        Storage::fake('public');
        SalesDailyReportSettings::save(['penalty_effective_from' => '2026-07-01']);

        $employee = $this->employee('2026-06-19');

        $this->assertFalse(PenaltyWindow::isChargeable(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-06-25')
        ));

        $this->assertTrue(PenaltyWindow::isChargeable(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-07-02')
        ));
    }

    public function test_earliest_chargeable_date_clamps_to_hire_date(): void
    {
        Storage::fake('public');
        $employee = $this->employee('2026-06-19');

        $earliest = PenaltyWindow::earliestChargeableDate(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-05-01')
        );

        $this->assertSame('2026-06-19', $earliest->toDateString());
    }

    public function test_earliest_chargeable_date_clamps_to_effective_from(): void
    {
        Storage::fake('public');
        SalesDailyReportSettings::save(['penalty_effective_from' => '2026-08-01']);

        $employee = $this->employee('2026-06-19');

        $earliest = PenaltyWindow::earliestChargeableDate(
            PenaltyWindow::SALES_DAILY_REPORT,
            $employee,
            Carbon::parse('2026-05-01')
        );

        $this->assertSame('2026-08-01', $earliest->toDateString());
    }
}
