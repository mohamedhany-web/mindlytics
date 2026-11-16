<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Domains\Sales\Services\SalesLeadHealthService;
use App\Domains\Sales\Support\CrmScope;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

/** «حالة العملاء»: كل مشكلة بتتحسب صح، والرقم في اللوحة يطابق القايمة، والمدير يشوف فريقه بس (docs/SALES_CRM.md). */
class SalesLeadHealthSmokeTest extends SmokeTestCase
{
    private User $rep;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();
        $job = EmployeeJob::where('code', 'sales')->value('id');
        $role = User::where('employee_job_id', $job)->value('role');
        $this->rep = User::forceCreate(['name' => 'Smoke Health Rep', 'email' => 'smoke-lh-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2011'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $job]);
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_buckets_are_counted_right_and_match_the_list(): void
    {
        $healthy = $this->lead('Smoke LH Healthy', ['next_follow_up_at' => now()->addDay(), 'last_contacted_at' => now()->subDay()]);
        $untouched = $this->lead('Smoke LH Untouched', ['next_follow_up_at' => now()->addDay()]);
        $overdue = $this->lead('Smoke LH Overdue', ['next_follow_up_at' => now()->subHours(3), 'last_contacted_at' => now()->subDays(2)]);
        $noNext = $this->lead('Smoke LH NoNext', ['next_follow_up_at' => null, 'last_contacted_at' => now()->subDay()]);
        $stale = $this->lead('Smoke LH Stale', ['next_follow_up_at' => now()->addDay(), 'last_contacted_at' => now()->subDays(20)], now()->subDays(30));
        // اتعدّل امبارح بس من غير تواصل: لسه راكد (القديم كان بيعدّه سليم)
        $editedOnly = $this->lead('Smoke LH EditedOnly', ['next_follow_up_at' => now()->addDay(), 'last_contacted_at' => now()->subDays(15)], now()->subDays(30));
        $pending = $this->lead('Smoke LH Pending', ['stage' => SalesLead::WON_STAGE, 'last_contacted_at' => now()]);
        // ملاحظة ونشاط مخطط ما يتحسبوش تواصل
        SalesActivity::create(['sales_lead_id' => $untouched->id, 'user_id' => $this->rep->id, 'type' => 'note', 'title' => 'x']);
        SalesActivity::create(['sales_lead_id' => $untouched->id, 'user_id' => $this->rep->id, 'type' => 'call', 'status' => 'planned', 'title' => 'x']);
        // مكالمة حقيقية من 3 أيام تطلّع العميل من الراكد حتى لو last_contacted_at قديم
        $revived = $this->lead('Smoke LH Revived', ['next_follow_up_at' => now()->addDay(), 'last_contacted_at' => now()->subDays(20)], now()->subDays(30));
        SalesActivity::create(['sales_lead_id' => $revived->id, 'user_id' => $this->rep->id, 'type' => 'call', 'status' => 'held', 'title' => 'x']);

        $scope = CrmScope::for($this->manager);
        $board = app(SalesLeadHealthService::class)->board($scope);
        $row = collect($board['rows'])->firstWhere('id', $this->rep->id);
        $this->assertNotNull($row, 'the rep row is on the manager board');
        $this->assertSame(7, $row['open']);
        $this->assertSame(1, $row['untouched']);
        $this->assertSame(1, $row['overdue']);
        $this->assertSame(1, $row['no_next']);
        $this->assertSame(2, $row['stale']);
        $this->assertSame(1, $row['pending_win']);
        $this->assertSame(1, $row['contacts_today']);
        $this->assertSame((int) round(100 * 2 / 7), $row['healthy_pct'], 'healthy = Healthy + Revived');

        $this->actingAs($this->manager);
        $this->get(route('employee.crm.leads.health'))->assertOk()->assertSee('حالة العملاء')->assertSee('Smoke Health Rep')
            ->assertSee(e(route('employee.crm.leads.index', ['quick' => 'stale', 'assigned_to' => $this->rep->id])), false);

        // الرقم بيطابق القايمة
        $expect = ['untouched' => [$untouched], 'overdue' => [$overdue], 'no_next' => [$noNext], 'stale' => [$stale, $editedOnly], 'pending_win' => [$pending]];
        foreach ($expect as $bucket => $leads) {
            $res = $this->get(route('employee.crm.leads.index', ['quick' => $bucket, 'assigned_to' => $this->rep->id]))->assertOk();
            foreach ($leads as $l) {
                $res->assertSee($l->name);
            }
            $res->assertDontSee('Smoke LH Healthy')->assertDontSee('Smoke LH Revived');
        }
    }

    public function test_crm_activity_updates_last_contact_and_reps_cannot_open_the_board(): void
    {
        $lead = $this->lead('Smoke LH Touch', ['next_follow_up_at' => now()->addDay()]);
        $this->actingAs($this->rep);
        $this->get(route('employee.crm.leads.health'))->assertNotFound();

        $this->post(route('employee.crm.activities.store'), ['type' => 'call', 'subject' => 'Smoke call', 'title' => 'Smoke call', 'status' => 'planned', 'sales_lead_id' => $lead->id]);
        $this->assertNull($lead->fresh()->last_contacted_at, 'a planned call is not a contact');
        $this->post(route('employee.crm.activities.store'), ['type' => 'call', 'subject' => 'Smoke call', 'title' => 'Smoke call', 'status' => 'held', 'sales_lead_id' => $lead->id]);
        $this->assertNotNull($lead->fresh()->last_contacted_at, 'a held call updates the last contact');
    }

    public function test_manager_board_shows_only_his_team(): void
    {
        $outsider = User::where('employee_job_id', EmployeeJob::where('code', 'sales')->value('id'))->where('is_active', true)
            ->whereNotIn('id', CrmScope::for($this->manager)->members()->pluck('id'))->first();
        $board = app(SalesLeadHealthService::class)->board(CrmScope::for($this->manager));
        $ids = array_column($board['rows'], 'id');
        $this->assertContains($this->rep->id, $ids);
        if ($outsider) {
            $this->assertNotContains($outsider->id, $ids);
        }
        $this->assertNull($board['unassigned'], 'unassigned leads are admin only');
    }

    private function lead(string $name, array $extra, $createdAt = null): SalesLead
    {
        $lead = SalesLead::create($extra + ['name' => $name, 'phone' => '0103'.random_int(1000000, 9999999), 'source' => 'call', 'stage' => 'interested', 'priority' => 'normal',
            'assigned_to' => $this->rep->id, 'follow_up_channel' => 'call']);
        if ($createdAt) {
            DB::table('sales_leads')->where('id', $lead->id)->update(['created_at' => $createdAt]);
        }

        return $lead->fresh();
    }
}
