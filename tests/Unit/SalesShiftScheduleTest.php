<?php

namespace Tests\Unit;

use App\Models\EmployeeJob;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\User;
use App\Services\SalesShiftPlanImporter;
use App\Services\SalesShiftScheduleService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesShiftScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'sales_shift_channel_events',
            'sales_shift_swap_requests',
            'sales_shift_employee_profiles',
            'sales_shift_segments',
            'sales_shift_plans',
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
            $table->string('role')->default('employee');
            $table->boolean('is_employee')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('weekly_off_day')->nullable();
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_shift_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('work_start_hour')->default(10);
            $table->unsignedTinyInteger('work_end_hour')->default(26);
            $table->unsignedSmallInteger('takeover_grace_minutes')->default(10);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
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
            $table->string('location_badge', 40)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sales_shift_employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('color_hex', 7)->default('#0EA5E9');
            $table->string('base_channels_label')->nullable();
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
            $table->string('source_type', 40)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function test_importer_creates_seven_day_plan(): void
    {
        $job = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales', 'is_active' => true]);
        foreach (['شهد', 'إسراء', 'مريم', 'حنين'] as $i => $name) {
            User::create([
                'name' => $name,
                'email' => "rep-shift-{$i}@test.local",
                'password' => Hash::make('x'),
                'is_employee' => true,
                'is_active' => true,
                'employee_job_id' => $job->id,
                'branch_id' => 1,
            ]);
        }

        $plan = app(SalesShiftPlanImporter::class)->importDefaultPlan(true);
        $this->assertTrue($plan->is_active);
        $this->assertGreaterThan(20, $plan->segments()->count());
    }

    public function test_channel_owner_resolves_for_active_segment(): void
    {
        $job = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales', 'is_active' => true]);
        $rep = User::create([
            'name' => 'مريم',
            'email' => 'mariam-shift@test.local',
            'password' => Hash::make('x'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);

        $plan = SalesShiftPlan::create([
            'name' => 'Test',
            'work_start_hour' => 10,
            'work_end_hour' => 26,
            'takeover_grace_minutes' => 10,
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-08-08 15:00:00')); // Saturday 3pm
        $dow = app(SalesShiftScheduleService::class)->salesDayIndex(now());

        SalesShiftSegment::create([
            'sales_shift_plan_id' => $plan->id,
            'day_of_week' => $dow,
            'user_id' => $rep->id,
            'start_hour' => 11,
            'end_hour' => 19,
            'mode' => 'normal',
            'channels' => ['whatsapp', 'followup'],
            'sort_order' => 0,
        ]);

        $owners = app(SalesShiftScheduleService::class)->channelOwnershipNow($plan);
        $this->assertSame($rep->id, $owners['whatsapp']['owner_id'] ?? null);
        $this->assertSame($rep->name, $owners['whatsapp']['owner_name'] ?? null);

        $check = app(SalesShiftScheduleService::class)->canUserRespondOnChannel($rep, 'whatsapp');
        $this->assertTrue($check['allowed']);

        Carbon::setTestNow();
    }

    public function test_non_owner_blocked_until_grace(): void
    {
        $job = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales', 'is_active' => true]);
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner-shift@test.local',
            'password' => Hash::make('x'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);
        $other = User::create([
            'name' => 'Other',
            'email' => 'other-shift@test.local',
            'password' => Hash::make('x'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);

        $plan = SalesShiftPlan::create([
            'name' => 'Test',
            'work_start_hour' => 10,
            'work_end_hour' => 26,
            'takeover_grace_minutes' => 10,
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-08-08 14:00:00'));
        $dow = app(SalesShiftScheduleService::class)->salesDayIndex(now());

        SalesShiftSegment::create([
            'sales_shift_plan_id' => $plan->id,
            'day_of_week' => $dow,
            'user_id' => $owner->id,
            'start_hour' => 10,
            'end_hour' => 18,
            'mode' => 'normal',
            'channels' => ['calls'],
            'sort_order' => 0,
        ]);

        $svc = app(SalesShiftScheduleService::class);
        $svc->recordChannelResponse($owner, 'calls', $owner->id, 'outbound_reply', 'crm', null, now()->subMinutes(2));

        $check = $svc->canUserRespondOnChannel($other, 'calls');
        $this->assertFalse($check['allowed']);
        $this->assertSame('not_owner', $check['reason']);

        Carbon::setTestNow();
    }

    public function test_build_team_live_panel_scoped_to_member_ids(): void
    {
        $job = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales', 'is_active' => true]);
        $inTeam = User::create([
            'name' => 'In Team',
            'email' => 'in-team-shift@test.local',
            'password' => Hash::make('x'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);
        $outside = User::create([
            'name' => 'Outside',
            'email' => 'outside-shift@test.local',
            'password' => Hash::make('x'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);

        $plan = SalesShiftPlan::create([
            'name' => 'Test',
            'work_start_hour' => 10,
            'work_end_hour' => 26,
            'takeover_grace_minutes' => 10,
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-08-08 15:00:00'));
        $dow = app(SalesShiftScheduleService::class)->salesDayIndex(now());

        foreach ([$inTeam, $outside] as $user) {
            SalesShiftSegment::create([
                'sales_shift_plan_id' => $plan->id,
                'day_of_week' => $dow,
                'user_id' => $user->id,
                'start_hour' => 11,
                'end_hour' => 19,
                'mode' => 'normal',
                'channels' => ['whatsapp'],
                'sort_order' => 0,
            ]);
        }

        $svc = app(SalesShiftScheduleService::class);
        $live = $svc->buildTeamLivePanel([(int) $inTeam->id], $plan);

        $this->assertCount(1, $live['active_now']);
        $this->assertSame($inTeam->id, $live['active_now'][0]['user_id']);
        $this->assertArrayHasKey('whatsapp', $live['ownership']);

        Carbon::setTestNow();
    }
}
