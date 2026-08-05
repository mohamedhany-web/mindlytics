<?php

namespace Tests\Unit;

use App\Models\EmployeeJob;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesManagerDailyReview;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Services\SalesManagerDailyScorecardService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesManagerDailyScorecardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'employee_salary_deductions',
            'sales_manager_daily_reviews',
            'sales_activities',
            'sales_leads',
            'sales_daily_reports',
            'sales_team_members',
            'sales_teams',
            'employee_attendance_records',
            'employee_presence_daily',
            'users',
            'employee_jobs',
            'branches',
            'sales_kpi_targets',
            'leave_requests',
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
            'name' => 'Main',
            'slug' => 'main',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
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
            $table->boolean('is_employee')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->unsignedTinyInteger('weekly_off_day')->nullable();
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('work_mode', 20)->default('online');
            $table->timestamps();
        });

        Schema::create('sales_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('manager_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('stage')->default('new_lead');
            $table->string('source')->nullable();
            $table->string('import_batch')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->decimal('expected_value', 12, 2)->nullable();
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->string('payment_txn_ref')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('won_confirmed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('outcome')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('report_date');
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->string('status')->default('pending');
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('late_penalty_waived')->default(false);
            $table->unsignedInteger('worked_minutes')->nullable();
            $table->unsignedInteger('required_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_manager_daily_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_team_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('manager_id');
            $table->date('work_date');
            $table->string('status', 20)->default('draft');
            $table->decimal('verified_score', 5, 1)->nullable();
            $table->json('score_snapshot')->nullable();
            $table->string('recommendation', 40)->default('none');
            $table->decimal('proposed_deduction_amount', 10, 2)->nullable();
            $table->text('manager_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'work_date']);
        });

        Schema::create('employee_salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('deduction_number')->nullable();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('applied');
            $table->date('deduction_date')->nullable();
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
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function test_unlinked_activity_does_not_count_as_verified_call(): void
    {
        [$manager, $rep, $team] = $this->seedTeam();
        $day = Carbon::today();

        SalesActivity::create([
            'sales_lead_id' => null,
            'user_id' => $rep->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(10, 0),
            'updated_at' => $day->copy()->setTime(10, 0),
        ]);

        $lead = SalesLead::create([
            'name' => 'Lead A',
            'phone' => '01000000001',
            'stage' => 'new_lead',
            'assigned_to' => $rep->id,
        ]);

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $rep->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(11, 0),
            'updated_at' => $day->copy()->setTime(11, 0),
        ]);

        $row = app(SalesManagerDailyScorecardService::class)->buildEmployeeDay(
            $rep,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay(),
            $team
        );

        $this->assertSame(1, (int) $row['sos']['call_attempts_daily']);
        $this->assertSame(1, (int) $row['sos']['calls_answered_daily']);
        $this->assertIsFloat($row['verified_score']);
        $this->assertArrayHasKey('results', $row['pillars']);
        $this->assertArrayHasKey('activity', $row['pillars']);
        $this->assertArrayHasKey('attendance', $row['pillars']);
    }

    public function test_manager_review_saves_snapshot_without_creating_deduction(): void
    {
        [$manager, $rep, $team] = $this->seedTeam();
        $day = Carbon::today();
        $service = app(SalesManagerDailyScorecardService::class);

        $row = $service->buildEmployeeDay($rep, $day->copy()->startOfDay(), $day->copy()->endOfDay(), $team);

        $before = EmployeeSalaryDeduction::count();

        $review = $service->saveReview($manager, $team, $rep, $day, $row, [
            'status' => SalesManagerDailyReview::STATUS_APPROVED,
            'recommendation' => 'deduction',
            'proposed_deduction_amount' => 50,
            'manager_notes' => 'مراجعة اختبار',
        ]);

        $this->assertTrue($review->isApproved());
        $this->assertSame(50.0, (float) $review->proposed_deduction_amount);
        $this->assertIsArray($review->score_snapshot);
        $this->assertSame($row['verified_score'], (float) $review->verified_score);
        $this->assertSame($before, EmployeeSalaryDeduction::count());
    }

    public function test_approved_snapshot_is_frozen_after_new_activity(): void
    {
        [$manager, $rep, $team] = $this->seedTeam();
        $day = Carbon::today();
        $service = app(SalesManagerDailyScorecardService::class);

        $row = $service->buildEmployeeDay($rep, $day->copy()->startOfDay(), $day->copy()->endOfDay(), $team);
        $review = $service->saveReview($manager, $team, $rep, $day, $row, [
            'status' => SalesManagerDailyReview::STATUS_APPROVED,
            'recommendation' => 'none',
        ]);
        $frozen = (float) $review->verified_score;

        $lead = SalesLead::create([
            'name' => 'Late Lead',
            'assigned_to' => $rep->id,
            'stage' => 'new_lead',
        ]);
        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $rep->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(15, 0),
            'updated_at' => $day->copy()->setTime(15, 0),
        ]);

        $again = $service->buildEmployeeDay(
            $rep,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay(),
            $team,
            $review->fresh()
        );

        $this->assertSame($frozen, (float) $again['verified_score']);
    }

    /**
     * @return array{0: User, 1: User, 2: SalesTeam}
     */
    private function seedTeam(): array
    {
        $mgrJob = EmployeeJob::create(['name' => 'Sales Manager', 'code' => 'sales_manager']);
        $repJob = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales']);

        $manager = User::create([
            'name' => 'Manager',
            'email' => 'mgr-score-'.uniqid().'@test.com',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => now()->subMonths(2)->toDateString(),
            'employee_job_id' => $mgrJob->id,
        ]);

        $rep = User::create([
            'name' => 'Rep',
            'email' => 'rep-score-'.uniqid().'@test.com',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => now()->subMonth()->toDateString(),
            'employee_job_id' => $repJob->id,
            'weekly_off_day' => 5,
        ]);

        $team = SalesTeam::create([
            'name' => 'Team A',
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);

        SalesTeamMember::create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
            'role' => 'member',
        ]);

        return [$manager, $rep, $team];
    }
}
