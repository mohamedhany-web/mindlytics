<?php

namespace Tests\Unit;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeJob;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\EmployeeAttendanceService;
use App\Services\SalesManagerOpsBoardService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * اختبارات قوية لتدفق Online/Offline + موافقة المدير + أيام النزول.
 */
class OfflineOnlineAttendanceFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'employee_attendance.sales_employees_only' => true,
            'employee_attendance.penalties_enabled' => false,
            'employee_attendance.late_penalty_enabled' => false,
            'employee_attendance.absence_penalty_enabled' => false,
            'employee_attendance.incomplete_penalty_enabled' => false,
        ]);

        Schema::dropIfExists('sales_activities');
        Schema::dropIfExists('sales_daily_reports');
        Schema::dropIfExists('sales_leads');
        Schema::dropIfExists('employee_presence_violations');
        Schema::dropIfExists('employee_presence_daily');
        Schema::dropIfExists('employee_work_unlocks');
        Schema::dropIfExists('employee_attendance_records');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('users');
        Schema::dropIfExists('employee_jobs');
        Schema::dropIfExists('work_schedules');

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
            $table->unsignedTinyInteger('weekly_off_day')->nullable();
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->string('work_mode', 20)->default('online');
            $table->string('offline_attendance_type', 30)->nullable();
            $table->json('onsite_days')->nullable();
            $table->json('work_week_plan')->nullable();
            $table->unsignedBigInteger('employee_job_id')->nullable();
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
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
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

        Schema::create('employee_presence_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->unsignedBigInteger('employee_attendance_record_id')->nullable();
            $table->unsignedInteger('online_seconds')->default(0);
            $table->unsignedInteger('offline_seconds')->default(0);
            $table->unsignedInteger('violation_count')->default(0);
            $table->timestamps();
        });

        Schema::create('employee_presence_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->unsignedBigInteger('employee_attendance_record_id')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('stage')->default('new_lead');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('note');
            $table->string('outcome')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('report_date');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    private function salesJob(): EmployeeJob
    {
        return EmployeeJob::query()->create([
            'name' => 'مبيعات',
            'code' => 'sales',
            'is_active' => true,
        ]);
    }

    private function makeSchedule(): WorkSchedule
    {
        return WorkSchedule::query()->create([
            'name' => 'Test Shift',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'required_hours' => 8,
            'grace_minutes' => 15,
            'early_access_minutes' => 30,
            'is_active' => true,
            'work_days' => null,
        ]);
    }

    private function makeEmployee(WorkSchedule $schedule, array $attrs = []): User
    {
        $job = $this->salesJob();

        return User::withoutEvents(function () use ($schedule, $job, $attrs) {
            return User::query()->create(array_merge([
                'name' => 'Sales Rep',
                'email' => 'rep'.uniqid('', true).'@test.local',
                'phone' => '010'.random_int(10000000, 99999999),
                'password' => bcrypt('password'),
                'role' => 'student',
                'is_employee' => true,
                'is_active' => true,
                'hire_date' => now()->toDateString(),
                'weekly_off_day' => 5,
                'work_schedule_id' => $schedule->id,
                'employee_job_id' => $job->id,
                'work_mode' => User::WORK_MODE_ONLINE,
            ], $attrs));
        });
    }

    private function makeManager(): User
    {
        $job = EmployeeJob::query()->firstOrCreate(
            ['code' => 'sales_manager'],
            ['name' => 'مدير مبيعات', 'is_active' => true]
        );

        return User::withoutEvents(function () use ($job) {
            return User::query()->create([
                'name' => 'Sales Manager',
                'email' => 'mgr'.uniqid('', true).'@test.local',
                'phone' => '011'.random_int(10000000, 99999999),
                'password' => bcrypt('password'),
                'role' => 'student',
                'is_employee' => true,
                'is_active' => true,
                'hire_date' => now()->toDateString(),
                'employee_job_id' => $job->id,
                'work_mode' => User::WORK_MODE_ONLINE,
            ]);
        });
    }

    public function test_onsite_selected_days_off_outside_chosen_days(): void
    {
        // Monday 2026-08-03 = dayOfWeek 1
        Carbon::setTestNow(Carbon::parse('2026-08-03 10:00:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_SELECTED_DAYS,
            'onsite_days' => [2, 3, 4], // Tue Wed Thu only
            'weekly_off_day' => null,
        ]);

        $this->assertTrue($employee->isOfflineWorker());
        $this->assertTrue($employee->isAttendanceOffDay(now()));

        $service = app(EmployeeAttendanceService::class);
        $record = $service->ensureTodayRecord($employee, $schedule, now());
        $this->assertSame('off_day', $record->status);
    }

    public function test_onsite_selected_days_is_work_day_on_chosen_day(): void
    {
        // Tuesday 2026-08-04 = dayOfWeek 2
        Carbon::setTestNow(Carbon::parse('2026-08-04 10:00:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_SELECTED_DAYS,
            'onsite_days' => [2, 3, 4],
            'weekly_off_day' => null,
        ]);

        $this->assertFalse($employee->isAttendanceOffDay(now()));

        $service = app(EmployeeAttendanceService::class);
        $record = $service->ensureTodayRecord($employee, $schedule, now());
        $this->assertSame('pending', $record->status);
    }

    public function test_online_clock_in_is_immediate_without_manager_approval(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:05:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_ONLINE,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');

        $service = app(EmployeeAttendanceService::class);
        $this->assertTrue($employee->isSubjectToWorkSchedule());

        $service->ensureTodayRecord($employee, $schedule, now());
        $record = $service->clockIn($employee, '127.0.0.1');

        $this->assertNotNull($record->clock_in_at);
        $this->assertSame('active', $record->status);
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_NOT_REQUIRED, $record->attendance_approval_status);
        $this->assertFalse($record->isAwaitingManagerApproval());

        $state = $service->getState($employee);
        $this->assertTrue($state['can_access']);
        $this->assertSame('working', $state['mode']);
    }

    public function test_offline_clock_in_requests_manager_approval_and_blocks_access(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:05:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());

        $record = $service->clockIn($employee, '127.0.0.1');

        $this->assertNull($record->clock_in_at);
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_PENDING, $record->attendance_approval_status);
        $this->assertTrue($record->isAwaitingManagerApproval());
        $this->assertNotNull($record->attendance_requested_at);

        $state = $service->getState($employee);
        $this->assertSame('pending_manager_approval', $state['mode']);
        $this->assertFalse($state['can_access']);
        $this->assertFalse($state['can_clock_in']);
    }

    public function test_manager_approves_on_time_opens_system_without_late_flag(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:05:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');

        $approved = $service->approveAttendanceRequest(
            $pending,
            $manager,
            EmployeeAttendanceRecord::LATENESS_ON_TIME
        );

        $this->assertNotNull($approved->clock_in_at);
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_APPROVED, $approved->attendance_approval_status);
        $this->assertSame(EmployeeAttendanceRecord::LATENESS_ON_TIME, $approved->manager_lateness_decision);
        $this->assertFalse((bool) $approved->is_late);
        $this->assertFalse((bool) $approved->late_penalty_waived);
        $this->assertSame($manager->id, $approved->approved_by);

        $state = $service->getState($employee->fresh());
        $this->assertTrue($state['can_access']);
        $this->assertSame('working', $state['mode']);
    }

    public function test_manager_excused_late_sets_late_waived_without_penalty_path(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 10:30:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');

        $approved = $service->approveAttendanceRequest(
            $pending,
            $manager,
            EmployeeAttendanceRecord::LATENESS_EXCUSED
        );

        $this->assertTrue((bool) $approved->is_late);
        $this->assertTrue((bool) $approved->late_penalty_waived);
        $this->assertSame(EmployeeAttendanceRecord::LATENESS_EXCUSED, $approved->manager_lateness_decision);
        $this->assertSame('active', $approved->status);
    }

    public function test_manager_confirmed_late_marks_late(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 10:30:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');

        $approved = $service->approveAttendanceRequest(
            $pending,
            $manager,
            EmployeeAttendanceRecord::LATENESS_CONFIRMED
        );

        $this->assertTrue((bool) $approved->is_late);
        $this->assertFalse((bool) $approved->late_penalty_waived);
        $this->assertSame(EmployeeAttendanceRecord::LATENESS_CONFIRMED, $approved->manager_lateness_decision);
    }

    public function test_manager_reject_keeps_locked_and_allows_re_request(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:10:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');

        $rejected = $service->rejectAttendanceRequest($pending, $manager, 'مش موجود في المكتب');

        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_REJECTED, $rejected->attendance_approval_status);
        $this->assertNull($rejected->clock_in_at);

        $state = $service->getState($employee->fresh());
        $this->assertSame('attendance_rejected', $state['mode']);
        $this->assertTrue($state['can_clock_in']);
        $this->assertFalse($state['can_access']);

        $again = $service->clockIn($employee->fresh(), '127.0.0.1');
        $this->assertTrue($again->isAwaitingManagerApproval());
    }

    public function test_waive_late_penalty_for_online_late_record(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:40:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_ONLINE,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $record = $service->clockIn($employee, '127.0.0.1');

        $this->assertTrue((bool) $record->is_late);

        $waived = $service->waiveLatePenalty($record, $manager, 'سبب وجيه');

        $this->assertTrue((bool) $waived->late_penalty_waived);
        $this->assertSame(EmployeeAttendanceRecord::LATENESS_EXCUSED, $waived->manager_lateness_decision);
    }

    public function test_pending_request_not_overwritten_by_resync(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:10:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');

        $synced = $service->syncDayStatusFromEmployeeFile($employee, $pending->fresh(), now());

        $this->assertTrue($synced->isAwaitingManagerApproval());
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_PENDING, $synced->attendance_approval_status);
    }

    public function test_ops_board_lists_pending_and_filters_work_mode(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:20:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $online = $this->makeEmployee($schedule, [
            'name' => 'Online Rep',
            'work_mode' => User::WORK_MODE_ONLINE,
            'weekly_off_day' => 5,
        ]);
        $online->load('employeeJob');

        $offline = $this->makeEmployee($schedule, [
            'name' => 'Offline Rep',
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $offline->load('employeeJob');

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($online, $schedule, now());
        $service->clockIn($online, '127.0.0.1');
        $service->ensureTodayRecord($offline, $schedule, now());
        $service->clockIn($offline, '127.0.0.1');

        $board = app(SalesManagerOpsBoardService::class)->build(
            [$online->id, $offline->id],
            now(),
            ['work_mode' => 'offline']
        );

        $this->assertSame(1, $board['rows']->count());
        $this->assertSame($offline->id, $board['rows']->first()['user_id']);
        $this->assertTrue($board['rows']->first()['pending_approval']);
        $this->assertSame(1, $board['stats']['pending_approval']);
        $this->assertSame(1, $board['pendingApprovals']->count());
    }

    public function test_clock_in_at_uses_request_time_on_approval(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:12:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_OFFLINE,
            'offline_attendance_type' => User::OFFLINE_FULL_TIME,
            'weekly_off_day' => 5,
        ]);
        $employee->load('employeeJob');
        $manager = $this->makeManager();

        $service = app(EmployeeAttendanceService::class);
        $service->ensureTodayRecord($employee, $schedule, now());
        $pending = $service->clockIn($employee, '127.0.0.1');
        $requestedAt = $pending->attendance_requested_at->copy();

        Carbon::setTestNow(Carbon::parse('2026-08-04 09:25:00', 'Africa/Cairo'));
        $approved = $service->approveAttendanceRequest(
            $pending,
            $manager,
            EmployeeAttendanceRecord::LATENESS_ON_TIME
        );

        $this->assertTrue($approved->clock_in_at->equalTo($requestedAt));
    }

    public function test_hybrid_offline_day_requires_approval_online_day_clocks_in(): void
    {
        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_HYBRID,
            'work_week_plan' => [
                '0' => ['active' => true, 'attendance_mode' => 'online', 'start_time' => null, 'end_time' => null, 'required_hours' => null],
                '1' => ['active' => true, 'attendance_mode' => 'offline', 'start_time' => '10:00', 'end_time' => '18:00', 'required_hours' => 8],
                '2' => ['active' => true, 'attendance_mode' => 'online', 'start_time' => '11:00', 'end_time' => '19:00', 'required_hours' => 7],
                '3' => ['active' => true, 'attendance_mode' => 'online', 'start_time' => null, 'end_time' => null, 'required_hours' => null],
                '4' => ['active' => true, 'attendance_mode' => 'online', 'start_time' => null, 'end_time' => null, 'required_hours' => null],
                '5' => ['active' => false, 'attendance_mode' => 'online', 'start_time' => null, 'end_time' => null, 'required_hours' => null],
                '6' => ['active' => false, 'attendance_mode' => 'online', 'start_time' => null, 'end_time' => null, 'required_hours' => null],
            ],
        ]);
        $employee->load('employeeJob');
        $service = app(EmployeeAttendanceService::class);

        // Monday = offline (day 1)
        Carbon::setTestNow(Carbon::parse('2026-08-03 10:05:00', 'Africa/Cairo'));
        $this->assertTrue($employee->requiresManagerApprovalFor(now()));
        $monday = $service->ensureTodayRecord($employee, $schedule, now());
        $this->assertSame('10:00', $monday->scheduled_start->format('H:i'));
        $this->assertSame(480, (int) $monday->required_minutes);
        $pending = $service->clockIn($employee, '127.0.0.1');
        $this->assertTrue($pending->isAwaitingManagerApproval());
        $this->assertNull($pending->clock_in_at);

        // Tuesday = online with custom start 11:00
        Carbon::setTestNow(Carbon::parse('2026-08-04 11:20:00', 'Africa/Cairo'));
        $this->assertFalse($employee->requiresManagerApprovalFor(now()));
        $tuesday = $service->ensureTodayRecord($employee, $schedule, now());
        $this->assertSame('11:00', $tuesday->scheduled_start->format('H:i'));
        $this->assertSame(420, (int) $tuesday->required_minutes);
        $in = $service->clockIn($employee, '127.0.0.1');
        $this->assertFalse($in->isAwaitingManagerApproval());
        $this->assertNotNull($in->clock_in_at);
        $this->assertTrue((bool) $in->is_late);
    }

    public function test_hybrid_inactive_day_is_off_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-07 10:00:00', 'Africa/Cairo')); // Friday = 5

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, [
            'work_mode' => User::WORK_MODE_HYBRID,
            'work_week_plan' => [
                '0' => ['active' => true, 'attendance_mode' => 'online'],
                '1' => ['active' => true, 'attendance_mode' => 'online'],
                '2' => ['active' => true, 'attendance_mode' => 'online'],
                '3' => ['active' => true, 'attendance_mode' => 'online'],
                '4' => ['active' => true, 'attendance_mode' => 'online'],
                '5' => ['active' => false, 'attendance_mode' => 'online'],
                '6' => ['active' => false, 'attendance_mode' => 'online'],
            ],
        ]);

        $this->assertTrue($employee->isAttendanceOffDay(now()));
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(EmployeeAttendanceService::class)->clockIn($employee, '127.0.0.1');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
