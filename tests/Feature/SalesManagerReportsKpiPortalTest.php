<?php

namespace Tests\Feature;

use App\Models\AdvertisingCampaign;
use App\Models\CampaignDailyReport;
use App\Models\EmployeeJob;
use App\Models\SalesDailyReport;
use App\Models\SalesKpiTarget;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesManagerReportsKpiPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureEmployeeWorkAccess::class,
            \App\Http\Middleware\PreventConcurrentSessions::class,
        ]);

        $this->mock(\App\Services\WhatsAppQueueService::class, function ($mock) {
            $mock->shouldReceive('pendingCount')->andReturn(0);
        });

        foreach ([
            'campaign_daily_reports', 'advertising_campaigns',
            'sales_activities', 'sales_leads', 'sales_daily_report_contacts',
            'sales_daily_reports', 'sales_kpi_targets', 'sales_team_members',
            'sales_teams', 'users', 'employee_jobs',
            'sales_shift_swap_requests', 'sales_shift_segments', 'sales_shift_plans',
            'employee_attendance_records', 'employee_presence_daily',
            'employee_presence_violations', 'leave_requests', 'branches',
            'work_schedules',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        \DB::table('branches')->insert([
            'name' => 'Main', 'is_active' => 1,
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
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
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
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('year_month', 7);
            $table->json('targets')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('report_date');
            $table->string('status')->default('draft');
            $table->unsignedInteger('calls_made')->nullable();
            $table->unsignedInteger('leads_qualified')->nullable();
            $table->unsignedInteger('bookings_from_leads')->nullable();
            $table->unsignedInteger('followups_done')->nullable();
            $table->unsignedInteger('messages_replied')->nullable();
            $table->text('activity_notes')->nullable();
            $table->text('productivity_notes')->nullable();
            $table->timestamp('manager_reviewed_at')->nullable();
            $table->unsignedBigInteger('manager_reviewed_by')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_daily_report_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_daily_report_id');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('advertising_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaign_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertising_campaign_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sales_daily_report_id')->nullable();
            $table->date('report_date');
            $table->unsignedInteger('new_messages')->default(0);
            $table->unsignedInteger('whatsapp_messages')->default(0);
            $table->unsignedInteger('messenger_messages')->default(0);
            $table->unsignedInteger('instagram_messages')->default(0);
            $table->unsignedInteger('qualified')->default(0);
            $table->unsignedInteger('unqualified')->default(0);
            $table->unsignedInteger('converted')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Minimal stubs for hub dashboard dependencies
        foreach ([
            'employee_attendance_records' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('work_date')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            },
            'employee_presence_daily' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('work_date')->nullable();
                $table->timestamps();
            },
            'employee_presence_violations' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('status')->default('open');
                $table->timestamps();
            },
            'leave_requests' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->string('status')->default('pending');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
            },
            'sales_shift_plans' => function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            },
            'sales_shift_segments' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sales_shift_plan_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
            },
            'sales_shift_swap_requests' => function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('requester_id');
                $table->unsignedBigInteger('partner_id')->nullable();
                $table->date('work_date')->nullable();
                $table->string('status', 20)->default('pending');
                $table->timestamps();
            },
        ] as $name => $callback) {
            Schema::create($name, $callback);
        }
    }

    /** @return array{manager: User, bd: User, rep: User, outsider: User, team: SalesTeam} */
    private function seedTeam(): array
    {
        $salesJob = EmployeeJob::query()->create(['name' => 'مبيعات', 'code' => 'sales', 'is_active' => true]);
        $mgrJob = EmployeeJob::query()->create(['name' => 'مدير', 'code' => 'sales_manager', 'is_active' => true]);
        $bdJob = EmployeeJob::query()->create(['name' => 'BD', 'code' => 'business_developer', 'is_active' => true]);

        $manager = User::query()->create([
            'name' => 'مدير تقارير',
            'email' => 'mgr-rep-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $mgrJob->id,
            'branch_id' => 1,
        ]);

        $bd = User::query()->create([
            'name' => 'بيزنس ديف',
            'email' => 'bd-rep-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $bdJob->id,
            'branch_id' => 1,
        ]);

        $rep = User::query()->create([
            'name' => 'موظف سيلز',
            'email' => 'rep-rep-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'branch_id' => 1,
        ]);

        $outsider = User::query()->create([
            'name' => 'خارج الفريق',
            'email' => 'out-rep-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $salesJob->id,
            'branch_id' => 1,
        ]);

        $team = SalesTeam::query()->create([
            'name' => 'فريق التقارير',
            'manager_id' => $manager->id,
            'is_active' => true,
        ]);

        SalesTeamMember::query()->create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
            'role' => 'member',
        ]);

        return compact('manager', 'bd', 'rep', 'outsider', 'team');
    }

    public function test_dashboard_shows_reports_hub_for_manager_and_bd(): void
    {
        ['manager' => $manager, 'bd' => $bd] = $this->seedTeam();

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.dashboard'))
            ->assertOk()
            ->assertSee('مركز التقارير والمؤشرات', false)
            ->assertSee('تقارير الكامبين', false)
            ->assertSee('أهداف KPIs', false);

        $this->actingAs($bd)
            ->get(route('employee.sales-manager.dashboard'))
            ->assertOk()
            ->assertSee('مركز التقارير والمؤشرات', false);
    }

    public function test_campaign_reports_page_and_export(): void
    {
        ['manager' => $manager, 'bd' => $bd, 'rep' => $rep, 'outsider' => $outsider] = $this->seedTeam();

        $campaign = AdvertisingCampaign::query()->create([
            'name' => 'حملة اختبار',
            'platform' => 'meta',
            'cost' => 1000,
            'is_active' => true,
        ]);

        CampaignDailyReport::query()->create([
            'advertising_campaign_id' => $campaign->id,
            'user_id' => $rep->id,
            'report_date' => today()->toDateString(),
            'new_messages' => 12,
            'whatsapp_messages' => 8,
            'messenger_messages' => 3,
            'instagram_messages' => 1,
            'qualified' => 4,
            'unqualified' => 2,
            'converted' => 1,
            'notes' => 'ملاحظة',
        ]);

        CampaignDailyReport::query()->create([
            'advertising_campaign_id' => $campaign->id,
            'user_id' => $outsider->id,
            'report_date' => today()->toDateString(),
            'new_messages' => 99,
            'qualified' => 50,
            'converted' => 10,
        ]);

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.campaign-reports.index'))
            ->assertOk()
            ->assertSee('حملة اختبار', false)
            ->assertSee('موظف سيلز', false)
            ->assertSee('12', false)
            ->assertDontSee('خارج الفريق', false);

        $this->actingAs($bd)
            ->get(route('employee.sales-manager.campaign-reports.index'))
            ->assertOk()
            ->assertSee('حملة اختبار', false);

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.campaign-reports.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($rep)
            ->get(route('employee.sales-manager.campaign-reports.index'))
            ->assertForbidden();
    }

    public function test_kpi_targets_view_and_save_for_team_member(): void
    {
        ['manager' => $manager, 'bd' => $bd, 'rep' => $rep, 'outsider' => $outsider] = $this->seedTeam();

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.kpi.targets', ['user_id' => $rep->id]))
            ->assertOk()
            ->assertSee('أهداف KPIs للفريق', false)
            ->assertSee('موظف سيلز', false);

        $this->actingAs($bd)
            ->get(route('employee.sales-manager.kpi.targets'))
            ->assertOk();

        $payload = array_merge(
            collect(config('sales_kpi.required_on_save', []))->mapWithKeys(
                fn ($key) => [$key => (float) (config('sales_kpi.defaults.'.$key) ?? 1)]
            )->all(),
            [
                'user_id' => $rep->id,
                'year_month' => now()->format('Y-m'),
            ]
        );

        $this->actingAs($manager)
            ->put(route('employee.sales-manager.kpi.targets.update'), $payload)
            ->assertRedirect(route('employee.sales-manager.kpi.targets', [
                'user_id' => $rep->id,
                'year_month' => now()->format('Y-m'),
            ]));

        $this->assertDatabaseHas('sales_kpi_targets', [
            'user_id' => $rep->id,
            'year_month' => now()->format('Y-m'),
        ]);

        $row = SalesKpiTarget::query()->where('user_id', $rep->id)->first();
        $this->assertNotNull($row);
        $this->assertEquals(100, (float) ($row->targets['people_worked_daily'] ?? 0));

        $this->actingAs($manager)
            ->put(route('employee.sales-manager.kpi.targets.update'), array_merge($payload, [
                'user_id' => $outsider->id,
            ]))
            ->assertSessionHasErrors('user_id');
    }

    public function test_daily_report_show_includes_campaign_entries(): void
    {
        ['manager' => $manager, 'rep' => $rep] = $this->seedTeam();

        $report = SalesDailyReport::query()->create([
            'user_id' => $rep->id,
            'report_date' => today()->toDateString(),
            'status' => SalesDailyReport::STATUS_SUBMITTED,
            'calls_made' => 20,
            'leads_qualified' => 3,
        ]);

        $campaign = AdvertisingCampaign::query()->create([
            'name' => 'كامبين اليوم',
            'platform' => 'meta',
            'cost' => 500,
            'is_active' => true,
        ]);

        CampaignDailyReport::query()->create([
            'advertising_campaign_id' => $campaign->id,
            'user_id' => $rep->id,
            'sales_daily_report_id' => $report->id,
            'report_date' => today()->toDateString(),
            'new_messages' => 7,
            'qualified' => 2,
            'converted' => 1,
        ]);

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.daily-reports.show', $report))
            ->assertOk()
            ->assertSee('تقارير الكامبين لهذا اليوم', false)
            ->assertSee('كامبين اليوم', false)
            ->assertSee('7', false);

        $report->refresh();
        $this->assertNotNull($report->manager_reviewed_at);
    }

    public function test_kpi_index_links_to_targets(): void
    {
        ['manager' => $manager] = $this->seedTeam();

        $this->actingAs($manager)
            ->get(route('employee.sales-manager.kpi.index'))
            ->assertOk()
            ->assertSee(route('employee.sales-manager.kpi.targets', [], false), false);
    }
}
