<?php

namespace Tests\Feature;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeJob;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesShiftScheduleService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OfficeShiftAttendanceConfirmTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\EnsureEmployeeWorkAccess::class);

        config([
            'employee_attendance.sales_employees_only' => true,
            'employee_attendance.penalties_enabled' => true,
            'employee_attendance.late_penalty_enabled' => true,
            'employee_attendance.late_penalty_amount' => 25.0,
            'employee_attendance.notify_employee' => false,
            'employee_attendance.penalty_effective_from' => null,
        ]);

        foreach ([
            'notifications',
            'employee_salary_deductions',
            'employee_agreements',
            'sales_team_members',
            'sales_teams',
            'sales_shift_channel_events',
            'sales_shift_swap_requests',
            'sales_shift_employee_profiles',
            'sales_shift_segments',
            'sales_shift_plans',
            'employee_work_unlocks',
            'employee_attendance_records',
            'leave_requests',
            'users',
            'employee_jobs',
            'work_schedules',
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

        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('required_hours', 5, 2)->default(8);
            $table->json('work_days')->nullable();
            $table->unsignedSmallInteger('grace_minutes')->default(15);
            $table->unsignedSmallInteger('early_access_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

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
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('student');
            $table->boolean('is_employee')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->unsignedTinyInteger('weekly_off_day')->nullable();
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->string('work_mode', 20)->default('online');
            $table->string('offline_attendance_type', 30)->nullable();
            $table->json('onsite_days')->nullable();
            $table->json('work_week_plan')->nullable();
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('presence_status')->nullable();
            $table->dateTime('presence_last_seen_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('type')->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('days')->default(1);
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->date('work_date');
            $table->dateTime('scheduled_start')->nullable();
            $table->dateTime('scheduled_end')->nullable();
            $table->unsignedSmallInteger('required_minutes')->default(480);
            $table->dateTime('clock_in_at')->nullable();
            $table->dateTime('clock_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_late')->default(false);
            $table->string('attendance_approval_status', 30)->default('not_required');
            $table->dateTime('attendance_requested_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('manager_lateness_decision', 30)->nullable();
            $table->boolean('late_penalty_waived')->default(false);
            $table->string('approval_rejection_reason', 500)->nullable();
            $table->string('clock_in_ip', 45)->nullable();
            $table->string('clock_out_ip', 45)->nullable();
            $table->unsignedBigInteger('late_deduction_id')->nullable();
            $table->unsignedBigInteger('presence_deduction_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'work_date']);
        });

        Schema::create('employee_work_unlocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unlocked_by')->nullable();
            $table->date('work_date');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->string('reason')->nullable();
            $table->string('duration_label')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->unsignedBigInteger('revoked_by')->nullable();
            $table->string('revoke_reason')->nullable();
            $table->json('metadata')->nullable();
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

        Schema::create('employee_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('employee_salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('agreement_id')->nullable();
            $table->string('deduction_number')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('type')->default('penalty');
            $table->date('deduction_date')->nullable();
            $table->string('status')->default('applied');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('priority')->nullable();
            $table->string('audience')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * @return array{0: User, 1: User, 2: SalesTeam, 3: Carbon}
     */
    private function seedOfficeTeam(): array
    {
        $salesJob = EmployeeJob::query()->create(['name' => 'مبيعات', 'code' => 'sales', 'is_active' => true]);
        $mgrJob = EmployeeJob::query()->create(['name' => 'مدير', 'code' => 'sales_manager', 'is_active' => true]);
        $schedule = WorkSchedule::query()->create([
            'name' => 'Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'required_hours' => 8,
            'grace_minutes' => 15,
            'early_access_minutes' => 30,
            'is_active' => true,
        ]);

        $manager = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Manager',
            'email' => 'mgr-office@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => '2024-01-01',
            'employee_job_id' => $mgrJob->id,
            'work_schedule_id' => $schedule->id,
            'work_mode' => 'online',
            'branch_id' => 1,
        ]));

        $rep = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Office Rep',
            'email' => 'rep-office@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => '2024-01-01',
            'employee_job_id' => $salesJob->id,
            'work_schedule_id' => $schedule->id,
            'work_mode' => 'offline',
            'offline_attendance_type' => 'full_time',
            'branch_id' => 1,
        ]));

        $team = SalesTeam::query()->create([
            'name' => 'Team A',
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);
        SalesTeamMember::query()->create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
            'role' => 'member',
        ]);

        $date = Carbon::parse('2026-08-08')->startOfDay();
        Carbon::setTestNow($date->copy()->setTime(12, 0));

        $plan = SalesShiftPlan::create([
            'name' => 'Active',
            'work_start_hour' => 10,
            'work_end_hour' => 26,
            'takeover_grace_minutes' => 10,
            'is_active' => true,
        ]);
        $dow = app(SalesShiftScheduleService::class)->salesDayIndex($date);
        SalesShiftSegment::create([
            'sales_shift_plan_id' => $plan->id,
            'day_of_week' => $dow,
            'user_id' => $rep->id,
            'start_hour' => 11,
            'end_hour' => 19,
            'mode' => SalesShiftSegment::MODE_NORMAL,
            'channels' => ['whatsapp'],
            'location_badge' => 'من المقر',
            'sort_order' => 0,
        ]);

        return [$manager, $rep, $team, $date];
    }

    public function test_confirm_on_time_without_deduction(): void
    {
        [$manager, $rep, , $date] = $this->seedOfficeTeam();

        $record = app(EmployeeAttendanceService::class)->confirmOfficeShiftPresence(
            $rep,
            $manager,
            $date,
            'on_time',
            null,
            '127.0.0.1',
        );

        $this->assertNotNull($record->clock_in_at);
        $this->assertFalse((bool) $record->is_late);
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_APPROVED, $record->attendance_approval_status);
        $this->assertSame(EmployeeAttendanceRecord::LATENESS_ON_TIME, $record->manager_lateness_decision);
        $this->assertNull($record->late_deduction_id);
        $this->assertSame(0, EmployeeSalaryDeduction::query()->count());

        Carbon::setTestNow();
    }

    public function test_confirm_late_with_custom_deduction_amount(): void
    {
        [$manager, $rep, , $date] = $this->seedOfficeTeam();

        $record = app(EmployeeAttendanceService::class)->confirmOfficeShiftPresence(
            $rep,
            $manager,
            $date,
            'late',
            47.5,
            '127.0.0.1',
        );

        $this->assertTrue((bool) $record->is_late);
        $this->assertNotNull($record->late_deduction_id);
        $deduction = EmployeeSalaryDeduction::query()->find($record->late_deduction_id);
        $this->assertNotNull($deduction);
        $this->assertEquals(47.5, (float) $deduction->amount);
        $this->assertSame((int) $rep->id, (int) $deduction->employee_id);

        Carbon::setTestNow();
    }

    public function test_http_manager_can_confirm_late_with_custom_amount(): void
    {
        [$manager, $rep, , $date] = $this->seedOfficeTeam();

        $this->actingAs($manager)
            ->post(route('employee.sales-manager.attendance.offline-day.confirm'), [
                'employee_id' => $rep->id,
                'date' => $date->toDateString(),
                'decision' => 'late',
                'deduction_amount' => 33,
            ])
            ->assertRedirect(route('employee.sales-manager.attendance.offline-day', ['date' => $date->toDateString()]));

        $record = EmployeeAttendanceRecord::query()
            ->where('user_id', $rep->id)
            ->whereDate('work_date', $date->toDateString())
            ->first();

        $this->assertNotNull($record);
        $this->assertTrue((bool) $record->is_late);
        $this->assertEquals(33.0, (float) EmployeeSalaryDeduction::query()->find($record->late_deduction_id)?->amount);

        Carbon::setTestNow();
    }

    public function test_http_manager_cannot_confirm_non_team_member(): void
    {
        [$manager, $rep, , $date] = $this->seedOfficeTeam();

        $outsider = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Outsider',
            'email' => 'out-office@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => '2024-01-01',
            'employee_job_id' => $rep->employee_job_id,
            'work_schedule_id' => $rep->work_schedule_id,
            'work_mode' => 'offline',
            'branch_id' => 1,
        ]));

        $this->actingAs($manager)
            ->post(route('employee.sales-manager.attendance.offline-day.confirm'), [
                'employee_id' => $outsider->id,
                'date' => $date->toDateString(),
                'decision' => 'on_time',
            ])
            ->assertForbidden();

        Carbon::setTestNow();
    }
}
