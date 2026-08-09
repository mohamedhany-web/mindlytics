<?php

namespace Tests\Unit;

use App\Models\EmployeeJob;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesActivity;
use App\Models\SalesDailyKpiPenalty;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesDailyKpiPenaltyService;
use App\Services\SalesDailyReportService;
use App\Services\SalesDailyResultService;
use App\Services\SalesNotificationService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesDailyKpiPenaltyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'notifications',
            'sales_daily_kpi_penalties',
            'employee_salary_deductions',
            'employee_agreements',
            'sales_activities',
            'sales_leads',
            'sales_kpi_targets',
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
            $table->unsignedBigInteger('employee_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('stage')->default('new_lead');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('won_confirmed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('outcome')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('year_month', 7);
            $table->json('targets')->nullable();
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

        Schema::create('sales_daily_kpi_penalties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->string('metric_key', 64);
            $table->decimal('actual', 10, 2)->default(0);
            $table->decimal('target', 10, 2)->default(0);
            $table->decimal('pct', 6, 2)->default(0);
            $table->unsignedBigInteger('deduction_id')->nullable();
            $table->timestamp('waived_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'work_date', 'metric_key']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('priority')->nullable();
            $table->string('audience')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        config([
            'sales_kpi.defaults.meetings_daily' => 0,
            'sales_kpi.defaults.discovery_sessions_daily' => 0,
            'sales_kpi.defaults.call_attempts_daily' => 50,
            'sales_kpi.defaults.calls_answered_daily' => 20,
            'sales_kpi.defaults.qualified_conversations_daily' => 8,
            'sales_kpi.defaults.proposals_daily' => 2,
            'sales_kpi.defaults.paid_enrollments_daily' => 1,
            'sales_kpi.daily_kpi_penalty.enabled' => true,
            'sales_kpi.daily_kpi_penalty.threshold_pct' => 70,
            'sales_kpi.daily_kpi_penalty.work_days_only' => false,
            'sales_kpi.daily_kpi_penalty.penalty_effective_from' => '2026-01-01',
            'sales_kpi.daily_kpi_penalty.metrics' => [
                'call_attempts_daily' => [
                    'amount' => 30,
                    'title' => 'غرامة KPI يومي — محاولات اتصال',
                ],
                'calls_answered_daily' => [
                    'amount' => 25,
                    'title' => 'غرامة KPI يومي — مكالمات تم الرد',
                ],
            ],
            'sales_daily_report.work_days_only' => false,
        ]);
    }

    private function makeRep(): User
    {
        $job = EmployeeJob::create(['name' => 'Sales', 'code' => 'sales', 'is_active' => true]);

        return User::create([
            'name' => 'Rep',
            'email' => 'rep-kpi-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'hire_date' => '2026-01-01',
            'employee_job_id' => $job->id,
            'branch_id' => 1,
            'weekly_off_day' => 5, // Friday off so weekdays work
        ]);
    }

    public function test_zero_meeting_targets_are_skipped_from_comparison(): void
    {
        $rep = $this->makeRep();
        $day = Carbon::parse('2026-08-06'); // Wednesday (past)

        $comparison = app(SalesDailyResultService::class)->comparisonFor($rep, $day);
        $keys = collect($comparison['lines'])->pluck('key')->all();

        $this->assertNotContains('discovery_sessions_daily', $keys);
        $this->assertContains('call_attempts_daily', $keys);
        $this->assertContains('paid_enrollments_daily', $keys);
    }

    public function test_kpi_miss_creates_deduction_and_is_idempotent(): void
    {
        $rep = $this->makeRep();
        $day = Carbon::parse('2026-08-06')->startOfDay(); // past Wednesday

        $this->mock(SalesNotificationService::class, function ($m) {
            $m->shouldReceive('notifyDailyKpiPenalty')->andReturnNull();
        });

        $service = app(SalesDailyKpiPenaltyService::class);

        $created = $service->applyForDate($rep, $day);
        $this->assertNotEmpty($created);
        $this->assertTrue(
            collect($created)->contains(fn ($d) => str_contains((string) $d->notes, 'call_attempts_daily'))
        );

        $countAfterFirst = EmployeeSalaryDeduction::query()->where('employee_id', $rep->id)->count();
        $this->assertGreaterThan(0, $countAfterFirst);

        $createdAgain = $service->applyForDate($rep, $day);
        $this->assertSame([], $createdAgain);
        $this->assertSame(
            $countAfterFirst,
            EmployeeSalaryDeduction::query()->where('employee_id', $rep->id)->count()
        );
        $this->assertSame(
            $countAfterFirst,
            SalesDailyKpiPenalty::query()->where('user_id', $rep->id)->whereDate('work_date', $day)->count()
        );
    }

    public function test_meeting_metric_is_not_chargeable(): void
    {
        $chargeable = array_keys(app(SalesDailyKpiPenaltyService::class)->chargeableMetrics());

        $this->assertNotContains('discovery_sessions_daily', $chargeable);
        $this->assertNotContains('meetings_daily', $chargeable);
        $this->assertContains('call_attempts_daily', $chargeable);
    }

    public function test_daily_report_metrics_match_sos_from_linked_crm_activity(): void
    {
        $rep = $this->makeRep();
        $day = Carbon::parse('2026-08-06')->startOfDay();

        $lead = SalesLead::create([
            'name' => 'Lead',
            'phone' => '01000000001',
            'stage' => 'new_lead',
            'assigned_to' => $rep->id,
        ]);

        $this->createActivityAt($rep->id, $lead->id, 'call', 'interested', $day->copy()->setTime(10, 0));
        $this->createActivityAt($rep->id, null, 'call', 'interested', $day->copy()->setTime(11, 0));
        $this->createActivityAt($rep->id, $lead->id, 'meeting', null, $day->copy()->setTime(12, 0));

        $sos = app(SalesDailyResultService::class)->metricsFor(
            (int) $rep->id,
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay()
        );
        $built = app(SalesDailyReportService::class)->buildFromActivities($rep, $day);
        $comparison = app(SalesDailyReportService::class)->kpiComparisonForReport($rep, [], $day);

        $this->assertSame(1, $sos['call_attempts_daily']);
        $this->assertSame(1, $sos['calls_answered_daily']);
        $this->assertSame(1, $sos['discovery_sessions_daily']);
        $this->assertSame(1, $built['metrics']['calls_made']);
        $this->assertSame(1, $built['metrics']['calls_answered']);
        $this->assertSame(1, $built['metrics']['meetings_held']);
        $this->assertSame('crm_sos', $comparison['source']);
        $this->assertNotContains(
            'discovery_sessions_daily',
            collect($comparison['lines'])->pluck('key')->all()
        );
    }

    private function createActivityAt(int $userId, ?int $leadId, string $type, ?string $outcome, Carbon $at): void
    {
        $row = SalesActivity::create([
            'sales_lead_id' => $leadId,
            'user_id' => $userId,
            'type' => $type,
            'outcome' => $outcome,
        ]);
        $row->timestamps = false;
        $row->forceFill([
            'created_at' => $at,
            'updated_at' => $at,
        ])->save();
    }
}
