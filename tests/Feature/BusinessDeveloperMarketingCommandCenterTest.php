<?php

namespace Tests\Feature;

use App\Models\EmployeeJob;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMarketingPlan;
use App\Models\ModeratorMarketingPlatform;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BusinessDeveloperMarketingCommandCenterTest extends TestCase
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
            'moderator_mkt_calendar_events',
            'moderator_mkt_platforms',
            'moderator_mkt_plans',
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

        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
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
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('moderator_mkt_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moderator_id');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('goals')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('design_task_cycle_id')->nullable();
            $table->timestamps();
        });

        Schema::create('moderator_mkt_platforms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('platform_key');
            $table->string('custom_label')->nullable();
            $table->string('profile_url')->nullable();
            $table->text('strategy_notes')->nullable();
            $table->text('cadence_notes')->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('moderator_mkt_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('content_type')->nullable();
            $table->unsignedBigInteger('assigned_employee_id')->nullable();
            $table->unsignedBigInteger('employee_task_id')->nullable();
            $table->boolean('requires_confirmation')->default(false);
            $table->timestamp('execution_confirmed_at')->nullable();
            $table->unsignedBigInteger('execution_confirmed_by')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->unsignedBigInteger('execution_penalty_deduction_id')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('design_task_cycle_id')->nullable();
            $table->timestamps();
        });
    }

    private function makeUser(string $jobCode): User
    {
        $job = EmployeeJob::query()->firstOrCreate(
            ['code' => $jobCode],
            ['name' => $jobCode, 'is_active' => true]
        );

        return User::query()->create([
            'name' => ucfirst($jobCode).' '.uniqid(),
            'email' => $jobCode.'-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'employee_job_id' => $job->id,
            'branch_id' => 1,
        ]);
    }

    public function test_bd_sees_command_center_with_all_plans(): void
    {
        $bd = $this->makeUser('business_developer');
        $moderator = $this->makeUser('moderator');
        $manager = $this->makeUser('sales_manager');

        $plan = ModeratorMarketingPlan::query()->create([
            'moderator_id' => $moderator->id,
            'title' => 'خطة رمضان',
            'summary' => 'حملة محتوى قوية',
            'status' => 'active',
        ]);

        ModeratorMarketingPlatform::query()->create([
            'plan_id' => $plan->id,
            'platform_key' => 'instagram',
            'color_hex' => '#e11d48',
            'sort_order' => 1,
        ]);

        ModeratorMarketingCalendarEvent::query()->create([
            'plan_id' => $plan->id,
            'title' => 'ريلز إطلاق',
            'starts_at' => now()->addDay(),
            'status' => 'scheduled',
            'requires_confirmation' => true,
        ]);

        $this->actingAs($bd)
            ->get(route('employee.business-developer.marketing'))
            ->assertOk()
            ->assertSee('مركز التسويق التنفيذي', false)
            ->assertSee('خطة رمضان', false)
            ->assertSee($moderator->name, false)
            ->assertSee('ريلز إطلاق', false);

        $this->actingAs($manager)
            ->get(route('employee.business-developer.marketing'))
            ->assertForbidden();
    }

    public function test_bd_marketing_plans_index_lists_all_moderators_plans(): void
    {
        $bd = $this->makeUser('business_developer');
        $moderator = $this->makeUser('moderator');

        ModeratorMarketingPlan::query()->create([
            'moderator_id' => $moderator->id,
            'title' => 'خطة عامة BD',
            'status' => 'active',
        ]);

        $this->actingAs($bd)
            ->get(route('employee.marketing-plans.index'))
            ->assertOk()
            ->assertSee('مركز التسويق التنفيذي', false)
            ->assertSee('خطة عامة BD', false)
            ->assertSee($moderator->name, false);
    }
}
