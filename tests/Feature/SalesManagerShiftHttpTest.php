<?php

namespace Tests\Feature;

use App\Models\EmployeeJob;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\SalesShiftSwapRequest;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * اختبارات HTTP لشيفتات الفريق وربطها بمدير المبيعات.
 */
class SalesManagerShiftHttpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\EnsureEmployeeWorkAccess::class);

        $this->mock(\App\Services\WhatsAppQueueService::class, function ($mock) {
            $mock->shouldReceive('pendingCount')->andReturn(0);
        });

        foreach ([
            'sales_shift_channel_events',
            'sales_shift_swap_requests',
            'sales_shift_employee_profiles',
            'sales_shift_segments',
            'sales_shift_plans',
            'sales_team_members',
            'sales_teams',
            'users',
            'employee_jobs',
            'branches',
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

        Schema::create('sales_shift_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('work_start_hour')->default(10);
            $table->unsignedTinyInteger('work_end_hour')->default(26);
            $table->unsignedSmallInteger('takeover_grace_minutes')->default(10);
            $table->boolean('is_active')->default(true);
            $table->json('rules')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_shift_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_shift_plan_id');
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('start_hour');
            $table->unsignedTinyInteger('end_hour');
            $table->string('mode', 20)->default('normal');
            $table->json('channels');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sales_shift_employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('color_hex', 7)->default('#0EA5E9');
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sales_shift_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('segment_id')->nullable();
            $table->date('work_date');
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('manager_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_shift_channel_events', function (Blueprint $table) {
            $table->id();
            $table->string('channel_code', 30);
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->unsignedBigInteger('actor_user_id');
            $table->string('event_type', 30);
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /** @return array{manager: User, rep1: User, rep2: User, outsider: User, team: SalesTeam, plan: SalesShiftPlan, segment: SalesShiftSegment} */
    private function seedTeamWithShifts(): array
    {
        $salesJob = EmployeeJob::query()->create(['name' => 'مبيعات', 'code' => 'sales', 'is_active' => true]);
        $mgrJob = EmployeeJob::query()->create(['name' => 'مدير', 'code' => 'sales_manager', 'is_active' => true]);

        $manager = User::query()->create([
            'name' => 'مدير الفريق',
            'email' => 'mgr-shift-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $mgrJob->id,
        ]);

        $rep1 = User::query()->create([
            'name' => 'موظف أ',
            'email' => 'rep1-shift-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'weekly_off_day' => 5,
        ]);

        $rep2 = User::query()->create([
            'name' => 'موظف ب',
            'email' => 'rep2-shift-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'weekly_off_day' => 6,
        ]);

        $outsider = User::query()->create([
            'name' => 'خارج الفريق',
            'email' => 'out-shift-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
        ]);

        $team = SalesTeam::query()->create([
            'name' => 'فريق المبيعات أ',
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

        $plan = SalesShiftPlan::query()->create([
            'name' => 'خطة اختبار',
            'work_start_hour' => 10,
            'work_end_hour' => 26,
            'takeover_grace_minutes' => 10,
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-08-08 15:00:00')); // Saturday 3pm
        $dow = app(\App\Services\SalesShiftScheduleService::class)->salesDayIndex(now());

        $segment = SalesShiftSegment::query()->create([
            'sales_shift_plan_id' => $plan->id,
            'day_of_week' => $dow,
            'user_id' => $rep1->id,
            'start_hour' => 11,
            'end_hour' => 19,
            'mode' => 'normal',
            'channels' => ['whatsapp', 'calls'],
            'sort_order' => 0,
        ]);

        return compact('manager', 'rep1', 'rep2', 'outsider', 'team', 'plan', 'segment');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_manager_can_open_shifts_index(): void
    {
        ['manager' => $manager, 'rep1' => $rep1, 'team' => $team] = $this->seedTeamWithShifts();

        $response = $this->actingAs($manager)->get(route('employee.sales-manager.shifts.index'));

        $response->assertOk();
        $response->assertSee('شيفتات وقنوات الفريق', false);
        $response->assertSee('من على الشيفت الآن؟', false);
        $response->assertSee($rep1->name, false);
        $response->assertSee($team->name, false);
    }

    public function test_manager_can_open_member_shift_show(): void
    {
        ['manager' => $manager, 'rep1' => $rep1] = $this->seedTeamWithShifts();

        $response = $this->actingAs($manager)->get(route('employee.sales-manager.shifts.show', $rep1));

        $response->assertOk();
        $response->assertSee($rep1->name, false);
        $response->assertSee('الآن:', false);
    }

    public function test_manager_cannot_view_outside_team_member_shift(): void
    {
        ['manager' => $manager, 'outsider' => $outsider] = $this->seedTeamWithShifts();

        $response = $this->actingAs($manager)->get(route('employee.sales-manager.shifts.show', $outsider));

        $response->assertNotFound();
    }

    public function test_swap_index_scoped_to_manager_team_only(): void
    {
        ['manager' => $manager, 'rep1' => $rep1, 'rep2' => $rep2, 'outsider' => $outsider] = $this->seedTeamWithShifts();

        SalesShiftSwapRequest::query()->create([
            'requester_id' => $rep1->id,
            'partner_id' => $rep2->id,
            'work_date' => now()->toDateString(),
            'status' => SalesShiftSwapRequest::STATUS_PENDING,
        ]);

        $otherRep = User::query()->create([
            'name' => 'موظف آخر',
            'email' => 'other-shift-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => EmployeeJob::query()->first()->id,
        ]);

        SalesShiftSwapRequest::query()->create([
            'requester_id' => $otherRep->id,
            'partner_id' => $outsider->id,
            'work_date' => now()->toDateString(),
            'status' => SalesShiftSwapRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($manager)->get(route('employee.sales-manager.shift-swaps.index'));

        $response->assertOk();
        $response->assertSee($rep1->name, false);
        $response->assertSee($rep2->name, false);
        $response->assertDontSee($otherRep->name, false);
    }

    public function test_manager_can_approve_team_swap(): void
    {
        ['manager' => $manager, 'rep1' => $rep1, 'rep2' => $rep2] = $this->seedTeamWithShifts();

        $swap = SalesShiftSwapRequest::query()->create([
            'requester_id' => $rep1->id,
            'partner_id' => $rep2->id,
            'work_date' => now()->toDateString(),
            'status' => SalesShiftSwapRequest::STATUS_PENDING,
        ]);

        $response = $this->actingAs($manager)->post(route('employee.sales-manager.shift-swaps.review', $swap), [
            'action' => 'approve',
            'manager_notes' => 'موافق',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $swap->refresh();
        $this->assertSame(SalesShiftSwapRequest::STATUS_APPROVED, $swap->status);
        $this->assertSame($manager->id, (int) $swap->reviewed_by);
    }

    public function test_sales_employee_cannot_access_manager_shifts(): void
    {
        ['rep1' => $rep1] = $this->seedTeamWithShifts();

        $response = $this->actingAs($rep1)->get(route('employee.sales-manager.shifts.index'));

        $response->assertForbidden();
    }

    public function test_live_panel_shows_only_team_members(): void
    {
        ['rep1' => $rep1, 'rep2' => $rep2, 'outsider' => $outsider, 'plan' => $plan] = $this->seedTeamWithShifts();

        $dow = app(\App\Services\SalesShiftScheduleService::class)->salesDayIndex(now());

        SalesShiftSegment::query()->create([
            'sales_shift_plan_id' => $plan->id,
            'day_of_week' => $dow,
            'user_id' => $outsider->id,
            'start_hour' => 10,
            'end_hour' => 18,
            'mode' => 'normal',
            'channels' => ['instagram'],
            'sort_order' => 0,
        ]);

        $svc = app(\App\Services\SalesShiftScheduleService::class);
        $live = $svc->buildTeamLivePanel([$rep1->id, $rep2->id], $plan);

        $names = collect($live['active_now'])->pluck('user_name')->all();
        $this->assertContains($rep1->name, $names);
        $this->assertNotContains($outsider->name, $names);
    }
}
