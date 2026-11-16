<?php

namespace Tests\Unit;

use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesDailyReport;
use App\Domains\Sales\Models\SalesDailyReportContact;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesReportActualsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesReportActualsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'sales_daily_report_contacts',
            'sales_daily_reports',
            'sales_activities',
            'sales_leads',
            'users',
            'employee_jobs',
            'branches',
            'meta_social_agent_links',
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
            $table->string('role')->default('employee');
            $table->boolean('is_employee')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('stage')->default('new_lead');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->string('outcome')->nullable();
            $table->string('status')->nullable();
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
            $table->unsignedInteger('messages_replied')->default(0);
            $table->unsignedInteger('leads_qualified')->default(0);
            $table->unsignedInteger('bookings_from_leads')->default(0);
            $table->unsignedInteger('numbers_worked')->default(0);
            $table->unsignedInteger('followups_done')->default(0);
            $table->unsignedInteger('calls_made')->default(0);
            $table->unsignedInteger('meetings_held')->default(0);
            $table->unsignedInteger('calls_answered')->default(0);
            $table->timestamps();
        });

        Schema::create('sales_daily_report_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_daily_report_id');
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('interaction_type')->default('call');
            $table->string('client_status')->nullable();
            $table->text('client_problems')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_social_agent_links', function (Blueprint $table) {
            $table->id();
            $table->string('meta_user_id');
            $table->string('meta_user_name')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_inflated_report_flags_and_accuracy_drop(): void
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

        $report = SalesDailyReport::create([
            'user_id' => $employee->id,
            'report_date' => $day->toDateString(),
            'status' => SalesDailyReport::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'calls_made' => 40,
            'meetings_held' => 0,
            'followups_done' => 0,
            'messages_replied' => 0,
            'numbers_worked' => 0,
        ]);

        $diff = app(SalesReportActualsService::class)->compareReportToActuals($report->fresh(['contacts', 'user']));

        $this->assertSame(40, $diff['submitted']['calls_made']);
        $this->assertSame(1, $diff['actual']['calls_made']);
        $this->assertTrue($diff['flags']['inflated']);
        $this->assertTrue($diff['accuracy_pct'] < 50);
        $this->assertSame(1, $diff['people_contacted']);
    }

    public function test_contact_name_mismatch_between_report_and_actual(): void
    {
        [$employee, $actualLead] = $this->seedRepAndLead('أحمد فعلي', '01001112233');
        $reportOnlyLead = SalesLead::create([
            'name' => 'محمود تقرير فقط',
            'phone' => '01009998877',
            'stage' => 'new_lead',
            'assigned_to' => $employee->id,
        ]);
        $day = Carbon::today();

        SalesActivity::create([
            'sales_lead_id' => $actualLead->id,
            'user_id' => $employee->id,
            'type' => 'call',
            'outcome' => 'interested',
            'created_at' => $day->copy()->setTime(11, 0),
            'updated_at' => $day->copy()->setTime(11, 0),
        ]);

        $report = SalesDailyReport::create([
            'user_id' => $employee->id,
            'report_date' => $day->toDateString(),
            'status' => SalesDailyReport::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'calls_made' => 1,
            'meetings_held' => 0,
            'followups_done' => 0,
            'messages_replied' => 0,
            'numbers_worked' => 1,
        ]);

        SalesDailyReportContact::create([
            'sales_daily_report_id' => $report->id,
            'sales_lead_id' => $reportOnlyLead->id,
            'contact_name' => $reportOnlyLead->name,
            'contact_phone' => $reportOnlyLead->phone,
            'interaction_type' => 'call',
            'client_status' => 'مهتم',
            'client_problems' => 'سعر',
        ]);

        $diff = app(SalesReportActualsService::class)->compareReportToActuals($report->fresh(['contacts', 'user']));

        $this->assertTrue($diff['flags']['contacts_mismatch']);
        $this->assertCount(1, $diff['contacts']['report_only']);
        $this->assertCount(1, $diff['contacts']['actual_only']);
        $this->assertSame('محمود تقرير فقط', $diff['contacts']['report_only'][0]['name']);
        $this->assertSame('أحمد فعلي', $diff['contacts']['actual_only'][0]['name']);
    }

    public function test_meta_link_status_detected(): void
    {
        [$employee] = $this->seedRepAndLead();
        \DB::table('meta_social_agent_links')->insert([
            'meta_user_id' => 'meta-123',
            'meta_user_name' => 'Sales Agent',
            'user_id' => $employee->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $status = app(SalesReportActualsService::class)->metaLinkStatus($employee);

        $this->assertTrue($status['linked']);
        $this->assertSame('Sales Agent', $status['label']);
    }

    /**
     * @return array{0: User, 1: SalesLead}
     */
    private function seedRepAndLead(string $name = 'عميل اختبار', string $phone = '01001234567'): array
    {
        $jobId = \DB::table('employee_jobs')->insertGetId([
            'name' => 'Sales',
            'code' => 'sales',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $employee = User::create([
            'name' => 'موظف سيلز',
            'email' => 'rep-actuals-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $jobId,
            'branch_id' => 1,
        ]);

        $lead = SalesLead::create([
            'name' => $name,
            'phone' => $phone,
            'stage' => 'new_lead',
            'assigned_to' => $employee->id,
        ]);

        return [$employee, $lead];
    }
}
