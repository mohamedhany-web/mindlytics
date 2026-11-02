<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\InstallmentPlan;
use App\Domains\Learning\Models\AcademicYear;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use Illuminate\Support\Facades\Cache;
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


    /** الرئيسية وصفحة التوصيف بيعرضوا نفس سعر الـ checkout، مش سعر اللوحة لو مختلف. */
    public function test_prices_match_the_linked_path_and_course_checkout_price(): void
    {
        SalesDiplomaBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::query()->update(['is_active' => false]);
        Cache::forget('site.has_intakes');

        $year = AcademicYear::query()->where('is_active', true)->firstOrFail();
        $year->update(['price' => 7777]);
        $diploma = SalesDiplomaBoardEntry::create(['name' => 'Smoke Linked Diploma', 'starts_at' => now()->addDays(3)->toDateString(), 'price_online' => 9999, 'academic_year_id' => $year->id, 'is_active' => true, 'landing_published' => true]);

        $course = AdvancedCourse::query()->firstOrFail();
        $course->update(['is_active' => true, 'price' => 3000, 'discount_amount' => 500, 'is_free' => false]);
        $board = SalesCourseBoardEntry::create(['name' => 'Smoke Linked Course', 'starts_at' => now()->addDays(4)->toDateString(), 'price_online' => 1111, 'advanced_course_id' => $course->id, 'is_active' => true, 'landing_published' => false]);
        InstallmentPlan::create(['name' => 'Smoke Plan', 'slug' => 'smoke-plan-'.uniqid(), 'advanced_course_id' => $course->id, 'total_amount' => 2500, 'deposit_amount' => 500, 'installments_count' => 4, 'frequency_unit' => 'month', 'frequency_interval' => 1, 'is_active' => true]);

        $this->assertSame(['board' => 9999.0, 'linked' => 7777.0, 'linked_name' => $year->name], $diploma->fresh()->priceConflict());
        $this->get('/?lang=ar')->assertOk()
            ->assertSee('7,777 ج.م')->assertDontSee('9,999')
            ->assertSee('2,500 ج.م')->assertDontSee('1,111')
            ->assertSee('تقسيط على 4 أقساط')
            ->assertSee('تقسيط لبعض البرامج')
            ->assertSee('القبول مفتوح للدفعات القادمة')
            ->assertSee(route('public.course.show', $course->id), false); // التفاصيل = صفحة الكورس المربوط لما التوصيف مش منشور
        $this->get(route('public.sales-diploma-board.show', $diploma->slug))->assertOk()->assertSee('7,777')->assertDontSee('9,999');
    }

    /** من غير دفعات ولا تقسيط: الصفحة ما تدّعيش حاجات مش موجودة. */
    public function test_no_claims_without_data(): void
    {
        SalesDiplomaBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::query()->update(['is_active' => false]);
        InstallmentPlan::query()->update(['is_active' => false]);
        Cache::forget('site.has_intakes');

        $this->get('/?lang=ar')->assertOk()
            ->assertDontSee('القبول مفتوح للدفعات القادمة')
            ->assertSee('احجز جلسة إرشاد مجانية')
            ->assertDontSee('تقسيط لبعض البرامج')
            ->assertDontSee('طريقتا دراسة');
    }

    /** المحاضرون في الرئيسية بس اللي صفحتهم بتفتح فعلاً. */
    public function test_homepage_instructor_links_open(): void
    {
        $html = $this->get('/?lang=ar')->assertOk()->getContent();
        preg_match_all('#href="[^"]*/instructors/(\d+)"#', $html, $m);
        foreach (array_unique($m[1]) as $id) {
            $this->get('/instructors/'.$id)->assertOk();
        }
        $this->assertTrue(true);
    }
}
