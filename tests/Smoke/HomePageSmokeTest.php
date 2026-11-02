<?php

namespace Tests\Smoke;

use App\Domains\Sales\Models\SalesCourseBoardEntry;
use App\Domains\Sales\Models\SalesDiplomaBoardEntry;

/**
 * الصفحة الرئيسية الجديدة (docs/HOMEPAGE.md): بالعربي من لوحة الدبلومات والكورسات، والإنجليزي على الصفحة القديمة،
 * وحجز الاستشارة بقى بوب أب الإرشاد المهني (tests/Smoke/AdvisingSmokeTest.php).
 */
class HomePageSmokeTest extends SmokeTestCase
{
    public function test_arabic_homepage_shows_intakes_from_the_boards(): void
    {
        SalesDiplomaBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::query()->update(['is_active' => false]);
        SalesDiplomaBoardEntry::create(['name' => 'Smoke Data Diploma', 'starts_at' => now()->addDays(10)->toDateString(), 'schedule_days' => 'Sat & Tue', 'price_online' => 9000, 'format' => 'أونلاين مباشر', 'is_active' => true, 'landing_published' => false]);
        SalesCourseBoardEntry::create(['name' => 'Smoke Python Course', 'start_label' => 'December', 'price_online' => 2500, 'format' => 'حضوري بالفرع', 'is_active' => true, 'landing_published' => false]);
        SalesCourseBoardEntry::create(['name' => 'Smoke Hidden Course', 'is_active' => false]);

        $html = $this->get('/?lang=ar')->assertOk()
            ->assertSee('نؤهّلك لمهنة حقيقية في عالم البرمجيات')
            ->assertDontSee('مدرسة')
            ->assertSee('images/home/classroom-1600.jpg', false)
            ->assertSee('Smoke Data Diploma')
            ->assertSee('Smoke Python Course')
            ->assertDontSee('Smoke Hidden Course')
            ->assertSee('9,000 ج.م')
            ->assertSee('data-mode="campus"', false)
            ->getContent();

        // الدبلومة اللي ليها تاريخ قبل الكورس اللي من غير تاريخ
        $this->assertLessThan(strpos($html, 'Smoke Python Course'), strpos($html, 'Smoke Data Diploma'));
    }

    public function test_course_with_a_start_date_is_ordered_by_date_and_admin_can_set_it(): void
    {
        SalesDiplomaBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::query()->update(['is_active' => false]);
        SalesDiplomaBoardEntry::create(['name' => 'Smoke Late Diploma', 'starts_at' => now()->addDays(40)->toDateString(), 'is_active' => true, 'landing_published' => false]);
        $course = SalesCourseBoardEntry::create(['name' => 'Smoke Early Course', 'is_active' => true, 'landing_published' => false]);

        $admin = \App\Models\User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $this->actingAs($admin)->put(route('admin.sales-course-board.update', $course), [
            'name' => 'Smoke Early Course', 'starts_at' => now()->addDays(5)->toDateString(), 'is_active' => '1', 'sort_order' => 0,
        ])->assertRedirect();
        $this->assertSame(now()->addDays(5)->toDateString(), $course->fresh()->starts_at?->toDateString());

        auth()->logout();
        $html = $this->get('/?lang=ar')->assertOk()->getContent();
        $this->assertLessThan(strpos($html, 'Smoke Late Diploma'), strpos($html, 'Smoke Early Course'));
    }

    public function test_homepage_without_board_data_still_renders(): void
    {
        SalesDiplomaBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::query()->update(['is_active' => false]);

        $this->get('/?lang=ar')->assertOk()->assertSee('أبلغوني بالدفعة القادمة');
    }

    public function test_english_homepage_uses_the_same_design(): void
    {
        $this->get('/?lang=en')->assertOk()
            ->assertSee('We prepare you for a real career in software')
            ->assertSee('dir="ltr"', false)
            ->assertSee('h-hero', false);
    }

}
