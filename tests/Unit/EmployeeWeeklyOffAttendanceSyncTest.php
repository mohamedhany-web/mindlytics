<?php

namespace Tests\Unit;

use App\Models\EmployeeAttendanceRecord;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\EmployeeAttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * اختبار مزامنة يوم الراحة بدون تشغيل كل migrations المشروع
 * (بعضها يعتمد على MySQL information_schema).
 */
class EmployeeWeeklyOffAttendanceSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('employee_work_unlocks');
        Schema::dropIfExists('employee_attendance_records');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('users');
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
            $table->unsignedBigInteger('employee_job_id')->nullable();
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

    private function makeEmployee(WorkSchedule $schedule, ?int $weeklyOffDay): User
    {
        return User::withoutEvents(function () use ($schedule, $weeklyOffDay) {
            return User::query()->create([
                'name' => 'Sales Rep',
                'email' => 'rep'.uniqid('', true).'@test.local',
                'phone' => '010'.random_int(10000000, 99999999),
                'password' => bcrypt('password'),
                'role' => 'student',
                'is_employee' => true,
                'is_active' => true,
                'hire_date' => now()->toDateString(),
                'weekly_off_day' => $weeklyOffDay,
                'work_schedule_id' => $schedule->id,
            ]);
        });
    }

    public function test_ensure_today_record_marks_off_day_from_employee_file(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-10 10:00:00', 'Africa/Cairo')); // Friday

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, 5);
        $service = app(EmployeeAttendanceService::class);

        $record = $service->ensureTodayRecord($employee, $schedule, now());

        $this->assertSame('off_day', $record->status);
        $this->assertSame(now()->toDateString(), $record->work_date->toDateString());
    }

    public function test_changing_weekly_off_resyncs_pending_today_record(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-10 10:00:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, 4); // Thursday off
        $service = app(EmployeeAttendanceService::class);

        $record = $service->ensureTodayRecord($employee, $schedule, now());
        $this->assertSame('pending', $record->status);

        $employee->forceFill(['weekly_off_day' => 5])->save();
        $employee->refresh();

        $synced = $service->syncDayStatusFromEmployeeFile($employee, $record->fresh(), now());

        $this->assertSame('off_day', $synced->status);
        $this->assertSame($record->id, $synced->id);
    }

    public function test_does_not_override_active_clocked_in_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-10 10:00:00', 'Africa/Cairo'));

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, 4);
        $service = app(EmployeeAttendanceService::class);

        $record = $service->ensureTodayRecord($employee, $schedule, now());
        $record->update([
            'status' => 'active',
            'clock_in_at' => now()->subHour(),
        ]);

        $employee->forceFill(['weekly_off_day' => 5])->save();
        $employee->refresh();

        $synced = $service->syncDayStatusFromEmployeeFile($employee, $record->fresh(), now());

        $this->assertSame('active', $synced->status);
    }

    public function test_work_day_when_weekly_off_is_another_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-09 10:00:00', 'Africa/Cairo')); // Thursday

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, 5);
        $service = app(EmployeeAttendanceService::class);

        $record = $service->ensureTodayRecord($employee, $schedule, now());

        $this->assertSame('pending', $record->status);
    }

    public function test_null_weekly_off_marks_saturday_as_off_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-11 10:00:00', 'Africa/Cairo')); // Saturday

        $schedule = $this->makeSchedule();
        $employee = $this->makeEmployee($schedule, null);
        $service = app(EmployeeAttendanceService::class);

        $record = $service->ensureTodayRecord($employee, $schedule, now());

        $this->assertSame('off_day', $record->status);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
