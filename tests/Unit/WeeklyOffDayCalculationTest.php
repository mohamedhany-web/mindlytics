<?php

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class WeeklyOffDayCalculationTest extends TestCase
{
    public function test_friday_off_is_only_friday(): void
    {
        $user = new User(['weekly_off_day' => 5]); // الجمعة

        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-10'))); // Friday
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-09'))); // Thursday
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-11'))); // Saturday
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-12'))); // Sunday
        $this->assertSame('الجمعة', $user->weeklyOffDayLabel());
    }

    public function test_sunday_off_is_only_sunday(): void
    {
        $user = new User(['weekly_off_day' => 0]);

        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-12')));
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-11')));
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-10')));
    }

    public function test_null_weekly_off_uses_weekend_default(): void
    {
        $user = new User(['weekly_off_day' => null]);

        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-10'))); // Friday
        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-11'))); // Saturday
        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-12'))); // Sunday
        $this->assertNull($user->weeklyOffDayLabel());
    }

    public function test_next_weekly_off_date_for_explicit_day(): void
    {
        $user = new User(['weekly_off_day' => 5]); // Friday

        $fromThursday = Carbon::parse('2026-07-09');
        $next = $user->nextWeeklyOffDate($fromThursday);

        $this->assertTrue($next->isSameDay(Carbon::parse('2026-07-10')));
        $this->assertTrue($user->isWeeklyOff($next));
    }

    public function test_next_weekly_off_includes_today_when_today_is_off(): void
    {
        $user = new User(['weekly_off_day' => 5]);
        $friday = Carbon::parse('2026-07-10');

        $this->assertTrue($user->nextWeeklyOffDate($friday)->isSameDay($friday));
    }

    public function test_weekly_off_blocks_workday_expectation(): void
    {
        $user = new User(['weekly_off_day' => 5]);

        // requiresDailyReportOn يعتمد أيضاً على الإجازات المعتمدة من DB؛
        // هنا نتحقق من أساس الحساب: يوم الراحة من ملف الموظف.
        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-10')));
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-09')));
    }

    public function test_empty_string_weekly_off_behaves_like_null_weekend_default(): void
    {
        $user = new User(['weekly_off_day' => '']);

        $this->assertNull($user->weekly_off_day);
        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-11'))); // Saturday
        $this->assertTrue($user->isWeeklyOff(Carbon::parse('2026-07-12'))); // Sunday
        $this->assertFalse($user->isWeeklyOff(Carbon::parse('2026-07-10'))); // Friday
    }

    public function test_app_timezone_is_cairo_for_correct_day_boundary(): void
    {
        $this->assertSame('Africa/Cairo', config('app.timezone'));
        $this->assertSame('Africa/Cairo', now()->timezoneName);
    }
}
