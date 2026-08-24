<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\WorkshopController;
use App\Models\EmployeeJob;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Services\SalesLeadMovementPolicy;
use App\Services\SalesNotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WorkshopConvertToLeadsTest extends TestCase
{
    private User $admin;

    private User $repA;

    private User $repB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(SalesNotificationService::class, function ($mock) {
            $mock->shouldReceive('notifyWorkshopLeadsTransferred')->andReturnNull();
        });

        foreach ([
            'workshop_registrations',
            'workshops',
            'sales_lead_group_members',
            'sales_leads',
            'sales_lead_groups',
            'users',
            'employee_jobs',
        ] as $table) {
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
            $table->boolean('is_employee')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sales_lead_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_admin_managed')->default(false);
            $table->timestamps();
        });

        Schema::create('sales_lead_group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_group_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('sales_lead_group_id')->nullable();
            $table->string('import_batch')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('source', 64)->default('other');
            $table->string('stage', 32)->default('new_lead');
            $table->string('priority', 16)->nullable();
            $table->text('interest')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->string('follow_up_channel')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('mode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workshop_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workshop_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('attendance_mode')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('confirmed');
            $table->string('checkin_token')->nullable();
            $table->timestamp('converted_to_lead_at')->nullable();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->timestamps();
        });

        $salesJob = EmployeeJob::query()->create([
            'name' => 'مبيعات',
            'code' => 'sales',
            'is_active' => true,
        ]);

        $this->admin = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-ws-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]));

        $this->repA = User::withoutEvents(fn () => User::query()->create([
            'name' => 'سيلز أ',
            'email' => 'repa-ws-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $salesJob->id,
        ]));

        $this->repB = User::withoutEvents(fn () => User::query()->create([
            'name' => 'سيلز ب',
            'email' => 'repb-ws-'.uniqid().'@t.test',
            'password' => Hash::make('password'),
            'is_employee' => true,
            'is_active' => true,
            'employee_job_id' => $salesJob->id,
        ]));
    }

    public function test_convert_distributes_new_leads_evenly_and_attaches_group(): void
    {
        $workshop = $this->makeWorkshop();
        $group = SalesLeadGroup::query()->create([
            'name' => 'فريق الورشة',
            'is_admin_managed' => true,
            'created_by' => $this->admin->id,
        ]);

        foreach (range(1, 4) as $i) {
            WorkshopRegistration::query()->create([
                'workshop_id' => $workshop->id,
                'name' => "مسجل {$i}",
                'phone' => '0100000000'.$i,
                'email' => "reg{$i}@t.test",
                'attendance_mode' => 'online',
                'status' => 'confirmed',
                'checkin_token' => 'tok-'.$i,
            ]);
        }

        $response = $this->convert($workshop, [$this->repA->id, $this->repB->id], $group->id);

        $this->assertTrue(session()->has('success'), (string) session('error'));

        $leads = SalesLead::query()->where('import_batch', 'like', 'WS-'.$workshop->id.'-%')->get();
        $this->assertCount(4, $leads);
        $this->assertSame(2, $leads->where('assigned_to', $this->repA->id)->count());
        $this->assertSame(2, $leads->where('assigned_to', $this->repB->id)->count());
        $this->assertTrue($leads->every(fn (SalesLead $lead) => (int) $lead->sales_lead_group_id === (int) $group->id));
        $this->assertTrue($leads->every(fn (SalesLead $lead) => $lead->originKind() === 'workshop'));

        $group->refresh();
        $this->assertEqualsCanonicalizing(
            [$this->repA->id, $this->repB->id],
            $group->memberIds()->map(fn ($id) => (int) $id)->all()
        );

        $this->assertSame(4, $workshop->registrations()->whereNotNull('converted_to_lead_at')->count());
        $this->assertNotNull($response);
    }

    public function test_convert_links_existing_unassigned_lead_into_group(): void
    {
        $workshop = $this->makeWorkshop();
        $group = SalesLeadGroup::query()->create([
            'name' => 'مجموعة الربط',
            'created_by' => $this->admin->id,
        ]);

        $existing = SalesLead::query()->create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
            'name' => 'عميل قديم',
            'phone' => '01099998888',
            'email' => 'old@t.test',
            'source' => 'other',
            'stage' => 'new_lead',
        ]));

        WorkshopRegistration::query()->create([
            'workshop_id' => $workshop->id,
            'name' => 'عميل قديم',
            'phone' => '01099998888',
            'email' => 'old@t.test',
            'attendance_mode' => 'offline',
            'status' => 'confirmed',
            'checkin_token' => 'tok-old',
        ]);

        $this->convert($workshop, [$this->repA->id], $group->id);

        $existing->refresh();
        $this->assertSame($this->repA->id, (int) $existing->assigned_to);
        $this->assertSame($group->id, (int) $existing->sales_lead_group_id);
        $this->assertSame('workshop', $existing->originKind());
    }

    public function test_origin_kind_scope_filters_workshop_and_import(): void
    {
        SalesLead::query()->create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
            'assigned_to' => $this->repA->id,
            'name' => 'من الورشة',
            'phone' => '01111111111',
            'source' => 'event',
            'import_batch' => 'WS-9-20260824120000',
            'notes' => "[workshop:9]\nاسم الورشة: ورشة الفلترة",
            'stage' => 'new_lead',
        ]));
        SalesLead::query()->create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
            'assigned_to' => $this->repA->id,
            'name' => 'من الاستيراد',
            'phone' => '01222222222',
            'source' => 'other',
            'import_batch' => 'IMP-BATCH-1',
            'stage' => 'new_lead',
        ]));
        SalesLead::query()->create(app(SalesLeadMovementPolicy::class)->withCreateDefaults([
            'assigned_to' => $this->repA->id,
            'name' => 'يدوي',
            'phone' => '01333333333',
            'source' => 'whatsapp',
            'stage' => 'new_lead',
        ]));

        $workshopNames = SalesLead::query()->originKind('workshop')->pluck('name')->all();
        $importNames = SalesLead::query()->originKind('import')->pluck('name')->all();
        $manualNames = SalesLead::query()->originKind('manual')->pluck('name')->all();
        $fromNine = SalesLead::query()->fromWorkshop(9)->pluck('name')->all();

        $this->assertSame(['من الورشة'], $workshopNames);
        $this->assertSame(['من الاستيراد'], $importNames);
        $this->assertSame(['يدوي'], $manualNames);
        $this->assertSame(['من الورشة'], $fromNine);
    }

    public function test_ensure_members_merges_without_dropping_existing(): void
    {
        $group = SalesLeadGroup::query()->create([
            'name' => 'دمج الأعضاء',
            'assigned_to' => $this->repA->id,
        ]);
        $group->syncMembers([$this->repA->id]);
        $group->ensureMembers([$this->repB->id]);

        $this->assertEqualsCanonicalizing(
            [$this->repA->id, $this->repB->id],
            $group->memberIds()->map(fn ($id) => (int) $id)->all()
        );
    }

    public function test_workshop_convert_view_always_renders_group_options(): void
    {
        $blade = file_get_contents(resource_path('views/admin/workshops/show.blade.php'));

        $this->assertStringContainsString('@foreach(($salesLeadGroups ?? []) as $g)', $blade);
        $this->assertStringContainsString('retransfer_converted', $blade);
        $this->assertStringNotContainsString('refreshLeadGroups', $blade);
        $this->assertStringNotContainsString('repIds.every', $blade);
    }

    public function test_retransfer_moves_converted_leads_to_another_group(): void
    {
        $workshop = $this->makeWorkshop();
        $firstGroup = SalesLeadGroup::query()->create([
            'name' => 'المجموعة الأولى',
            'created_by' => $this->admin->id,
        ]);
        $secondGroup = SalesLeadGroup::query()->create([
            'name' => 'المجموعة الثانية',
            'created_by' => $this->admin->id,
        ]);

        foreach (range(1, 4) as $i) {
            WorkshopRegistration::query()->create([
                'workshop_id' => $workshop->id,
                'name' => "مسجل {$i}",
                'phone' => '0101000000'.$i,
                'email' => "retry{$i}@t.test",
                'attendance_mode' => 'online',
                'status' => 'confirmed',
                'checkin_token' => 'retry-'.$i,
            ]);
        }

        $this->convert($workshop, [$this->repA->id], $firstGroup->id);
        $this->assertSame(4, SalesLead::query()->where('sales_lead_group_id', $firstGroup->id)->count());

        session()->forget(['success', 'error']);
        $this->convert($workshop, [$this->repA->id, $this->repB->id], $secondGroup->id, true);

        $this->assertTrue(session()->has('success'), (string) session('error'));
        $this->assertSame(0, SalesLead::query()->where('sales_lead_group_id', $firstGroup->id)->count());
        $leads = SalesLead::query()->where('sales_lead_group_id', $secondGroup->id)->get();
        $this->assertCount(4, $leads);
        $this->assertSame(4, SalesLead::query()->count());
        $this->assertSame(2, $leads->where('assigned_to', $this->repA->id)->count());
        $this->assertSame(2, $leads->where('assigned_to', $this->repB->id)->count());
        $this->assertEqualsCanonicalizing(
            [$this->repA->id, $this->repB->id],
            $secondGroup->fresh()->memberIds()->map(fn ($id) => (int) $id)->all()
        );
    }

    public function test_second_convert_without_retransfer_does_not_duplicate(): void
    {
        $workshop = $this->makeWorkshop();
        $group = SalesLeadGroup::query()->create([
            'name' => 'مجموعة ثابتة',
            'created_by' => $this->admin->id,
        ]);

        WorkshopRegistration::query()->create([
            'workshop_id' => $workshop->id,
            'name' => 'مسجل واحد',
            'phone' => '01055556666',
            'email' => 'once@t.test',
            'attendance_mode' => 'online',
            'status' => 'confirmed',
            'checkin_token' => 'once-1',
        ]);

        $this->convert($workshop, [$this->repA->id], $group->id);
        $this->assertSame(1, SalesLead::query()->count());

        session()->forget(['success', 'error']);
        $this->convert($workshop, [$this->repB->id], $group->id);

        $this->assertTrue(session()->has('error'));
        $this->assertSame(1, SalesLead::query()->count());
        $this->assertSame($this->repA->id, (int) SalesLead::query()->first()->assigned_to);
    }

    private function convert(Workshop $workshop, array $assigneeIds, ?int $groupId, bool $retransfer = false)
    {
        Auth::login($this->admin);

        $payload = [
            'assigned_to' => $assigneeIds,
            'sales_lead_group_id' => $groupId,
        ];
        if ($retransfer) {
            $payload['retransfer_converted'] = 1;
        }

        $request = Request::create(
            '/admin/workshops/'.$workshop->id.'/convert-to-leads',
            'POST',
            $payload
        );
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => $this->admin);

        return app(WorkshopController::class)->convertRegistrationsToLeads($request, $workshop);
    }

    private function makeWorkshop(string $title = 'ورشة اختبار الترحيل'): Workshop
    {
        return Workshop::query()->create([
            'title' => $title,
            'slug' => 'ws-'.uniqid(),
            'mode' => 'hybrid',
            'is_active' => true,
        ]);
    }
}
