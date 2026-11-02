<?php

namespace Tests\Smoke;

use App\Domains\Learning\Models\AcademicYear;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use App\Domains\Sales\Models\SalesDiplomaBoardEntry;

/**
 * البرامج التدريبية (/learning-paths) وصفحة البرنامج والكورسات على هوية الموقع:
 * بيانات الدفعة من لوحة الدبلومات المربوطة، والكورسات بدفعات جاية من لوحة الكورسات.
 */
class ProgramPagesSmokeTest extends SmokeTestCase
{
    public function test_program_list_and_page_show_the_linked_intake(): void
    {
        $year = AcademicYear::create(['name' => 'Smoke Program Pages Diploma', 'code' => 'SMK-PP', 'is_active' => true, 'price' => 9000, 'order' => 0, 'description' => 'Smoke program description']);
        SalesDiplomaBoardEntry::create(['name' => 'Smoke Program Board', 'academic_year_id' => $year->id, 'starts_at' => now()->addDays(12)->toDateString(), 'duration' => 'Smoke 4 months',
            'format' => 'أونلاين مباشر', 'highlights' => ['Smoke highlight one'], 'is_active' => true, 'landing_published' => false]);

        $this->get('/learning-paths?lang=ar')->assertOk()
            ->assertSee('البرامج التدريبية')
            ->assertSee('Smoke Program Pages Diploma')
            ->assertSee('Smoke 4 months')
            ->assertSee('9,000');

        $this->get(route('public.learning-path.show', 'smoke-program-pages-diploma').'?lang=ar')->assertOk()
            ->assertSee('برنامج تدريبي')
            ->assertSee('Smoke highlight one')
            ->assertSee('Smoke 4 months')
            ->assertSee(route('public.learning-path.checkout', 'smoke-program-pages-diploma'), false)
            ->assertDontSee('cdn.tailwindcss.com', false);
    }

    public function test_courses_page_lists_upcoming_course_intakes(): void
    {
        SalesCourseBoardEntry::query()->update(['is_active' => false]);
        SalesCourseBoardEntry::create(['name' => 'Smoke Intake Course', 'starts_at' => now()->addDays(5)->toDateString(), 'price_online' => 2500, 'format' => 'أونلاين مباشر', 'is_active' => true, 'landing_published' => false]);
        SalesCourseBoardEntry::create(['name' => 'Smoke Inactive Course', 'is_active' => false]);

        $this->get('/courses?lang=ar')->assertOk()
            ->assertSee('كورسات بدفعات جاية')
            ->assertSee('Smoke Intake Course')
            ->assertSee('2,500')
            ->assertDontSee('Smoke Inactive Course');
    }

    public function test_mobile_menu_is_a_single_side_drawer(): void
    {
        $html = $this->get('/?lang=ar')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'id="h-drawer"'));
        $this->assertSame(1, substr_count($html, "querySelector('[data-menu-toggle]')"));
    }
}
