<?php

namespace Tests\Feature;

use App\Models\EmployeeJob;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Services\SalesManagerHubService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesManagerHubDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\EnsureEmployeeWorkAccess::class);

        $this->mock(\App\Services\WhatsAppQueueService::class, function ($mock) {
            $mock->shouldReceive('pendingCount')->andReturn(0);
        });

        foreach ([
            'sales_activities', 'sales_leads', 'sales_shift_swap_requests',
            'sales_shift_segments', 'sales_shift_plans', 'sales_shift_employee_profiles',
            'sales_shift_channel_events', 'employee_attendance_records',
            'employee_presence_daily', 'employee_presence_violations',
            'leave_requests', 'sales_daily_reports', 'sales_kpi_targets', 'sales_team_members',
            'sales_teams', 'users', 'employee_jobs', 'work_schedules', 'branches',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
        \DB::table('branches')->insert([
            'name' => 'Main', 'slug' => 'main', 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->boolean('is_employee')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('weekly_off_day')->nullable();
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->string('work_mode', 20)->default('online');
            $table->string('presence_status')->nullable();
            $table->dateTime('presence_last_seen_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sales_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('manager_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_team_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('stage')->default('new_lead');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->decimal('expected_value', 12, 2)->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->dateTime('last_contacted_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('won_confirmed_at')->nullable();
            $table->string('priority')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('note');
            $table->string('outcome')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->dateTime('clock_in_at')->nullable();
            $table->dateTime('clock_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_late')->default(false);
            $table->string('attendance_approval_status', 30)->default('not_required');
            $table->dateTime('attendance_requested_at')->nullable();
            $table->dateTime('scheduled_start')->nullable();
            $table->dateTime('scheduled_end')->nullable();
            $table->boolean('late_penalty_waived')->default(false);
            $table->timestamps();
        });

        Schema::create('employee_presence_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->unsignedInteger('online_seconds')->default(0);
            $table->unsignedInteger('away_seconds')->default(0);
            $table->unsignedInteger('offline_seconds')->default(0);
            $table->timestamps();
        });

        Schema::create('employee_presence_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->unsignedBigInteger('employee_attendance_record_id')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('reason')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('sales_kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('year_month', 7);
            $table->json('targets')->nullable();
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('status')->default('pending');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('sales_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('report_date');
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('sales_shift_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('sales_shift_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_plan_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('sales_shift_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('partner_id');
            $table->date('work_date');
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    /** @return array{manager: User, rep1: User, rep2: User, team: SalesTeam} */
    private function seedTeam(): array
    {
        $salesJob = EmployeeJob::query()->create(['name' => 'مبيعات', 'code' => 'sales', 'is_active' => true]);
        $mgrJob = EmployeeJob::query()->create(['name' => 'مدير', 'code' => 'sales_manager', 'is_active' => true]);

        $manager = User::query()->create([
            'name' => 'مدير Hub',
            'email' => 'mgr-hub-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $mgrJob->id,
            'branch_id' => 1,
        ]);

        $rep1 = User::query()->create([
            'name' => 'أحمد',
            'email' => 'ahmed-hub-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'branch_id' => 1,
            'presence_status' => 'online',
            'presence_last_seen_at' => now(),
        ]);

        $rep2 = User::query()->create([
            'name' => 'محمد',
            'email' => 'mohamed-hub-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'branch_id' => 1,
        ]);

        $team = SalesTeam::query()->create([
            'name' => 'فريق Hub',
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);

        foreach ([$rep1, $rep2] as $rep) {
            SalesTeamMember::query()->create([
                'sales_team_id' => $team->id,
                'user_id' => $rep->id,
                'role' => 'member',
            ]);
        }

        $lead = SalesLead::query()->create([
            'name' => 'عميل اختبار',
            'stage' => 'new_lead',
            'assigned_to' => $rep1->id,
        ]);

        SalesActivity::query()->create([
            'sales_lead_id' => $lead->id,
            'user_id' => $rep1->id,
            'type' => 'call',
            'outcome' => 'interested',
            'title' => 'مكالمة',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SalesActivity::query()->create([
            'sales_lead_id' => $lead->id,
            'user_id' => $rep1->id,
            'type' => 'meeting',
            'title' => 'اجتماع',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return compact('manager', 'rep1', 'rep2', 'team');
    }

    public function test_manager_dashboard_renders_hub_sections(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-06 12:00:00'));
        ['manager' => $manager, 'rep1' => $rep1] = $this->seedTeam();

        $response = $this->actingAs($manager)->get(route('employee.sales-manager.dashboard'));

        $response->assertOk();
        $response->assertSee('نظرة تنفيذية', false);
        $response->assertSee('ترتيب أداء الفريق', false);
        $response->assertSee('مراقبة مباشرة', false);
        $response->assertSee($rep1->name, false);
        $response->assertSee('Leaderboard', false);

        Carbon::setTestNow();
    }

    public function test_hub_service_ranks_by_target_score(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-06 12:00:00'));
        ['rep1' => $rep1, 'rep2' => $rep2, 'team' => $team] = $this->seedTeam();

        $hub = app(SalesManagerHubService::class)->build(
            $team,
            [(int) $rep1->id, (int) $rep2->id],
            today()
        );

        $this->assertNotEmpty($hub['ranking']);
        $this->assertSame($rep1->id, $hub['ranking'][0]['user_id']);
        $this->assertGreaterThanOrEqual(1, $hub['kpis']['calls_today']);
        $this->assertArrayHasKey('pipeline', $hub);
        $this->assertArrayHasKey('alerts', $hub);
        $this->assertSame(2, $hub['kpis']['team_members']);

        Carbon::setTestNow();
    }

    public function test_employee_today_summary_for_profile(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-06 12:00:00'));
        ['rep1' => $rep1] = $this->seedTeam();

        $today = app(SalesManagerHubService::class)->employeeToday($rep1);

        $this->assertSame(1, $today['metrics']['calls']);
        $this->assertSame(1, $today['metrics']['meetings']);
        $this->assertNotEmpty($today['activities']);

        Carbon::setTestNow();
    }
}
