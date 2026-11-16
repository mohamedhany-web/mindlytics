<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * مدير المبيعات شايف ومتحكم في فريقه بس: ولا صفحة بتعرض بيانات فريق تاني، وولا إجراء بيأثر على فريق تاني.
 */
class SalesManagerTeamIsolationSmokeTest extends SmokeTestCase
{
    private User $manager;

    private User $rep;

    private User $otherRep;

    private SalesLead $otherLead;

    private SalesLead $myLead;

    private string $mark;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $salesJob = EmployeeJob::where('code', 'sales')->firstOrFail();
        $managerJob = EmployeeJob::where('code', 'sales_manager')->firstOrFail();
        $this->manager = User::where('employee_job_id', $managerJob->id)->where('is_active', true)->firstOrFail();
        $this->rep = User::where('employee_job_id', $salesJob->id)->where('is_active', true)->firstOrFail();
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        DB::table('sales_team_members')->updateOrInsert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id], ['role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        // فريق تاني بمديره وموظفه
        $this->mark = 'ZZTEAM'.Str::upper(Str::random(5));
        $mk = fn (string $name, int $job) => User::forceCreate([
            'name' => $name, 'email' => 'smoke-'.Str::lower(Str::random(8)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $job,
        ]);
        $otherManager = $mk($this->mark.' Manager', $managerJob->id);
        $this->otherRep = $mk($this->mark.' Rep', $salesJob->id);
        $otherTeam = SalesTeam::create(['name' => $this->mark.' Team', 'manager_id' => $otherManager->id, 'created_by' => $otherManager->id, 'is_active' => true]);
        DB::table('sales_team_members')->insert(['sales_team_id' => $otherTeam->id, 'user_id' => $this->otherRep->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $lead = fn (User $owner, string $name) => SalesLead::create(['name' => $name, 'phone' => '0108'.random_int(1000000, 9999999), 'source' => 'call', 'stage' => 'offer_sent',
            'priority' => 'normal', 'assigned_to' => $owner->id, 'next_follow_up_at' => now()->addHour(), 'follow_up_channel' => 'call', 'expected_value' => 4321]);
        $this->otherLead = $lead($this->otherRep, $this->mark.' Lead');
        $this->myLead = $lead($this->rep, 'Smoke My Team Lead');
        \App\Domains\Sales\Models\SalesActivity::create(['sales_lead_id' => $this->otherLead->id, 'user_id' => $this->otherRep->id, 'type' => 'call', 'title' => $this->mark.' Call', 'body' => $this->mark]);
        $by = ['assigned_to' => $this->otherRep->id, 'created_by' => $this->otherRep->id];
        \App\Domains\Sales\Models\CrmAccount::create(['name' => $this->mark.' Account'] + $by);
        \App\Domains\Sales\Models\CrmCase::create(['subject' => $this->mark.' Case', 'status' => 'new', 'priority' => 'high', 'sales_lead_id' => $this->otherLead->id] + $by);
        \App\Domains\Sales\Models\SalesDailyReport::forceCreate(['user_id' => $this->otherRep->id, 'report_date' => now()->toDateString(), 'status' => 'submitted', 'activity_notes' => $this->mark.' report']);
    }

    public function test_manager_pages_never_show_another_team(): void
    {
        $this->actingAs($this->manager);
        $leaks = [];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || ! preg_match('#^employee/(sales-manager|crm)(/|$)#', $uri) || str_contains($uri, '{')
                || preg_match('#/(poll|count|templates|suggested-templates|excel|pdf|export)$#', $uri)) {
                continue;
            }
            foreach (['', '?quick=all', '?date='.now()->toDateString()] as $q) {
                $response = $this->get('/'.$uri.$q);
                if ($response->getStatusCode() === 200 && str_contains((string) $response->getContent(), $this->mark)) {
                    $leaks[$uri] = true;
                }
            }
        }
        $this->assertSame([], array_keys($leaks), "Another team's data shows for the manager on:\n".implode("\n", array_keys($leaks)));
    }

    public function test_manager_cannot_open_another_teams_records(): void
    {
        $this->actingAs($this->manager);
        $other = $this->otherRep->id;
        $report = \App\Domains\Sales\Models\SalesDailyReport::where('user_id', $other)->firstOrFail();
        $open = [];
        foreach ([
            "/employee/crm/leads/{$this->otherLead->id}", "/employee/crm/leads/{$this->otherLead->id}/edit", "/employee/crm/deals/{$this->otherLead->id}",
            "/employee/crm/opportunities/{$this->otherLead->id}", "/employee/sales-manager/team/{$other}", "/employee/sales-manager/team/{$other}/report",
            "/employee/sales-manager/scorecard/{$other}", "/employee/sales-manager/commissions/{$other}", "/employee/sales-manager/attendance/employees/{$other}",
            "/employee/sales-manager/shifts/{$other}", "/employee/sales-manager/daily-reports/{$report->id}",
            '/employee/crm/cases/'.\App\Domains\Sales\Models\CrmCase::where('subject', $this->mark.' Case')->value('id'),
            '/employee/crm/accounts/'.\App\Domains\Sales\Models\CrmAccount::where('name', $this->mark.' Account')->value('id'),
        ] as $url) {
            $status = $this->get($url)->getStatusCode();
            if ($status === 200) {
                $open[] = $url;
            }
        }
        $this->assertSame([], $open, "The manager can open another team's records:\n".implode("\n", $open));
    }

    public function test_manager_actions_cannot_touch_another_team(): void
    {
        $this->actingAs($this->manager);
        $other = $this->otherRep->id;
        $before = $this->otherLead->only(['assigned_to', 'stage', 'name']);

        $this->post(route('employee.sales-manager.leads.transfer', $this->otherLead), ['to_user_id' => $this->rep->id, 'reason' => 'smoke']);
        $this->post(route('employee.sales-manager.distribution.assign', $this->otherLead), ['to_user_id' => $this->rep->id]);
        $this->put(route('employee.sales-manager.leads.update', $this->otherLead), ['name' => 'Hacked', 'phone' => $this->otherLead->phone, 'source' => 'call', 'stage' => 'offer_sent', 'priority' => 'normal',
            'assigned_to' => $this->rep->id, 'category_id' => \App\Domains\Sales\Models\SalesLeadCategory::query()->value('id'), 'follow_up_channel' => 'call', 'next_follow_up_at' => now()->addDay()->format('Y-m-d H:i')]);
        $this->assertSame($before, $this->otherLead->fresh()->only(['assigned_to', 'stage', 'name']), 'another team lead changed');

        // ما يحوّلش عميل من فريقه لموظف برّه الفريق
        $this->post(route('employee.sales-manager.leads.transfer', $this->myLead), ['to_user_id' => $other, 'reason' => 'smoke']);
        $this->post(route('employee.sales-manager.distribution.assign', $this->myLead), ['to_user_id' => $other]);
        $this->assertSame($this->rep->id, (int) $this->myLead->fresh()->assigned_to, 'own lead moved outside the team');

        // ولا يقيّم أو يدّي إذن أو يحط أهداف أو ينقل بيانات موظف في فريق تاني
        $this->post(route('employee.sales-manager.scorecard.review', $other), ['date' => now()->toDateString(), 'status' => 'reviewed', 'recommendation' => 'none']);
        $this->post(route('employee.sales-manager.attendance.permissions.store'), ['employee_id' => $other, 'type' => 'day_absence', 'work_date' => now()->toDateString()]);
        $this->put(route('employee.sales-manager.kpi.targets.update'), ['user_id' => $other, 'year_month' => now()->format('Y-m')]);
        $this->post(route('employee.sales-manager.transfer.store'), ['from_user_id' => $other, 'to_user_id' => $this->rep->id, 'confirm' => '1']);
        $this->assertSame(0, DB::table('sales_manager_daily_reviews')->where('employee_id', $other)->count(), 'scorecard review for another team');
        $this->assertSame(0, DB::table('sales_attendance_permissions')->where('employee_id', $other)->count(), 'attendance permission for another team');
        $this->assertSame($other, (int) $this->otherLead->fresh()->assigned_to, 'bulk transfer moved another team lead');
    }

    public function test_manager_menu_reaches_every_manager_page(): void
    {
        $html = (string) $this->actingAs($this->manager)->get('/employee/crm')->assertOk()->getContent();
        foreach ([
            'employee.sales-manager.kpi.index', 'employee.sales-manager.kpi.targets', 'employee.sales-manager.scorecard.index', 'employee.sales-manager.daily-reports.index',
            'employee.sales-manager.team-reports.index', 'employee.sales-manager.commissions.index', 'employee.sales-manager.campaign-reports.index',
            'employee.sales-manager.distribution.index', 'employee.sales-manager.transfer.index', 'employee.sales-manager.live-board', 'employee.sales-manager.ops-board',
            'employee.sales-manager.org-chart.index', 'employee.sales-manager.attendance.index', 'employee.sales-manager.attendance.permissions.index',
            'employee.sales-manager.attendance.offline-day', 'employee.sales-manager.presence.index', 'employee.sales-manager.shifts.index',
            'employee.sales-manager.shift-swaps.index', 'employee.sales-manager.schedule-calendar.index', 'employee.sales-manager.whatsapp.inbox.index',
            'employee.sales-manager.whatsapp.queue.index', 'employee.sales.course-board.index', 'employee.sales.policy.index',
        ] as $name) {
            $this->assertStringContainsString(route($name), $html, "manager menu misses {$name}");
        }
        // مش من صلاحياته: اعتماد الفوز وصلاحيات CRM وأي صفحة أدمن
        preg_match('#<nav class="crm-nav".*?</nav>#s', $html, $nav);
        $this->assertStringNotContainsString(route('employee.crm.win-approvals.index'), $html);
        $this->assertStringNotContainsString(route('employee.crm.access.index'), $html);
        $this->assertStringNotContainsString('/admin/', $nav[0] ?? '');
        $this->assertStringContainsString('إدارة الفريق', $html);
    }
}
