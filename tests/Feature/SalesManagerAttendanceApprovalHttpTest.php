<?php

namespace Tests\Feature;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeJob;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * اختبارات HTTP لموافقة المدير ولوحة المتابعة.
 */
class SalesManagerAttendanceApprovalHttpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // المدير أيضاً خاضع لقفل الدوام؛ نختبر منطق الموافقة/اللوحة بدون قفل الشاشة
        $this->withoutMiddleware(\App\Http\Middleware\EnsureEmployeeWorkAccess::class);

        config([
            'employee_attendance.sales_employees_only' => true,
            'employee_attendance.system_lock_enabled' => true,
            'employee_attendance.penalties_enabled' => false,
            'employee_attendance.late_penalty_enabled' => false,
        ]);

        foreach ([
            'sales_team_members', 'sales_teams', 'employee_work_unlocks',
            'employee_attendance_records', 'leave_requests', 'users',
            'employee_jobs', 'work_schedules', 'employee_presence_daily',
            'employee_presence_violations', 'sales_activities', 'sales_daily_reports',
            'sales_leads',
        ] as $table) {
            Schema::dropIfExists($table);
        }

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
            $table->json('metadata')->nullable();
            $table->timestamps();
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

        Schema::create('employee_presence_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->unsignedInteger('offline_seconds')->default(0);
            $table->unsignedInteger('violation_count')->default(0);
            $table->timestamps();
        });

        Schema::create('employee_presence_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
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

    private function seedTeam(): array
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
            'email' => 'mgr'.uniqid().'@t.test',
            'phone' => '011'.random_int(10000000, 99999999),
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => now()->toDateString(),
            'employee_job_id' => $mgrJob->id,
            'work_mode' => 'online',
        ]));

        $rep = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Offline Rep',
            'email' => 'rep'.uniqid().'@t.test',
            'phone' => '010'.random_int(10000000, 99999999),
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => now()->toDateString(),
            'employee_job_id' => $salesJob->id,
            'work_schedule_id' => $schedule->id,
            'work_mode' => 'offline',
            'offline_attendance_type' => 'full_time',
            'weekly_off_day' => 5,
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

        return compact('manager', 'rep', 'schedule', 'team');
    }

    public function test_manager_can_open_ops_board(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 10:00:00', 'Africa/Cairo'));
        ['manager' => $manager] = $this->seedTeam();

        // partial بدون layout الموظف (السايدبار يعتمد جداول واتساب غير لازمة لهذا الاختبار)
        $response = $this->actingAs($manager)->get(route('employee.sales-manager.ops-board', ['partial' => 1]));

        $response->assertOk();
        $response->assertSee('متابعة الفريق', false);
        $response->assertSee('تحديث حي', false);
    }

    public function test_manager_can_approve_pending_attendance_via_http(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:15:00', 'Africa/Cairo'));
        ['manager' => $manager, 'rep' => $rep, 'schedule' => $schedule] = $this->seedTeam();

        $record = EmployeeAttendanceRecord::query()->create([
            'user_id' => $rep->id,
            'work_schedule_id' => $schedule->id,
            'work_date' => now()->toDateString(),
            'scheduled_start' => now()->copy()->setTime(9, 0),
            'scheduled_end' => now()->copy()->setTime(17, 0),
            'required_minutes' => 480,
            'status' => 'pending',
            'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_PENDING,
            'attendance_requested_at' => now(),
        ]);

        $response = $this->actingAs($manager)->post(
            route('employee.sales-manager.attendance.approve', $record),
            ['lateness_decision' => 'on_time']
        );

        $response->assertRedirect();
        $record->refresh();
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_APPROVED, $record->attendance_approval_status);
        $this->assertNotNull($record->clock_in_at);
        $this->assertFalse((bool) $record->is_late);
    }

    public function test_manager_can_reject_pending_attendance_via_http(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-04 09:15:00', 'Africa/Cairo'));
        ['manager' => $manager, 'rep' => $rep, 'schedule' => $schedule] = $this->seedTeam();

        $record = EmployeeAttendanceRecord::query()->create([
            'user_id' => $rep->id,
            'work_schedule_id' => $schedule->id,
            'work_date' => now()->toDateString(),
            'scheduled_start' => now()->copy()->setTime(9, 0),
            'scheduled_end' => now()->copy()->setTime(17, 0),
            'required_minutes' => 480,
            'status' => 'pending',
            'attendance_approval_status' => EmployeeAttendanceRecord::APPROVAL_PENDING,
            'attendance_requested_at' => now(),
        ]);

        $response = $this->actingAs($manager)->post(
            route('employee.sales-manager.attendance.reject', $record),
            ['reason' => 'غير موجود بالمكتب']
        );

        $response->assertRedirect();
        $record->refresh();
        $this->assertSame(EmployeeAttendanceRecord::APPROVAL_REJECTED, $record->attendance_approval_status);
        $this->assertNull($record->clock_in_at);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
