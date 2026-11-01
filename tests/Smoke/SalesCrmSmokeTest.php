<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Wallet;
use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\MetaSocialPage;
use App\Domains\Messaging\Models\WhatsAppConversation;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * شاشات CRM الموحّدة (docs/SALES_CRM.md) على نسخة MySQL، كل شيء يُتراجع عنه:
 * كل صفحة لكل دور، نطاق البيانات (الموظف عملاؤه، المدير فريقه، الأدمن الكل)،
 * روابط الشريط العلوي لا تؤدي لـ 403/404/500، والإجراءات (تحويل جماعي، تصدير، تحويل لطالب).
 */
class SalesCrmSmokeTest extends SmokeTestCase
{
    private User $admin;

    private User $rep;

    private User $outsider;

    private User $manager;

    private SalesLead $repLead;

    private SalesLead $outsiderLead;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $salesJob = EmployeeJob::where('code', 'sales')->firstOrFail();
        $this->rep = User::where('employee_job_id', $salesJob->id)->where('is_active', true)->firstOrFail();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();

        // الموظف في فريق المدير؛ موظف آخر خارج الفريق
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        if (! DB::table('sales_team_members')->where('sales_team_id', $team->id)->where('user_id', $this->rep->id)->exists()) {
            DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->outsider = User::forceCreate([
            'name' => 'Smoke Outsider Rep', 'email' => 'smoke-out-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $salesJob->id,
        ]);

        $this->repLead = $this->lead($this->rep, 'Smoke CRM Rep Lead', ['stage' => 'offer_sent', 'offer_price' => 2500, 'expected_value' => 2500]);
        $this->outsiderLead = $this->lead($this->outsider, 'Smoke CRM Outsider Lead');
    }

