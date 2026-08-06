<?php

namespace Tests\Unit;

use App\Models\EmployeeJob;
use App\Models\SalesActivity;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesCrmComplianceService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesCrmComplianceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'sales_activities',
            'sales_daily_reports',
            'sales_leads',
            'leave_requests',
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
            'name' => 'Main',
            'slug' => 'main',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
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

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
            $table->string('profile_type')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('field_domain')->nullable();
            $table->string('experience_level')->nullable();
            $table->string('course_motivation')->nullable();
            $table->string('start_preference')->nullable();
            $table->boolean('can_pay')->nullable();
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
            $table->string('recording_url')->nullable();
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
            $table->unsignedInteger('calls_made')->default(0);
            $table->unsignedInteger('meetings_held')->default(0);
            $table->unsignedInteger('followups_done')->default(0);
            $table->timestamps();
        });
    }

    public function test_unlinked_activity_does_not_count_as_crm_usage(): void
    {
        [$employee, $lead] = $this->seedRepAndLead();
        $day = Carbon::today();

        SalesActivity::create([
            'sales_lead_id' => null,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(10, 0),
            'updated_at' => $day->copy()->setTime(10, 0),
        ]);

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(11, 0),
            'updated_at' => $day->copy()->setTime(11, 0),
        ]);

        $row = app(SalesCrmComplianceService::class)->buildEmployee(
            $employee,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay()
        );

        $this->assertSame(1, (int) $row['usage']['crm_activities']);
        $this->assertSame(1, (int) $row['usage']['leads_touched']);
    }

    public function test_inflated_daily_report_lowers_accuracy(): void
    {
        [$employee, $lead] = $this->seedRepAndLead();
        $day = Carbon::today();

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => 'no_answer',
            'created_at' => $day->copy()->setTime(10, 0),
            'updated_at' => $day->copy()->setTime(10, 0),
        ]);

        SalesDailyReport::create([
            'user_id' => $employee->id,
            'report_date' => $day->toDateString(),
            'status' => SalesDailyReport::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'calls_made' => 40,
            'meetings_held' => 0,
            'followups_done' => 0,
        ]);

        $row = app(SalesCrmComplianceService::class)->buildEmployee(
            $employee,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay()
        );

        $this->assertSame(1, (int) $row['report']['verified_calls']);
        $this->assertSame(40, (int) $row['report']['claimed_calls']);
        $this->assertGreaterThan(0, (int) $row['report']['inflated_days']);
        $this->assertTrue((float) $row['report']['accuracy_pct'] < 50);
        $this->assertTrue(collect($row['exceptions'])->contains(fn ($e) => $e['code'] === 'report_inflated'));
    }

    public function test_lead_timeline_includes_stage_changes_in_order(): void
    {
        [$employee, $lead] = $this->seedRepAndLead();

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'stage_change',
            'meta' => ['from' => 'connected', 'to' => 'qualification'],
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        $timeline = app(SalesCrmComplianceService::class)->leadTimeline($lead->fresh());

        $this->assertSame(2, (int) $timeline['steps_count']);
        $this->assertSame(1, (int) $timeline['stage_changes_count']);
        $this->assertSame('call', $timeline['steps'][0]['type']);
        $this->assertSame('stage_change', $timeline['steps'][1]['type']);
        $this->assertSame('qualification', $timeline['steps'][1]['to_stage']);
    }

    public function test_calls_without_outcome_reduce_quality(): void
    {
        [$employee, $lead] = $this->seedRepAndLead();
        $day = Carbon::today();

        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => null,
            'created_at' => $day->copy()->setTime(9, 0),
            'updated_at' => $day->copy()->setTime(9, 0),
        ]);
        SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => '',
            'created_at' => $day->copy()->setTime(10, 0),
            'updated_at' => $day->copy()->setTime(10, 0),
        ]);

        $row = app(SalesCrmComplianceService::class)->buildEmployee(
            $employee,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay()
        );

        $this->assertSame(2, (int) $row['quality']['calls_total']);
        $this->assertSame(0.0, (float) $row['quality']['calls_with_outcome_pct']);
        $this->assertTrue(collect($row['exceptions'])->contains(fn ($e) => $e['code'] === 'call_no_outcome'));
    }

    /**
     * @return array{0: User, 1: SalesLead}
     */
    private function seedRepAndLead(): array
    {
        $job = EmployeeJob::create([
            'title' => 'مبيعات',
            'name' => 'Sales',
            'code' => 'sales',
            'is_active' => true,
        ]);

        $employee = User::create([
            'name' => 'Rep One',
            'email' => 'rep-compliance-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => now()->subMonths(2)->toDateString(),
            'weekly_off_day' => 5, // Friday
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);

        $lead = SalesLead::create([
            'name' => 'Client A',
            'phone' => '01000000000',
            'stage' => 'connected',
            'assigned_to' => $employee->id,
        ]);

        return [$employee, $lead];
    }
}
