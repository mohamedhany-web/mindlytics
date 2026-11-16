<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\CrmSavedFilter;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

/** الفلاتر المحفوظة في قايمة العملاء، ودمج العملاء المكررين (docs/SALES_CRM.md). */
class SalesSavedFiltersAndMergeSmokeTest extends SmokeTestCase
{
    private User $rep;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->rep = User::where('employee_job_id', EmployeeJob::where('code', 'sales')->value('id'))->where('is_active', true)->firstOrFail();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        DB::table('sales_team_members')->updateOrInsert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id], ['role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_saved_filters_are_per_user_and_reopen_the_same_list(): void
    {
        $this->actingAs($this->rep);
        $this->get(route('employee.crm.leads.index', ['quick' => 'today', 'stage' => 'interested']))->assertOk()->assertSee('احفظ الفلتر الحالي');

        $this->post(route('employee.crm.saved-filters.store'), ['module' => 'leads', 'name' => 'Smoke مهتمين النهارده',
            'params' => ['quick' => 'today', 'stage' => 'interested', 'evil' => 'x', 'page' => 3]])
            ->assertRedirect(route('employee.crm.leads.index', ['quick' => 'today', 'stage' => 'interested']));
        $filter = CrmSavedFilter::where('user_id', $this->rep->id)->where('name', 'Smoke مهتمين النهارده')->firstOrFail();
        $this->assertSame(['quick' => 'today', 'stage' => 'interested'], $filter->params, 'only allowed list params are saved');

        $this->get(route('employee.crm.leads.index'))->assertOk()->assertSee('Smoke مهتمين النهارده')
            ->assertSee(e(route('employee.crm.leads.index', ['quick' => 'today', 'stage' => 'interested'])), false);
        // نفس الاسم بيحدّث الفلتر بدل ما يكرّره
        $this->post(route('employee.crm.saved-filters.store'), ['module' => 'leads', 'name' => 'Smoke مهتمين النهارده', 'params' => ['quick' => 'overdue']]);
        $this->assertSame(1, CrmSavedFilter::where('user_id', $this->rep->id)->where('name', 'Smoke مهتمين النهارده')->count());
        $this->assertSame(['quick' => 'overdue'], $filter->fresh()->params);
        // من غير فلتر: ما يتحفظش
        $this->post(route('employee.crm.saved-filters.store'), ['module' => 'leads', 'name' => 'Smoke فاضي', 'params' => []])->assertSessionHas('error');

        // فلاتر كل واحد ليه: المدير ما يشوفهاش وما يقدرش يمسحها
        $this->actingAs($this->manager)->get(route('employee.crm.leads.index'))->assertOk()->assertDontSee('Smoke مهتمين النهارده');
        $this->delete(route('employee.crm.saved-filters.destroy', $filter->id));
        $this->assertNotNull($filter->fresh());

        $this->actingAs($this->rep)->delete(route('employee.crm.saved-filters.destroy', $filter->id))->assertRedirect();
        $this->assertNull($filter->fresh());
    }

    public function test_rep_merges_own_duplicate_and_everything_moves(): void
    {
        $phone = '0109'.random_int(1000000, 9999999);
        $keep = $this->lead($this->rep, 'Smoke Merge Keep', $phone, ['notes' => 'ملاحظة الأصلي']);
        $dupe = $this->lead($this->rep, 'Smoke Merge Keep', '+2'.$phone, ['email' => 'smoke-merge@example.test', 'interest' => 'Data Diploma', 'notes' => 'ملاحظة المكرر']);
        $act = SalesActivity::create(['sales_lead_id' => $dupe->id, 'user_id' => $this->rep->id, 'type' => 'call', 'title' => 'Smoke dupe call', 'body' => 'x']);
        $case = \App\Domains\Sales\Models\CrmCase::create(['subject' => 'Smoke dupe case', 'status' => 'new', 'priority' => 'low', 'sales_lead_id' => $dupe->id, 'assigned_to' => $this->rep->id]);
        $doc = \App\Domains\Sales\Models\CrmDocument::create(['title' => 'Smoke dupe doc', 'disk' => 'local', 'path' => 'x.pdf', 'original_name' => 'x.pdf', 'mime' => 'application/pdf', 'size' => 1,
            'parent_type' => SalesLead::class, 'parent_id' => $dupe->id, 'assigned_to' => $this->rep->id]);

        $this->actingAs($this->rep);
        $this->get(route('employee.crm.leads.show', $keep->id))->assertOk()->assertSee(route('employee.crm.leads.merge', $keep->id), false);
        // الاقتراحات فيها المكرر (نفس الرقم بصيغة تانية)
        $this->get(route('employee.crm.leads.merge', $keep->id))->assertOk()->assertSee('نفس الرقم')->assertSee('#'.$dupe->id);
        $this->get(route('employee.crm.leads.merge', $keep->id).'?with='.$dupe->id)->assertOk()->assertSee('قارن واختار')->assertSee('smoke-merge@example.test');

        $this->post(route('employee.crm.leads.merge.store', $keep->id), ['other_id' => $dupe->id, 'keep' => 'this'])
            ->assertRedirect(route('employee.crm.leads.show', $keep->id))->assertSessionHasNoErrors();

        $dupe = SalesLead::withTrashed()->findOrFail($dupe->id);
        $this->assertNotNull($dupe->deleted_at);
        $this->assertSame($keep->id, (int) $dupe->merged_into_id);
        $this->assertSame($keep->id, (int) $act->fresh()->sales_lead_id);
        $this->assertSame($keep->id, (int) $case->fresh()->sales_lead_id);
        $this->assertSame($keep->id, (int) $doc->fresh()->parent_id);
        $keep = $keep->fresh();
        $this->assertSame('smoke-merge@example.test', $keep->email, 'empty fields are filled from the duplicate');
        $this->assertSame($phone, $keep->phone, 'the kept lead keeps its own phone');
        $this->assertStringContainsString('ملاحظة المكرر', (string) $keep->notes);
        $this->assertTrue(SalesActivity::where('sales_lead_id', $keep->id)->where('title', 'دمج عميل مكرر')->exists());
        $this->get(route('employee.crm.leads.index', ['q' => 'Smoke Merge Keep']))->assertOk()->assertDontSee('#'.$dupe->id.'<');
    }

    public function test_merge_guards(): void
    {
        $other = User::forceCreate(['name' => 'Smoke Merge Other Rep', 'email' => 'smoke-mo-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $this->rep->employee_job_id]);
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $other->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $mine = $this->lead($this->rep, 'Smoke Guard Mine', '0107'.random_int(1000000, 9999999));
        $theirs = $this->lead($other, 'Smoke Guard Theirs', '0106'.random_int(1000000, 9999999));
        $won = $this->lead($this->rep, 'Smoke Guard Won', '0105'.random_int(1000000, 9999999), ['won_confirmed_at' => now(), 'stage' => SalesLead::WON_STAGE]);

        // الموظف ما يشوفش عميل زميله ولا يدمجه
        $this->actingAs($this->rep)->get(route('employee.crm.leads.merge', $mine->id).'?with='.$theirs->id)->assertNotFound();
        $this->post(route('employee.crm.leads.merge.store', $mine->id), ['other_id' => $theirs->id, 'keep' => 'this'])->assertNotFound();
        // صفقة معتمدة ما تتدمجش في غيرها (ينفع العكس)
        $this->post(route('employee.crm.leads.merge.store', $mine->id), ['other_id' => $won->id, 'keep' => 'this'])->assertSessionHasErrors('merge');
        $this->assertNull(SalesLead::find($won->id)?->deleted_at);
        $this->assertNotNull(SalesLead::find($won->id));

        // المدير يدمج عميلين من موظفين مختلفين في فريقه
        $this->actingAs($this->manager)->get(route('employee.crm.leads.merge', $theirs->id).'?with='.$mine->id)->assertOk();
        $this->post(route('employee.crm.leads.merge.store', $theirs->id), ['other_id' => $mine->id, 'keep' => 'this'])->assertSessionHasNoErrors();
        $this->assertSame($theirs->id, (int) SalesLead::withTrashed()->find($mine->id)->merged_into_id);
    }

    private function lead(User $owner, string $name, string $phone, array $extra = []): SalesLead
    {
        return SalesLead::create($extra + ['name' => $name, 'phone' => $phone, 'source' => 'call', 'stage' => 'interested', 'priority' => 'normal',
            'assigned_to' => $owner->id, 'next_follow_up_at' => now()->addDay(), 'follow_up_channel' => 'call']);
    }
}
