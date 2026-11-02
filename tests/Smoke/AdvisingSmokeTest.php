<?php

namespace Tests\Smoke;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBlockedDate;
use App\Domains\Advising\Models\AdvisingBooking;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * الإرشاد المهني (docs/ADVISING.md): الإدارة بتحدد التوافر، والطالب بيحجز من البوب أب،
 * والميعاد مش بيتحجز مرتين، والأيام المقفولة بتختفي، والإدارة بتسجّل نتيجة الجلسة.
 */
class AdvisingSmokeTest extends SmokeTestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // إعدادات المنصة (PlatformSettings) من غير ما نلمس الملف الحقيقي
        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        AdvisingAvailability::query()->update(['is_active' => false]);
    }

    public function test_admin_sets_availability_and_a_student_books_a_session(): void
    {
        $this->actingAs($this->admin)->put(route('admin.advising.settings.update'), ['enabled' => '1', 'window_days' => 7, 'min_notice_hours' => 0])->assertRedirect();
        $this->actingAs($this->admin)->post(route('admin.advising.availability.store'), [
            'weekdays' => [0, 1, 2, 3, 4, 5, 6], 'start_time' => '10:00', 'end_time' => '23:00', 'slot_minutes' => 60, 'capacity' => 1, 'mode' => 'online',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($this->admin)->get(route('admin.advising.availability'))->assertOk()->assertSee('مواعيد التوافر الأسبوعية');
        auth()->logout();

        $days = $this->getJson(route('advising.slots'))->assertOk()->json('days');
        $this->assertNotEmpty($days);
        $slot = $days[count($days) - 1]['slots'][0]['key'];

        // من غير هدف: مرفوض
        $this->postJson(route('advising.store'), ['name' => 'X', 'phone' => '01000000000', 'slot' => $slot])->assertStatus(422)->assertJsonValidationErrors('goal');

        $payload = ['name' => 'Smoke Advisee', 'phone' => '01000000088', 'goal' => 'career_change', 'level' => 'basics', 'interest' => 'Data', 'notes' => 'smoke', 'slot' => $slot];
        $this->postJson(route('advising.store'), $payload)->assertOk()->assertJsonPath('ok', true);
        $booking = AdvisingBooking::where('phone', '01000000088')->firstOrFail();
        $this->assertSame('pending', $booking->status);
        $this->assertSame('career_change', $booking->goal);

        // نفس الميعاد (سعته 1) مش بيتحجز تاني، وبيختفي من المواعيد
        $this->postJson(route('advising.store'), ['phone' => '01000000099'] + $payload)->assertStatus(422)->assertJsonValidationErrors('slot');
        $keys = collect($this->getJson(route('advising.slots'))->json('days'))->flatMap(fn ($d) => collect($d['slots'])->pluck('key'));
        $this->assertNotContains($slot, $keys->all());

        // الإدارة بتشوف الحجز وتسجّل نتيجة الجلسة
        $this->actingAs($this->admin)->get(route('admin.advising.bookings', ['range' => 'all']))->assertOk()->assertSee('Smoke Advisee');
        $this->actingAs($this->admin)->put(route('admin.advising.bookings.update', $booking), [
            'status' => 'done', 'outcome' => 'محتاج أساسيات SQL الأول', 'recommended_program' => 'دبلومة تحليل البيانات',
        ])->assertRedirect();
        $this->assertSame('done', $booking->fresh()->status);
        $this->assertSame('دبلومة تحليل البيانات', $booking->fresh()->recommended_program);
    }

    public function test_blocked_day_and_closed_booking_hide_slots(): void
    {
        AdvisingAvailability::create(['weekday' => now()->addDays(2)->dayOfWeek, 'start_time' => '10:00', 'end_time' => '12:00', 'slot_minutes' => 30, 'capacity' => 1, 'mode' => 'campus', 'is_active' => true]);
        $date = now()->addDays(2)->toDateString();
        $this->assertContains($date, collect($this->getJson(route('advising.slots'))->json('days'))->pluck('date')->all());

        $this->actingAs($this->admin)->post(route('admin.advising.blocked.store'), ['date' => $date, 'reason' => 'smoke'])->assertRedirect();
        $this->assertTrue(AdvisingBlockedDate::whereDate('date', $date)->exists());
        auth()->logout();
        $this->assertNotContains($date, collect($this->getJson(route('advising.slots'))->json('days'))->pluck('date')->all());

        $this->actingAs($this->admin)->put(route('admin.advising.settings.update'), ['window_days' => 14, 'min_notice_hours' => 3])->assertRedirect(); // enabled مش متعلّم = مقفول
        auth()->logout();
        $this->assertSame([], $this->getJson(route('advising.slots'))->json('days'));
    }

    public function test_booking_page_and_popup_render(): void
    {
        $this->get(route('advising.show'))->assertOk()->assertSee('خلّينا نساعدك تعرف تبدأ منين');
        $this->get('/?lang=ar')->assertOk()->assertSee('id="h-advising"', false)->assertSee('data-advising-open', false);
        $this->get('/faq')->assertOk()->assertSee('id="h-advising"', false);
    }
}
