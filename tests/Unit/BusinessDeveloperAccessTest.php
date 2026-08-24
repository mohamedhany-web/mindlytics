<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureModeratorEmployee;
use App\Http\Middleware\EnsureSalesManager;
use App\Models\EmployeeJob;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Services\SalesTeamService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BusinessDeveloperAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['sales_leads', 'sales_team_members', 'sales_teams', 'users', 'employee_jobs'] as $table) {
            Schema::dropIfExists($table);
        }

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
            $table->boolean('is_employee')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_job_id')->nullable();
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
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('stage')->default('new_lead');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_middleware_aliases_are_registered(): void
    {
        $aliases = app('router')->getMiddleware();

        $this->assertSame(EnsureSalesManager::class, $aliases['sales.manager'] ?? null);
        $this->assertSame(EnsureModeratorEmployee::class, $aliases['moderator.employee'] ?? null);
        $this->assertArrayHasKey('sales.staff', $aliases);
        $this->assertArrayHasKey('employee.work', $aliases);
    }

    public function test_business_developer_has_sales_and_moderator_portal_access(): void
    {
        $bd = $this->makeUser('business_developer');
        $manager = $this->makeUser('sales_manager');
        $moderator = $this->makeUser('moderator');
        $rep = $this->makeUser('sales');

        $this->assertTrue($bd->isBusinessDeveloper());
        $this->assertTrue($bd->hasSalesManagerPortalAccess());
        $this->assertTrue($bd->hasModeratorPortalAccess());
        $this->assertTrue($bd->isSalesStaff());
        $this->assertFalse($bd->isSalesEmployee());
        $this->assertFalse($bd->isSalesManager());
        $this->assertFalse($bd->isModeratorEmployee());
        $this->assertTrue($bd->canManageModeratorResource((int) $moderator->id));

        $this->assertTrue($manager->hasSalesManagerPortalAccess());
        $this->assertFalse($manager->hasModeratorPortalAccess());
        $this->assertTrue($moderator->hasModeratorPortalAccess());
        $this->assertFalse($moderator->hasSalesManagerPortalAccess());
        $this->assertFalse($rep->hasSalesManagerPortalAccess());
        $this->assertFalse($rep->canManageModeratorResource((int) $moderator->id));
    }

    public function test_business_developer_sees_sales_reps_across_all_teams(): void
    {
        $bd = $this->makeUser('business_developer');
        $managerA = $this->makeUser('sales_manager', 'مدير أ');
        $managerB = $this->makeUser('sales_manager', 'مدير ب');
        $repA = $this->makeUser('sales', 'مندوب أ');
        $repB = $this->makeUser('sales', 'مندوب ب');

        $teamA = SalesTeam::query()->create([
            'name' => 'فريق أ',
            'manager_id' => $managerA->id,
            'is_active' => true,
        ]);
        $teamB = SalesTeam::query()->create([
            'name' => 'فريق ب',
            'manager_id' => $managerB->id,
            'is_active' => true,
        ]);

        SalesTeamMember::query()->create([
            'sales_team_id' => $teamA->id,
            'user_id' => $repA->id,
            'role' => 'member',
        ]);
        SalesTeamMember::query()->create([
            'sales_team_id' => $teamB->id,
            'user_id' => $repB->id,
            'role' => 'member',
        ]);

        $service = app(SalesTeamService::class);

        $this->actingAs($bd);

        $bdIds = $service->memberUserIds($teamA, $bd);
        $this->assertEqualsCanonicalizing([(int) $repA->id, (int) $repB->id], $bdIds);
        $this->assertCount(2, $service->memberRecords($bd, $teamA));

        $managerIds = $service->memberUserIds($teamA, $managerA);
        $this->assertSame([(int) $repA->id], $managerIds);
        $this->assertNotContains((int) $repB->id, $managerIds);

        $leadB = SalesLead::query()->create([
            'name' => 'عميل الفريق الآخر',
            'assigned_to' => $repB->id,
            'stage' => 'new_lead',
        ]);

        $this->assertTrue($service->canAccessLead($bd, $leadB));
        $this->assertFalse($service->canAccessLead($managerA, $leadB));
        $this->assertTrue($service->canAccessLead($managerB, $leadB));
    }

    private function makeUser(string $jobCode, ?string $name = null): User
    {
        $job = EmployeeJob::query()->firstOrCreate(
            ['code' => $jobCode],
            ['name' => $jobCode, 'is_active' => true]
        );

        return User::withoutEvents(fn () => User::query()->create([
            'name' => $name ?: $jobCode.'-'.uniqid(),
            'email' => $jobCode.'-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $job->id,
        ]));
    }
}
