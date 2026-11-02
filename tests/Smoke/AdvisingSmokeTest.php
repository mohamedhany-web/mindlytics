<?php

namespace Tests\Smoke;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBlockedDate;
use App\Domains\Advising\Models\AdvisingBooking;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * الإرشاد المهني (docs/ADVISING.md): الإدارة بتحدد التوافر، والطالب بيحجز من البوب أب،
 * والميعاد مش بيتحجز مرتين، والأيام المقفولة بتختفي، والإدارة بتسجّل نتيجة الجلسة،
 * وكل حجز بيتربط بعميل محتمل في الـ CRM واجتماع في كاليندر الموظف.
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

    /** يفتح فترة توافر بكرة ويرجّع أول ميعاد فيها. */
    private function openSlot(): string
    {
        AdvisingAvailability::create(['weekday' => now()->addDay()->dayOfWeek, 'start_time' => '09:00', 'end_time' => '23:00', 'slot_minutes' => 30, 'capacity' => 5, 'mode' => 'online', 'is_active' => true]);
        $day = collect($this->getJson(route('advising.slots'))->json('days'))->firstWhere('date', now()->addDay()->toDateString());
        $this->assertNotNull($day);

        return $day['slots'][0]['key'];
    }

    private function settings(array $extra): void
    {
        $this->actingAs($this->admin)->put(route('admin.advising.settings.update'), $extra + ['enabled' => '1', 'window_days' => 7, 'min_notice_hours' => 0])->assertRedirect()->assertSessionHasNoErrors();
        auth()->logout();
    }

    public function test_new_booking_becomes_an_assigned_lead_with_a_meeting_in_the_crm(): void
    {
        $rep = User::salesEmployees()->where('is_active', true)->firstOrFail();
        $this->settings(['crm_sync' => '1', 'crm_assignee_id' => $rep->id]);
        $slot = $this->openSlot();
        $phone = '0109'.random_int(1000000, 9999999);

        $this->postJson(route('advising.store'), ['name' => 'Smoke CRM Advisee', 'phone' => $phone, 'goal' => 'first_job', 'level' => 'beginner', 'interest' => 'Smoke Track', 'notes' => 'محتاج أبدأ', 'slot' => $slot])->assertOk();

        $booking = AdvisingBooking::where('phone', $phone)->firstOrFail();
        $lead = SalesLead::findOrFail($booking->sales_lead_id);
        $this->assertSame($rep->id, (int) $lead->assigned_to);
        $this->assertSame('website', $lead->source);
        $this->assertSame('high', $lead->priority);
        $this->assertTrue(SalesActivity::where('sales_lead_id', $lead->id)->where('title', 'حجز جلسة إرشاد مهني من الموقع')->exists());
        $hasPlanned = class_exists(SalesPlannedActivity::class) && \Illuminate\Support\Facades\Schema::hasTable('sales_planned_activities');
        if ($hasPlanned) {
            $meeting = SalesPlannedActivity::findOrFail($booking->sales_planned_activity_id);
            $this->assertSame('meeting', $meeting->kind);
            $this->assertSame($rep->id, (int) $meeting->assigned_to);
            $this->assertSame($booking->starts_at->toDateTimeString(), $meeting->starts_at->toDateTimeString());
        } else {
            $this->assertNull($booking->sales_planned_activity_id); // من غير كاليندر الـ CRM: عميل ونشاط بس
        }

        // الجلسة اتعملت: النتيجة في سجل العميل، والبرنامج المرشَّح بقى اهتمامه، والاجتماع اتقفل
        $this->actingAs($this->admin)->put(route('admin.advising.bookings.update', $booking), [
            'status' => 'done', 'outcome' => 'يبدأ بأساسيات البرمجة', 'recommended_program' => 'Smoke Recommended Program',
        ])->assertRedirect();
        $this->assertTrue(SalesActivity::where('sales_lead_id', $lead->id)->where('title', 'نتيجة جلسة الإرشاد المهني')->exists());
        $this->assertSame('Smoke Recommended Program', $lead->fresh()->interest);
        if ($hasPlanned) {
            $this->assertSame('done', $meeting->fresh()->status);
        }

        $this->actingAs($this->admin)->get(route('admin.advising.bookings', ['range' => 'all']))->assertOk()->assertSee(route('admin.crm.leads.show', $lead->id), false);
    }

    public function test_booking_from_an_existing_lead_stays_with_its_owner_and_cancel_cancels_the_meeting(): void
    {
        $owner = User::salesEmployees()->where('is_active', true)->firstOrFail();
        $phone = '0108'.random_int(1000000, 9999999);
        $lead = SalesLead::create(['assigned_to' => $owner->id, 'name' => 'Smoke Existing Lead', 'phone' => $phone, 'source' => 'social', 'stage' => 'new_lead', 'priority' => 'normal', 'next_follow_up_at' => now()->addDays(20), 'follow_up_channel' => 'call']);
        $this->settings(['crm_sync' => '1']); // من غير موظف افتراضي
        $slot = $this->openSlot();

        $before = SalesLead::count();
        $this->postJson(route('advising.store'), ['name' => 'Smoke Existing', 'phone' => '+2'.$phone, 'goal' => 'career_change', 'slot' => $slot])->assertOk();
        $booking = AdvisingBooking::where('phone', '+2'.$phone)->firstOrFail();

        $this->assertSame($before, SalesLead::count());
        $this->assertSame($lead->id, (int) $booking->sales_lead_id);
        $this->assertSame($owner->id, (int) $lead->fresh()->assigned_to);
        $this->assertSame($booking->starts_at->toDateTimeString(), $lead->fresh()->next_follow_up_at->toDateTimeString()); // الجلسة أقرب من المتابعة القديمة

        $this->actingAs($this->admin)->put(route('admin.advising.bookings.update', $booking), ['status' => 'cancelled'])->assertRedirect();
        if (class_exists(SalesPlannedActivity::class) && \Illuminate\Support\Facades\Schema::hasTable('sales_planned_activities')) {
            $this->assertSame('cancelled', SalesPlannedActivity::findOrFail($booking->sales_planned_activity_id)->status);
        }
        $this->assertTrue(SalesActivity::where('sales_lead_id', $lead->id)->where('title', 'اتلغت جلسة الإرشاد المهني')->exists());
    }

    public function test_without_an_assignee_the_lead_waits_unassigned_and_sync_can_be_turned_off(): void
    {
        $this->settings(['crm_sync' => '1']);
        $slot = $this->openSlot();
        $phone = '0107'.random_int(1000000, 9999999);
        $this->postJson(route('advising.store'), ['name' => 'Smoke Unassigned', 'phone' => $phone, 'goal' => 'unsure', 'slot' => $slot])->assertOk();
        $booking = AdvisingBooking::where('phone', $phone)->firstOrFail();
        $this->assertNull(SalesLead::findOrFail($booking->sales_lead_id)->assigned_to);
        $this->assertNull($booking->sales_planned_activity_id);

        $this->settings([]); // crm_sync مش متعلّم = مقفول
        $phone2 = '0106'.random_int(1000000, 9999999);
        $this->postJson(route('advising.store'), ['name' => 'Smoke No Sync', 'phone' => $phone2, 'goal' => 'unsure', 'slot' => $slot])->assertOk();
        $this->assertNull(AdvisingBooking::where('phone', $phone2)->firstOrFail()->sales_lead_id);
    }
}