    public function test_rep_sees_every_crm_page_and_only_own_leads(): void
    {
        $this->actingAs($this->rep);
        $home = $this->page('/employee/crm');
        $home->assertSee('يومي')->assertSee('هدفي النهارده');

        foreach (['all', 'today', 'overdue', 'new', 'stale', 'meta'] as $quick) {
            $this->page('/employee/crm/leads?quick='.$quick);
        }
        $this->page('/employee/crm/leads?q=Smoke%20CRM&sort=name&dir=desc')->assertSee('Smoke CRM Rep Lead')->assertDontSee('Smoke CRM Outsider Lead');
        foreach (['open', 'won_pending', 'won_month', 'refunded', 'all'] as $quick) {
            $this->page('/employee/crm/deals?quick='.$quick);
        }

        $this->page('/employee/crm/leads/'.$this->repLead->id)->assertSee('Smoke CRM Rep Lead')->assertSee('تسجيل سريع');
        $this->page('/employee/crm/deals/'.$this->repLead->id)->assertSee('المدفوعات');

        $this->get('/employee/crm/leads/'.$this->outsiderLead->id)->assertNotFound();
        $this->get('/employee/crm/deals/'.$this->outsiderLead->id)->assertNotFound();
        $this->get('/employee/crm/leads/'.$this->repLead->id.'/convert')->assertForbidden();
        $this->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$this->repLead->id], 'to_user_id' => $this->outsider->id])->assertForbidden();

        $this->assertNavLinksWork($home);
    }

    public function test_rep_quick_actions_from_the_record_return_to_crm(): void
    {
        $this->actingAs($this->rep);
        $url = '/employee/crm/leads/'.$this->repLead->id;
        $page = $this->page($url)->getContent();
        $this->assertStringContainsString('name="redirect_to" value="'.url($url).'"', $page, 'CRM forms must bring the rep back to the CRM record');
        $this->post('/employee/sales/leads/'.$this->repLead->id.'/quick-activity', ['type' => 'call', 'outcome' => 'interested', 'redirect_to' => url($url)])
            ->assertRedirect(url($url));
        $this->post('/employee/sales/leads/'.$this->repLead->id.'/next-follow', [
            'next_follow_up_at' => now()->addDays(2)->format('Y-m-d H:i'), 'follow_up_channel' => 'whatsapp', 'note' => 'smoke', 'redirect_to' => url($url),
        ])->assertRedirect(url($url));
        $this->page($url)->assertSee('مكالمة');
    }

    public function test_manager_sees_team_only_and_can_transfer_and_convert(): void
    {
        $this->actingAs($this->manager);
        $home = $this->page('/employee/crm');
        $home->assertSee('الفريق النهارده')->assertSee('مستني قرارك');

        $this->page('/employee/crm/leads?quick=all')->assertSee('Smoke CRM Rep Lead')->assertDontSee('Smoke CRM Outsider Lead');
        $this->page('/employee/crm/leads/'.$this->repLead->id)->assertSee('تحويل لموظف آخر');
        $this->get('/employee/crm/leads/'.$this->outsiderLead->id)->assertNotFound();
        $this->page('/employee/crm/deals?quick=all');

        // تحويل جماعي داخل الفريق
        $rep2 = User::forceCreate([
            'name' => 'Smoke Team Rep 2', 'email' => 'smoke-t2-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2015'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $this->rep->employee_job_id,
        ]);
        DB::table('sales_team_members')->insert(['sales_team_id' => SalesTeam::where('manager_id', $this->manager->id)->value('id'), 'user_id' => $rep2->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        $extra = $this->lead($this->rep, 'Smoke CRM Bulk Lead');
        $this->from('/employee/crm/leads')->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$extra->id, $this->outsiderLead->id], 'to_user_id' => $rep2->id, 'reason' => 'smoke'])
            ->assertRedirect('/employee/crm/leads')->assertSessionHasNoErrors();
        $this->assertSame($rep2->id, (int) $extra->fresh()->assigned_to);
        $this->assertSame($this->outsider->id, (int) $this->outsiderLead->fresh()->assigned_to, 'a lead outside the team must not move');
        $this->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$extra->id], 'to_user_id' => $this->outsider->id])->assertSessionHasErrors('to_user_id');

        // تحويل لطالب: طلب معتمد لطالب بنفس الرقم بصيغة مختلفة
        $digits = (string) random_int(10000000, 99999999);
        $lead = $this->lead($this->rep, 'Smoke CRM Convert Lead', ['phone' => '010'.$digits, 'stage' => 'interested']);
        $order = $this->approvedOrderFor('+2010'.$digits, 900, autoLink: false, lead: $lead);
        $this->actingAs($this->manager);
        $this->page('/employee/crm/leads/'.$lead->id.'/convert')->assertSee('#'.$order->id);
        $this->post('/employee/crm/leads/'.$lead->id.'/convert', ['order_id' => $order->id])
            ->assertRedirect('/employee/crm/deals/'.$lead->id);
        $lead->refresh();
        $this->assertSame($order->id, (int) $lead->order_id);
        $this->assertSame(SalesLead::WON_STAGE, $lead->stage);
        $this->page('/employee/crm/deals/'.$lead->id)->assertSee('#'.$order->id)->assertSee('900.00');

        $this->assertNavLinksWork($home);
    }

    public function test_admin_sees_everything_and_can_export(): void
    {
        $this->actingAs($this->admin);
        $home = $this->page('/admin/crm');
        $home->assertSee('لوحة المبيعات');
        foreach (['all', 'today', 'overdue', 'new', 'stale', 'meta', 'unassigned'] as $quick) {
            $this->page('/admin/crm/leads?quick='.$quick);
        }
        $this->page('/admin/crm/leads?q=Smoke%20CRM')->assertSee('Smoke CRM Rep Lead')->assertSee('Smoke CRM Outsider Lead');
        $this->page('/admin/crm/leads/'.$this->outsiderLead->id)->assertSee('فتح في الواجهة الكلاسيكية');
        $this->page('/admin/crm/deals?quick=all');
        $this->page('/admin/crm/deals/'.$this->repLead->id);
        $this->page('/admin/crm/leads/'.$this->repLead->id.'/convert');

        $csv = $this->post('/admin/crm/leads/bulk', ['action' => 'export', 'ids' => [$this->repLead->id, $this->outsiderLead->id]]);
        $csv->assertOk();
        $this->assertStringContainsString('Smoke CRM Outsider Lead', $csv->streamedContent());

        // الموظف والمدير لا يدخلون مسار الأدمن
        $this->assertNotSame(200, $this->actingAs($this->rep)->get('/admin/crm')->getStatusCode());

        $this->actingAs($this->admin);
        $this->assertNavLinksWork($home);
    }

    public function test_rep_schedules_and_completes_a_call_and_sees_it_everywhere(): void
    {
        $this->actingAs($this->rep);
        $lead = $this->lead($this->rep, 'Smoke CRM Plan Lead', ['next_follow_up_at' => null]);
        $when = now()->addDay()->setTime(11, 30);

        $this->page('/employee/crm/activities/create?lead='.$lead->id)->assertSee('Smoke CRM Plan Lead');
        $this->post('/employee/crm/activities', [
            'kind' => 'call', 'title' => 'Smoke planned call', 'sales_lead_id' => $lead->id, 'assigned_to' => $this->rep->id,
            'starts_at' => $when->format('Y-m-d\TH:i'), 'duration_minutes' => 10, 'priority' => 'high',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $planned = SalesPlannedActivity::where('title', 'Smoke planned call')->firstOrFail();
        $this->assertSame($this->rep->id, (int) $planned->created_by);
        $this->assertSame($when->format('Y-m-d H:i'), $lead->fresh()->next_follow_up_at?->format('Y-m-d H:i'), 'scheduling pulls the lead follow-up forward');

        foreach (['open', 'today', 'overdue', 'week', 'done', 'all'] as $quick) {
            $this->page('/employee/crm/activities?quick='.$quick);
        }
        $this->page('/employee/crm/activities?quick=open&q=Smoke%20planned')->assertSee('Smoke planned call');
        $this->page('/employee/crm/leads/'.$lead->id)->assertSee('Smoke planned call');
        $this->page('/employee/crm')->assertSee('Smoke planned call');
        foreach (['week', 'month', 'day', 'shared'] as $view) {
            $this->page('/employee/crm/calendar?view='.$view.'&date='.$when->toDateString())->assertSee('Smoke planned call');
        }
        $this->page('/employee/crm/activities/'.$planned->id)->assertSee('تسجيل النتيجة');
        $this->page('/employee/crm/activities/'.$planned->id.'/edit');

        // التنفيذ: يتسجّل في سجل العميل (KPIs) ويحرّك المرحلة ويشيل المتابعة
        $this->post('/employee/crm/activities/'.$planned->id.'/complete', ['status' => 'done'])->assertSessionHasErrors('outcome');
        $this->post('/employee/crm/activities/'.$planned->id.'/complete', ['status' => 'done', 'outcome' => 'interested', 'result_notes' => 'smoke ok', 'apply_stage' => 1])
            ->assertSessionHasNoErrors()->assertRedirect();
        $planned->refresh();
        $this->assertSame('done', $planned->status);
        $this->assertNotNull($planned->sales_activity_id);
        $this->assertTrue(SalesActivity::whereKey($planned->sales_activity_id)->where('sales_lead_id', $lead->id)->where('type', 'call')->where('user_id', $this->rep->id)->exists());
        $this->assertSame('interested', SalesActivity::find($planned->sales_activity_id)->outcome);
        $this->assertNotSame('new_lead', $lead->fresh()->stage, 'call outcome moves the stage like the quick log');
        $this->post('/employee/crm/activities/'.$planned->id.'/complete', ['status' => 'done', 'outcome' => 'interested'])->assertForbidden();

        // خارج النطاق
        $foreign = SalesPlannedActivity::create(['kind' => 'call', 'title' => 'Smoke foreign call', 'sales_lead_id' => $this->outsiderLead->id, 'assigned_to' => $this->outsider->id, 'starts_at' => $when]);
        $this->get('/employee/crm/activities/'.$foreign->id)->assertNotFound();
        $this->page('/employee/crm/activities?quick=all&q=Smoke%20foreign')->assertDontSee('Smoke foreign call');
        $this->post('/employee/crm/activities', ['kind' => 'call', 'title' => 'x', 'sales_lead_id' => $this->outsiderLead->id, 'assigned_to' => $this->rep->id, 'starts_at' => $when->format('Y-m-d\TH:i')])
            ->assertSessionHasErrors('sales_lead_id');
        $this->post('/employee/crm/activities', ['kind' => 'call', 'title' => 'x', 'sales_lead_id' => $lead->id, 'assigned_to' => $this->outsider->id, 'starts_at' => $when->format('Y-m-d\TH:i')])
            ->assertSessionHasErrors('assigned_to');
    }

    public function test_students_module_is_scoped_by_role(): void
    {
        $mine = User::forceCreate(['name' => 'Smoke CRM Student Mine', 'email' => 'smoke-sm-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2010'.random_int(10000000, 99999999), 'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true]);
        $theirs = User::forceCreate(['name' => 'Smoke CRM Student Theirs', 'email' => 'smoke-st-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2010'.random_int(10000000, 99999999), 'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true]);
        SalesLead::whereKey($this->repLead->id)->update(['student_user_id' => $mine->id]);
        SalesLead::whereKey($this->outsiderLead->id)->update(['student_user_id' => $theirs->id]);

        $this->actingAs($this->rep);
        $this->page('/employee/crm/students?q=Smoke%20CRM%20Student')->assertSee('Smoke CRM Student Mine')->assertDontSee('Smoke CRM Student Theirs');
        $this->page('/employee/crm/students/'.$mine->id)->assertSee('Smoke CRM Rep Lead')->assertSee('الكورسات');
        $this->get('/employee/crm/students/'.$theirs->id)->assertNotFound();
        $this->get('/employee/crm/students/'.$this->rep->id)->assertNotFound();

        // جدولة مهمة على الطالب
        $this->post('/employee/crm/activities', ['kind' => 'task', 'title' => 'Smoke student task', 'student_user_id' => $mine->id, 'assigned_to' => $this->rep->id, 'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i')])
            ->assertSessionHasNoErrors();
        $this->page('/employee/crm/students/'.$mine->id)->assertSee('Smoke student task');
        $this->post('/employee/crm/activities', ['kind' => 'task', 'title' => 'x', 'student_user_id' => $theirs->id, 'assigned_to' => $this->rep->id, 'starts_at' => now()->addDays(2)->format('Y-m-d\TH:i')])
            ->assertSessionHasErrors('student_user_id');

        $this->actingAs($this->manager);
        $this->page('/employee/crm/students?q=Smoke%20CRM%20Student')->assertSee('Smoke CRM Student Mine')->assertDontSee('Smoke CRM Student Theirs');

        $this->actingAs($this->admin);
        $this->page('/admin/crm/students?q=Smoke%20CRM%20Student')->assertSee('Smoke CRM Student Mine')->assertSee('Smoke CRM Student Theirs');
        $this->page('/admin/crm/students/'.$theirs->id)->assertSee('Smoke CRM Outsider Lead');
    }

    public function test_manager_assigns_to_team_and_admin_sees_all_activities(): void
    {
        $when = now()->addDays(3)->setTime(13, 0);
        $this->actingAs($this->manager);
        $this->post('/employee/crm/activities', ['kind' => 'meeting', 'title' => 'Smoke team meeting', 'sales_lead_id' => $this->repLead->id, 'assigned_to' => $this->rep->id, 'starts_at' => $when->format('Y-m-d\TH:i'), 'duration_minutes' => 30])
            ->assertSessionHasNoErrors();
        $meeting = SalesPlannedActivity::where('title', 'Smoke team meeting')->firstOrFail();
        $this->post('/employee/crm/activities', ['kind' => 'call', 'title' => 'x', 'sales_lead_id' => $this->repLead->id, 'assigned_to' => $this->outsider->id, 'starts_at' => $when->format('Y-m-d\TH:i')])
            ->assertSessionHasErrors('assigned_to');
        $this->get('/employee/crm/activities/create?lead='.$this->outsiderLead->id)->assertOk()->assertDontSee('Smoke CRM Outsider Lead');

        $this->page('/employee/crm/calendar?view=shared&date='.$when->toDateString())->assertSee('Smoke team meeting')->assertSee($this->rep->name);
        $this->page('/employee/crm/calendar?view=week&user='.$this->rep->id.'&date='.$when->toDateString())->assertSee('Smoke team meeting');
        $this->post('/employee/crm/activities/'.$meeting->id.'/complete', ['status' => 'done'])->assertForbidden();
        $this->put('/employee/crm/activities/'.$meeting->id, ['title' => 'Smoke team meeting moved', 'assigned_to' => $this->rep->id, 'starts_at' => $when->copy()->addHour()->format('Y-m-d\TH:i')])
            ->assertSessionHasNoErrors();
        $this->assertSame($when->copy()->addHour()->format('Y-m-d H:i'), $meeting->fresh()->starts_at->format('Y-m-d H:i'));

        $this->actingAs($this->rep);
        $this->page('/employee/crm/activities?quick=all&q=Smoke%20team')->assertSee('Smoke team meeting moved');

        $this->actingAs($this->admin);
        foreach (['open', 'today', 'overdue', 'week', 'done', 'all'] as $quick) {
            $this->page('/admin/crm/activities?quick='.$quick);
        }
        $this->page('/admin/crm/activities?quick=all&q=Smoke%20team')->assertSee('Smoke team meeting moved');
        foreach (['week', 'month', 'day', 'shared'] as $view) {
            $this->page('/admin/crm/calendar?view='.$view.'&date='.$when->toDateString());
        }
        $this->page('/admin/crm/activities/'.$meeting->id)->assertSee('تسجيل النتيجة');
        $this->post('/admin/crm/activities/'.$meeting->id.'/complete', ['status' => 'missed', 'result_notes' => 'no show'])->assertSessionHasNoErrors();
        $this->assertSame('missed', $meeting->fresh()->status);
        $this->assertNull($meeting->fresh()->sales_activity_id, 'a missed meeting is not logged as contact');

        $task = SalesPlannedActivity::create(['kind' => 'task', 'title' => 'Smoke cancel me', 'assigned_to' => $this->rep->id, 'created_by' => $this->manager->id, 'starts_at' => $when]);
        $this->actingAs($this->manager)->post('/employee/crm/activities/'.$task->id.'/cancel')->assertRedirect();
        $this->assertSame('cancelled', $task->fresh()->status);
    }

    public function test_reports_are_scoped_correct_and_exportable(): void
    {
        // النسخة فيها نشاط حقيقي النهارده، فبنقارن قبل وبعد: مكالمتين (واحدة اترد عليها)، اجتماع، وصفقة فازت؛ ونشاط للموظف اللي برة الفريق
        $base = $this->actingAs($this->rep)->reportRow('/employee/crm/reports/performance?range=today', $this->rep->name)
            ?? ['calls' => 0, 'answered' => 0, 'meetings' => 0, 'won' => 0, 'won_value' => 0];
        $won = $this->lead($this->rep, 'Smoke CRM Report Won', ['stage' => SalesLead::WON_STAGE, 'expected_value' => 1500, 'closed_at' => now(), 'source' => 'referral']);
        foreach ([['call', 'interested'], ['call', 'no_answer'], ['meeting', null]] as [$type, $outcome]) {
            SalesActivity::create(['sales_lead_id' => $won->id, 'user_id' => $this->rep->id, 'type' => $type, 'outcome' => $outcome, 'title' => 'smoke']);
        }
        SalesActivity::create(['sales_lead_id' => $this->outsiderLead->id, 'user_id' => $this->outsider->id, 'type' => 'call', 'outcome' => 'interested', 'title' => 'smoke']);
        $lost = $this->lead($this->rep, 'Smoke CRM Report Lost', ['stage' => 'lost', 'lost_reason' => 'price', 'closed_at' => now()]);

        $this->actingAs($this->rep);
        $this->page('/employee/crm/reports')->assertSee('أداء الموظفين')->assertSee('KPIs والأداء');
        foreach (array_keys(\App\Domains\Sales\Support\CrmReports::REPORTS) as $key) {
            foreach (['today', 'week', 'month', 'last_month', '30d'] as $range) {
                $this->page('/employee/crm/reports/'.$key.'?range='.$range);
            }
        }
        $this->page('/employee/crm/reports/performance?range=custom&from='.now()->subDays(3)->toDateString().'&to='.now()->toDateString());
        $this->get('/employee/crm/reports/nope')->assertNotFound();

        $row = $this->reportRow('/employee/crm/reports/performance?range=today', $this->rep->name);
        $this->assertSame($base['calls'] + 2, $row['calls']);
        $this->assertSame($base['answered'] + 1, $row['answered']);
        $this->assertSame($base['meetings'] + 1, $row['meetings']);
        $this->assertSame($base['won'] + 1, $row['won']);
        $this->assertEqualsWithDelta((float) $base['won_value'] + 1500, (float) $row['won_value'], 0.01);
        $this->assertNull($this->reportRow('/employee/crm/reports/performance?range=today', $this->outsider->name), 'a rep only sees their own row');
        $this->assertNotNull($this->reportRow('/employee/crm/reports/lost?range=today', 'السعر'));
        $this->assertNotNull($this->reportRow('/employee/crm/reports/sources?range=today', 'إحالة'));
        $this->assertNotNull($this->reportRow('/employee/crm/reports/deals?range=today', $this->rep->name));
        // الموظف ما يقدرش يفلتر بموظف تاني
        $this->assertNull($this->reportRow('/employee/crm/reports/performance?range=today&user='.$this->outsider->id, $this->outsider->name));

        $this->actingAs($this->manager);
        $this->assertNotNull($this->reportRow('/employee/crm/reports/performance?range=today', $this->rep->name));
        $this->assertNull($this->reportRow('/employee/crm/reports/performance?range=today', $this->outsider->name), 'the outsider is not in the team');
        $this->assertNull($this->reportRow('/employee/crm/reports/performance?range=today&user='.$this->outsider->id, $this->outsider->name));

        $this->actingAs($this->admin);
        foreach (array_keys(\App\Domains\Sales\Support\CrmReports::REPORTS) as $key) {
            $this->page('/admin/crm/reports/'.$key.'?range=month');
        }
        $this->assertSame(1, $this->reportRow('/admin/crm/reports/performance?range=today&user='.$this->outsider->id, $this->outsider->name)['calls'], 'the new outsider rep has exactly the one call');
        $csv = $this->get('/admin/crm/reports/performance?range=today&export=csv');
        $csv->assertOk();
        $this->assertStringContainsString($this->outsider->name, $csv->streamedContent());
        $this->assertNotNull($lost->id);
    }

    public function test_unified_inbox_is_scoped_per_role(): void
    {
        $wa = fn (string $name, ?User $assignee, ?SalesLead $lead, int $unread = 0) => WhatsAppConversation::forceCreate([
            'phone_number' => '2010'.random_int(10000000, 99999999), 'contact_name' => $name, 'assigned_to' => $assignee?->id, 'sales_lead_id' => $lead?->id,
            'status' => WhatsAppConversation::STATUS_OPEN, 'last_message_at' => now(), 'last_message_preview' => 'hello '.$name, 'last_message_direction' => 'inbound', 'unread_count' => $unread,
        ]);
        $wa('Smoke WA Mine', null, $this->repLead, 2);
        $wa('Smoke WA Assigned', $this->rep, null);
        $wa('Smoke WA Outsider', $this->outsider, $this->outsiderLead);
        $wa('Smoke WA Queue', null, null);
        $page = MetaSocialPage::forceCreate(['page_id' => 'smoke-'.Str::random(8), 'page_name' => 'Smoke Page', 'is_active' => true]);
        MetaSocialConversation::forceCreate(['meta_social_page_id' => $page->id, 'platform' => 'instagram', 'participant_id' => 'smoke-'.Str::random(8),
            'participant_name' => 'Smoke IG Mine', 'assigned_to' => $this->rep->id, 'status' => 'open', 'last_message_at' => now()->subMinute(), 'last_message_preview' => 'ig hi', 'unread_count' => 1]);

        $this->actingAs($this->rep);
        $inbox = $this->page('/employee/crm/inbox?q=Smoke');
        $inbox->assertSee('Smoke WA Mine')->assertSee('Smoke WA Assigned')->assertSee('Smoke IG Mine')->assertSee('Smoke CRM Rep Lead')
            ->assertDontSee('Smoke WA Outsider')->assertDontSee('Smoke WA Queue');
        $this->page('/employee/crm/inbox?q=Smoke&quick=unread')->assertSee('Smoke WA Mine')->assertDontSee('Smoke WA Assigned');
        $this->page('/employee/crm/inbox?q=Smoke&channel=instagram')->assertSee('Smoke IG Mine')->assertDontSee('Smoke WA Mine');
        $this->page('/employee/crm/inbox?q=Smoke&quick=unassigned')->assertDontSee('Smoke WA Queue');
        foreach (['all', 'unread', 'open', 'no_lead'] as $quick) {
            $this->page('/employee/crm/inbox?quick='.$quick);
        }
        preg_match('/href="([^"]+whatsapp\/inbox\/\d+)"/', $inbox->getContent(), $m);
        $this->assertNotEmpty($m, 'each WhatsApp row opens the existing thread');
        $this->assertNotContains($this->get(parse_url(html_entity_decode($m[1]), PHP_URL_PATH))->getStatusCode(), [403, 404, 500]);

        $this->actingAs($this->manager);
        $this->page('/employee/crm/inbox?q=Smoke')->assertSee('Smoke WA Mine')->assertDontSee('Smoke WA Outsider');
        $this->page('/employee/crm/inbox?q=Smoke&quick=unassigned')->assertSee('Smoke WA Queue')->assertDontSee('Smoke WA Assigned');

        $this->actingAs($this->admin);
        $this->page('/admin/crm/inbox?q=Smoke')->assertSee('Smoke WA Outsider')->assertSee('Smoke WA Queue')->assertSee('Smoke IG Mine');
        $this->page('/admin/crm/inbox?q=Smoke&assigned_to='.$this->outsider->id)->assertSee('Smoke WA Outsider')->assertDontSee('Smoke WA Mine');
    }

    /** يفتح كل روابط الشريط العلوي (قوائم الوحدات و«+») للدور الحالي: ممنوع 403 أو 404 أو 500. */
    private function assertNavLinksWork(TestResponse $page): void
    {
        preg_match_all('/<div class="crm-menu"[^>]*>(.*?)<\/div>/s', $page->getContent(), $menus);
        $urls = [];
        foreach ($menus[1] as $menu) {
            preg_match_all('/href="([^"]+)"/', $menu, $m);
            $urls = array_merge($urls, $m[1]);
        }
        $urls = array_values(array_unique(array_filter($urls, fn ($u) => str_starts_with($u, 'http'))));
        $this->assertNotEmpty($urls, 'the CRM top bar must render its module menus');

        $bad = [];
        foreach ($urls as $url) {
            $path = parse_url($url, PHP_URL_PATH).(($q = parse_url($url, PHP_URL_QUERY)) ? '?'.$q : '');
            $status = $this->get($path)->getStatusCode();
            if (in_array($status, [403, 404], true) || $status >= 500) {
                $bad[] = "{$status} {$path}";
            }
        }
        $this->assertSame([], $bad, "Broken CRM menu links:\n".implode("\n", $bad));
    }

    /** صف من جدول التقرير أول خانة فيه = $first (أو null). صفوف «أداء الموظفين» بترجع بأسماء الأعمدة. */
    private function reportRow(string $url, string $first): ?array
    {
        $html = $this->page($url)->getContent();
        preg_match('/<tbody>(.*?)<\/tbody>/s', $html, $body);
        preg_match_all('/<tr>(.*?)<\/tr>/s', $body[1] ?? '', $rows);
        foreach ($rows[1] as $tr) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $tr, $cells);
            $cells = array_map(fn ($c) => trim(html_entity_decode(strip_tags($c))), $cells[1]);
            if (($cells[0] ?? null) !== $first) {
                continue;
            }
            $keys = ['name', 'new', 'worked', 'calls', 'answered', 'answer_rate', 'qualified', 'meetings', 'won', 'won_value', 'conversion'];
            $values = array_map(fn ($v) => is_numeric(str_replace(',', '', $v)) ? (str_contains($v, '.') ? (float) str_replace(',', '', $v) : (int) str_replace(',', '', $v)) : $v, $cells);

            return count($values) === count($keys) ? array_combine($keys, $values) : $values;
        }

        return null;
    }

    private function page(string $url): TestResponse
    {
        $response = $this->get($url);
        $e = $response->exception;
        $this->assertSame(200, $response->getStatusCode(), $url.' → '.$response->getStatusCode().($e ? ' '.get_class($e).': '.mb_substr($e->getMessage(), 0, 300).' @ '.str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine() : ''));

        return $response;
    }

    /** @param  array<string, mixed>  $attrs */
    private function lead(User $owner, string $name, array $attrs = []): SalesLead
    {
        $lead = new SalesLead;
        $lead->forceFill(array_merge([
            'name' => $name, 'phone' => '011'.random_int(10000000, 99999999), 'source' => 'social', 'stage' => 'new_lead', 'priority' => 'normal',
            'assigned_to' => $owner->id, 'created_by' => $owner->id, 'last_contacted_at' => now(), 'next_follow_up_at' => now()->subHour(), 'follow_up_channel' => 'call',
            'interest_type_id' => DB::table('sales_interest_types')->value('id'), 'category_id' => DB::table('sales_lead_categories')->value('id'),
        ], $attrs))->save();

        return $lead;
    }

    private function approvedOrderFor(string $phone, float $amount, bool $autoLink, SalesLead $lead): Order
    {
        $student = User::forceCreate([
            'name' => 'Smoke Convert Student', 'email' => 'smoke-cs-'.Str::lower(Str::random(6)).'@example.test', 'phone' => $phone,
            'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true,
        ]);
        // نحاكي حالة «رقم مختلف»: نخلي الـ lead خارج نافذة الربط التلقائي قبل الاعتماد
        if (! $autoLink) {
            SalesLead::whereKey($lead->id)->update(['updated_at' => now()->subDays(200), 'last_contacted_at' => now()->subDays(200)]);
        }
        $wallet = Wallet::create(['name' => 'Smoke Wallet '.Str::random(4), 'type' => 'vodafone_cash', 'balance' => 0, 'is_active' => true]);
        $order = Order::forceCreate([
            'user_id' => $student->id, 'advanced_course_id' => AdvancedCourse::where('is_active', true)->value('id'), 'original_amount' => $amount,
            'discount_amount' => 0, 'amount' => $amount, 'payment_method' => 'bank_transfer', 'wallet_id' => $wallet->id, 'status' => Order::STATUS_PENDING,
        ]);
        $this->actingAs($this->admin)->postJson("/admin/orders/{$order->id}/approve")->assertOk();
        $this->assertNull($lead->fresh()->order_id, 'precondition: not auto-linked');

        return $order->fresh();
    }
}
