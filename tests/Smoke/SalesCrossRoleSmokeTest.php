<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesInterestType;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

/**
 * كل حاجة بيعملها دور بتظهر عند الباقي (على الصفحات نفسها، مش في الداتابيز بس):
 * الموظف ← المدير والأدمن، المدير ← الموظف، الأدمن ← الموظف والمدير.
 */
class SalesCrossRoleSmokeTest extends SmokeTestCase
{
    private User $admin;

    private User $manager;

    private User $rep;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();
        $this->rep = User::where('employee_job_id', EmployeeJob::where('code', 'sales')->value('id'))->where('is_active', true)->firstOrFail();
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        DB::table('sales_team_members')->updateOrInsert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id], ['role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_rep_work_shows_for_manager_and_admin(): void
    {
        $lead = $this->repCreatesLead('Smoke Cross Rep Lead');
        $this->ok($this->actingAs($this->rep)->post(route('employee.sales.leads.activities.store', $lead), ['type' => 'call', 'outcome' => 'interested', 'body' => 'Smoke cross call note']));

        // المدير: في قايمة فريقه وفي ملف العميل بالنشاط
        $this->actingAs($this->manager)->get(route('employee.crm.leads.index', ['q' => 'Smoke Cross Rep Lead']))->assertOk()->assertSee('Smoke Cross Rep Lead');
        $this->actingAs($this->manager)->get(route('employee.crm.leads.show', $lead->id))->assertOk()->assertSee('Smoke cross call note');
        // الأدمن: نفس الشيء
        $this->actingAs($this->admin)->get(route('admin.crm.leads.index', ['q' => 'Smoke Cross Rep Lead']))->assertOk()->assertSee('Smoke Cross Rep Lead');
        $this->actingAs($this->admin)->get(route('admin.crm.leads.show', $lead->id))->assertOk()->assertSee('Smoke cross call note');

        // التقرير اليومي: عند المدير والأدمن
        $this->ok($this->actingAs($this->rep)->post(route('employee.sales.daily-reports.store'), [
            'action' => 'submit', 'report_date' => now()->toDateString(), 'messages_replied' => 3, 'leads_qualified' => 1, 'bookings_from_leads' => 0, 'numbers_worked' => 5,
            'followups_done' => 2, 'calls_made' => 4, 'meetings_held' => 0, 'calls_answered' => 2, 'activity_notes' => 'Smoke cross report note', 'productivity_notes' => 'smoke',
            'contacts' => array_map(fn ($i) => ['interaction_type' => 'call', 'sales_lead_id' => $lead->id, 'contact_name' => 'Smoke Cross '.$i, 'contact_phone' => $lead->phone, 'client_status' => 'interested', 'client_problems' => 'smoke '.$i], range(1, 4)),
        ]));
        $this->actingAs($this->manager)->get(route('employee.sales-manager.daily-reports.index', ['date' => now()->toDateString()]))->assertOk()->assertSee($this->rep->name);
        $this->actingAs($this->admin)->get(route('admin.sales.daily-reports.index', ['date' => now()->toDateString()]))->assertOk()->assertSee($this->rep->name);

        // الفوز: يظهر للأدمن للاعتماد، والعمولة بعد الاعتماد عند الموظف والمدير والأدمن
        $course = DB::table('advanced_courses')->where('is_active', true)->value('id');
        $this->ok($this->actingAs($this->rep)->post(route('employee.sales.leads.pipeline', $lead), ['stage' => 'qualification', 'notes' => 'smoke: qualified on the phone']));
        $this->ok($this->actingAs($this->rep)->post(route('employee.sales.leads.pipeline', $lead), [
            'stage' => SalesLead::WON_STAGE, 'expected_value' => 1300, 'course_type' => 'advanced', 'course_ref_id' => $course, 'notes' => 'smoke: enrolled and paid',
        ]));
        $this->actingAs($this->admin)->get(route('admin.crm.win-approvals.index'))->assertOk()->assertSee('Smoke Cross Rep Lead');
        $this->ok($this->actingAs($this->admin)->post(route('admin.sales.win-approvals.approve', $lead), ['commission_amount' => 137, 'commission_notes' => 'smoke', 'override_reason' => 'smoke: paid outside the platform']));
        $this->actingAs($this->rep)->get(route('employee.sales.commissions.index'))->assertOk()->assertSee('137');
        $this->actingAs($this->manager)->get(route('employee.sales-manager.commissions.show', $this->rep->id))->assertOk()->assertSee('137');
        $this->actingAs($this->admin)->get(route('admin.sales.commissions.show', $this->rep->id))->assertOk()->assertSee('137');
    }

    public function test_manager_and_admin_work_shows_for_the_rep(): void
    {
        // المدير بيضيف عميل لموظف في فريقه: يظهر عند الموظف ويوصله إشعار
        $notesBefore = DB::table('notifications')->where('user_id', $this->rep->id)->count();
        $phone = '0104'.random_int(1000000, 9999999);
        // فورم الـ CRM بتاع المدير لازم يروح لمسار المدير (مش مسار الموظف اللي بيرفضه)
        $this->actingAs($this->manager)->get(route('employee.crm.leads.create'))->assertOk()->assertSee(route('employee.sales-manager.leads.store'), false);
        $this->ok($this->actingAs($this->manager)->post(route('employee.sales-manager.leads.store'), $this->payload('Smoke Cross Manager Lead', $phone, ['assigned_to' => $this->rep->id, 'category_id' => $this->category()])));
        $lead = SalesLead::where('phone', $phone)->firstOrFail();
        $this->assertSame($this->rep->id, (int) $lead->assigned_to);
        // المدير بيعدّل العميل من نفس الفورم، والموظف بيشوف التعديل
        $this->actingAs($this->manager)->get(route('employee.crm.leads.edit', $lead->id))->assertOk()->assertSee(route('employee.sales-manager.leads.update', $lead), false);
        $this->ok($this->actingAs($this->manager)->put(route('employee.sales-manager.leads.update', $lead), $this->payload('Smoke Cross Manager Lead Edited', $phone, ['assigned_to' => $this->rep->id, 'category_id' => $this->category()])));
        $this->actingAs($this->rep)->get(route('employee.crm.leads.show', $lead->id))->assertOk()->assertSee('Smoke Cross Manager Lead Edited');
        // وما ينفعش يسند لموظف برّه فريقه
        $outsider = User::salesEmployees()->where('is_active', true)->whereNotIn('id', DB::table('sales_team_members')->select('user_id'))->first();
        if ($outsider) {
            $this->actingAs($this->manager)->post(route('employee.sales-manager.leads.store'), $this->payload('Smoke Cross Outsider', '0102'.random_int(1000000, 9999999), ['assigned_to' => $outsider->id, 'category_id' => $this->category()]))->assertSessionHasErrors('assigned_to');
            session()->forget('errors');
        }
        $this->actingAs($this->rep)->get(route('employee.crm.leads.index', ['q' => 'Smoke Cross Manager Lead']))->assertOk()->assertSee('Smoke Cross Manager Lead');
        $this->assertGreaterThan($notesBefore, DB::table('notifications')->where('user_id', $this->rep->id)->count(), 'the rep gets a notification for the new lead');

        // الأدمن بيضيف عميل من فورم الـ CRM (فيه التصنيف) لموظف: يظهر عند الموظف ومديره
        $this->actingAs($this->admin)->get(route('admin.crm.leads.create'))->assertOk()->assertSee('name="category_id"', false);
        $adminPhone = '0106'.random_int(1000000, 9999999);
        $this->ok($this->actingAs($this->admin)->post(route('admin.sales.leads.store'), $this->payload('Smoke Cross Admin Lead', $adminPhone, ['assigned_to' => $this->rep->id, 'category_id' => $this->category()])));
        $this->actingAs($this->rep)->get(route('employee.crm.leads.index', ['q' => 'Smoke Cross Admin Lead']))->assertOk()->assertSee('Smoke Cross Admin Lead');
        $this->actingAs($this->manager)->get(route('employee.crm.leads.index', ['q' => 'Smoke Cross Admin Lead']))->assertOk()->assertSee('Smoke Cross Admin Lead');

        // الأدمن بيفتح تذكرة للموظف: تظهر عنده، والموظف بيقفلها فتظهر مقفولة عند الأدمن
        $this->ok($this->actingAs($this->admin)->post(route('admin.crm.cases.store'), ['subject' => 'Smoke Cross Case', 'status' => 'new', 'priority' => 'high', 'sales_lead_id' => $lead->id, 'assigned_to' => $this->rep->id]));
        $case = \App\Domains\Sales\Models\CrmCase::where('subject', 'Smoke Cross Case')->latest('id')->firstOrFail();
        $this->actingAs($this->rep)->get(route('employee.crm.cases.index'))->assertOk()->assertSee('Smoke Cross Case');
        $this->ok($this->actingAs($this->rep)->put(route('employee.crm.cases.update', $case->id), ['subject' => 'Smoke Cross Case', 'status' => 'closed', 'priority' => 'high', 'sales_lead_id' => $lead->id, 'assigned_to' => $this->rep->id, 'resolution' => 'Smoke solved by rep']));
        $this->actingAs($this->admin)->get(route('admin.crm.cases.show', $case->id))->assertOk()->assertSee('Smoke solved by rep');

        // لوحة الكورسات من الإدارة: تظهر عند الموظف
        $this->ok($this->actingAs($this->admin)->post(route('admin.sales-course-board.store'), ['name' => 'Smoke Cross Board Course', 'price_online' => 2100, 'format' => 'أونلاين مباشر', 'instructor_name' => 'Smoke', 'is_active' => '1']));
        $this->actingAs($this->rep)->get(route('employee.sales.course-board.index'))->assertOk()->assertSee('Smoke Cross Board Course');
    }

    private function repCreatesLead(string $name): SalesLead
    {
        $phone = '0103'.random_int(1000000, 9999999);
        $this->ok($this->actingAs($this->rep)->post(route('employee.sales.leads.store'), $this->payload($name, $phone)));

        return SalesLead::where('phone', $phone)->firstOrFail();
    }

    private function payload(string $name, string $phone, array $extra = []): array
    {
        return $extra + [
            'name' => $name, 'phone' => $phone, 'source' => 'whatsapp', 'stage' => 'new_lead', 'priority' => 'normal',
            'interest_type_id' => SalesInterestType::query()->value('id'), 'follow_up_channel' => 'call',
            'next_follow_up_at' => now()->addDay()->format('Y-m-d H:i'),
        ];
    }

    private function category(): int
    {
        return (int) \App\Domains\Sales\Models\SalesLeadCategory::query()->active()->ordered()->value('id');
    }

    private function ok(TestResponse $response): void
    {
        $errors = session('errors');
        $this->assertLessThan(400, $response->getStatusCode(), $response->getStatusCode().' '.mb_substr($response->exception?->getMessage() ?? '', 0, 300));
        $this->assertTrue(! $errors || $errors->isEmpty(), 'validation: '.($errors ? json_encode($errors->getBag('default')->toArray(), JSON_UNESCAPED_UNICODE) : ''));
        session()->forget('errors');
    }
}
