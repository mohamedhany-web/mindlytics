<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Wallet;
use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * شاشات CRM الموحّدة (docs/SALES_CRM.md) على نسخة MySQL، كل شيء يُتراجع عنه:
 * كل صفحة لكل دور، نطاق البيانات (الموظف عملاؤه، المدير فريقه، الأدمن الكل)،
 * روابط الشريط العلوي لا تؤدي لـ 403/404/500، والإجراءات (تحويل جماعي، تصدير، تحويل لطالب).
 */
class SalesCrmSmokeTest extends SmokeTestCase
{
    private User $admin;

    private User $rep;

    private User $outsider;

    private User $manager;

    private SalesLead $repLead;

    private SalesLead $outsiderLead;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $salesJob = EmployeeJob::where('code', 'sales')->firstOrFail();
        $this->rep = User::where('employee_job_id', $salesJob->id)->where('is_active', true)->firstOrFail();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();

        // الموظف في فريق المدير؛ موظف آخر خارج الفريق
        $team = SalesTeam::where('manager_id', $this->manager->id)->firstOrFail();
        if (! DB::table('sales_team_members')->where('sales_team_id', $team->id)->where('user_id', $this->rep->id)->exists()) {
            DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->outsider = User::forceCreate([
            'name' => 'Smoke Outsider Rep', 'email' => 'smoke-out-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $salesJob->id,
        ]);

        $this->repLead = $this->lead($this->rep, 'Smoke CRM Rep Lead', ['stage' => 'offer_sent', 'offer_price' => 2500, 'expected_value' => 2500]);
        $this->outsiderLead = $this->lead($this->outsider, 'Smoke CRM Outsider Lead');
    }

    public function test_rep_sees_every_crm_page_and_only_own_leads(): void
    {
        $this->actingAs($this->rep);
        $home = $this->page('/employee/crm');
        $home->assertSee('يومي')->assertSee('هدفي النهارده');

        foreach (['all', 'today', 'overdue', 'new', 'stale', 'meta'] as $quick) {
            $this->page('/employee/crm/leads?quick='.$quick);
        }
        $this->page('/employee/crm/leads?q=Smoke%20CRM&sort=name&dir=desc')->assertSee('Smoke CRM Rep Lead')->assertDontSee('Smoke CRM Outsider Lead');
        foreach (['open', 'won_pending', 'won_month', 'refunded', 'all'] as $quick) {
            $this->page('/employee/crm/deals?quick='.$quick);
        }

        $this->page('/employee/crm/leads/'.$this->repLead->id)->assertSee('Smoke CRM Rep Lead')->assertSee('تسجيل سريع');
        $this->page('/employee/crm/deals/'.$this->repLead->id)->assertSee('المدفوعات');

        $this->get('/employee/crm/leads/'.$this->outsiderLead->id)->assertNotFound();
        $this->get('/employee/crm/deals/'.$this->outsiderLead->id)->assertNotFound();
        $this->get('/employee/crm/leads/'.$this->repLead->id.'/convert')->assertForbidden();
        $this->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$this->repLead->id], 'to_user_id' => $this->outsider->id])->assertForbidden();

        $this->assertNavLinksWork($home);
    }

    public function test_rep_quick_actions_from_the_record_return_to_crm(): void
    {
        $this->actingAs($this->rep);
        $url = '/employee/crm/leads/'.$this->repLead->id;
        $page = $this->page($url)->getContent();
        $this->assertStringContainsString('name="redirect_to" value="'.url($url).'"', $page, 'CRM forms must bring the rep back to the CRM record');
        $this->post('/employee/sales/leads/'.$this->repLead->id.'/quick-activity', ['type' => 'call', 'outcome' => 'interested', 'redirect_to' => url($url)])
            ->assertRedirect(url($url));
        $this->post('/employee/sales/leads/'.$this->repLead->id.'/next-follow', [
            'next_follow_up_at' => now()->addDays(2)->format('Y-m-d H:i'), 'follow_up_channel' => 'whatsapp', 'note' => 'smoke', 'redirect_to' => url($url),
        ])->assertRedirect(url($url));
        $this->page($url)->assertSee('مكالمة');
    }

    public function test_manager_sees_team_only_and_can_transfer_and_convert(): void
    {
        $this->actingAs($this->manager);
        $home = $this->page('/employee/crm');
        $home->assertSee('الفريق النهارده')->assertSee('مستني قرارك');

        $this->page('/employee/crm/leads?quick=all')->assertSee('Smoke CRM Rep Lead')->assertDontSee('Smoke CRM Outsider Lead');
        $this->page('/employee/crm/leads/'.$this->repLead->id)->assertSee('تحويل لموظف آخر');
        $this->get('/employee/crm/leads/'.$this->outsiderLead->id)->assertNotFound();
        $this->page('/employee/crm/deals?quick=all');

        // تحويل جماعي داخل الفريق
        $rep2 = User::forceCreate([
            'name' => 'Smoke Team Rep 2', 'email' => 'smoke-t2-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2015'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $this->rep->employee_job_id,
        ]);
        DB::table('sales_team_members')->insert(['sales_team_id' => SalesTeam::where('manager_id', $this->manager->id)->value('id'), 'user_id' => $rep2->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        $extra = $this->lead($this->rep, 'Smoke CRM Bulk Lead');
        $this->from('/employee/crm/leads')->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$extra->id, $this->outsiderLead->id], 'to_user_id' => $rep2->id, 'reason' => 'smoke'])
            ->assertRedirect('/employee/crm/leads')->assertSessionHasNoErrors();
        $this->assertSame($rep2->id, (int) $extra->fresh()->assigned_to);
        $this->assertSame($this->outsider->id, (int) $this->outsiderLead->fresh()->assigned_to, 'a lead outside the team must not move');
        $this->post('/employee/crm/leads/bulk', ['action' => 'transfer', 'ids' => [$extra->id], 'to_user_id' => $this->outsider->id])->assertSessionHasErrors('to_user_id');

        // تحويل لطالب: طلب معتمد لطالب بنفس الرقم بصيغة مختلفة
        $digits = (string) random_int(10000000, 99999999);
        $lead = $this->lead($this->rep, 'Smoke CRM Convert Lead', ['phone' => '010'.$digits, 'stage' => 'interested']);
        $order = $this->approvedOrderFor('+2010'.$digits, 900, autoLink: false, lead: $lead);
        $this->actingAs($this->manager);
        $this->page('/employee/crm/leads/'.$lead->id.'/convert')->assertSee('#'.$order->id);
        $this->post('/employee/crm/leads/'.$lead->id.'/convert', ['order_id' => $order->id])
            ->assertRedirect('/employee/crm/deals/'.$lead->id);
        $lead->refresh();
        $this->assertSame($order->id, (int) $lead->order_id);
        $this->assertSame(SalesLead::WON_STAGE, $lead->stage);
        $this->page('/employee/crm/deals/'.$lead->id)->assertSee('#'.$order->id)->assertSee('900.00');

        $this->assertNavLinksWork($home);
    }

    public function test_admin_sees_everything_and_can_export(): void
    {
        $this->actingAs($this->admin);
        $home = $this->page('/admin/crm');
        $home->assertSee('لوحة المبيعات');
        foreach (['all', 'today', 'overdue', 'new', 'stale', 'meta', 'unassigned'] as $quick) {
            $this->page('/admin/crm/leads?quick='.$quick);
        }
        $this->page('/admin/crm/leads?q=Smoke%20CRM')->assertSee('Smoke CRM Rep Lead')->assertSee('Smoke CRM Outsider Lead');
        $this->page('/admin/crm/leads/'.$this->outsiderLead->id)->assertSee('فتح في الواجهة الكلاسيكية');
        $this->page('/admin/crm/deals?quick=all');
        $this->page('/admin/crm/deals/'.$this->repLead->id);
        $this->page('/admin/crm/leads/'.$this->repLead->id.'/convert');

        $csv = $this->post('/admin/crm/leads/bulk', ['action' => 'export', 'ids' => [$this->repLead->id, $this->outsiderLead->id]]);
        $csv->assertOk();
        $this->assertStringContainsString('Smoke CRM Outsider Lead', $csv->streamedContent());

        // الموظف والمدير لا يدخلون مسار الأدمن
        $this->assertNotSame(200, $this->actingAs($this->rep)->get('/admin/crm')->getStatusCode());

        $this->actingAs($this->admin);
        $this->assertNavLinksWork($home);
    }

    public function test_suite_modules_open_for_rep_manager_and_admin(): void
    {
        $modules = [
            'accounts', 'contacts', 'activities', 'calls', 'meetings', 'tasks', 'notes', 'emails',
            'calendar', 'campaigns', 'cases', 'documents', 'quotes', 'reports', 'opportunities',
        ];

        foreach ([
            ['user' => $this->rep, 'prefix' => '/employee/crm', 'admin' => false],
            ['user' => $this->manager, 'prefix' => '/employee/crm', 'admin' => true],
            ['user' => $this->admin, 'prefix' => '/admin/crm', 'admin' => true],
        ] as $ctx) {
            $this->actingAs($ctx['user']);
            foreach ($modules as $module) {
                $this->page($ctx['prefix'].'/'.$module);
            }
            $this->page($ctx['prefix'].'/reports/kpi');
            $this->page($ctx['prefix'].'/reports/daily');
            $this->page($ctx['prefix'].'/inbox');
            $this->page($ctx['prefix'].'/leads/create');
            if ($ctx['admin']) {
                $this->page($ctx['prefix'].'/admin');
                $this->page($ctx['prefix'].'/win-approvals');
            } else {
                $this->get($ctx['prefix'].'/admin')->assertForbidden();
            }
        }
    }

    public function test_legacy_sales_get_pages_redirect_into_crm(): void
    {
        $this->actingAs($this->rep);
        $this->get('/employee/sales')->assertRedirect('/employee/crm');
        $this->get('/employee/sales/leads')->assertRedirect('/employee/crm/leads');
        $this->get('/employee/sales/follow-ups')->assertRedirect('/employee/crm/leads?quick=today');
        $this->get('/employee/sales/leads/'.$this->repLead->id)->assertRedirect('/employee/crm/leads/'.$this->repLead->id);

        $this->actingAs($this->manager);
        $this->get('/employee/sales-manager')->assertRedirect('/employee/crm');
        $this->get('/employee/sales-manager/pipeline')->assertRedirect('/employee/crm/opportunities');
        $this->get('/employee/sales-manager/leads')->assertRedirect('/employee/crm/leads');

        $this->actingAs($this->admin);
        $this->get('/admin/sales/leads')->assertRedirect('/admin/crm/leads');
        $this->get('/admin/sales/leads/'.$this->outsiderLead->id)->assertRedirect('/admin/crm/leads/'.$this->outsiderLead->id);
        $this->actingAs($this->rep);
        $this->get('/employee/sales/leads/create')->assertRedirect('/employee/crm/leads/create');
        $this->get('/employee/sales')->assertRedirect('/employee/crm');
        $this->page('/employee/sales/kpi');
        $this->page('/employee/sales/policy');
        $this->page('/employee/sales/groups');
        $this->actingAs($this->manager);
        $this->get('/employee/sales-manager')->assertRedirect('/employee/crm');
        $this->page('/employee/sales-manager/scorecard');
        $this->page('/employee/sales-manager/attendance');
        $this->page('/employee/sales-manager/distribution');
        $this->page('/employee/sales-manager/whatsapp/queue');
    }

    /** يفتح كل روابط الشريط العلوي (قوائم الوحدات و«+») للدور الحالي: ممنوع 403 أو 404 أو 500. */
    private function assertNavLinksWork(TestResponse $page): void
    {
        preg_match_all('/<div class="crm-menu"[^>]*>(.*?)<\/div>/s', $page->getContent(), $menus);
        $urls = [];
        foreach ($menus[1] as $menu) {
            preg_match_all('/href="([^"]+)"/', $menu, $m);
            $urls = array_merge($urls, $m[1]);
        }
        $urls = array_values(array_unique(array_filter($urls, fn ($u) => str_starts_with($u, 'http'))));
        $this->assertNotEmpty($urls, 'the CRM top bar must render its module menus');

        $bad = [];
        foreach ($urls as $url) {
            $path = parse_url($url, PHP_URL_PATH).(($q = parse_url($url, PHP_URL_QUERY)) ? '?'.$q : '');
            $status = $this->get($path)->getStatusCode();
            if (in_array($status, [403, 404], true) || $status >= 500) {
                $bad[] = "{$status} {$path}";
            }
        }
        $this->assertSame([], $bad, "Broken CRM menu links:\n".implode("\n", $bad));
    }

    private function page(string $url): TestResponse
    {
        $response = $this->get($url);
        $e = $response->exception;
        $this->assertSame(200, $response->getStatusCode(), $url.' → '.$response->getStatusCode().($e ? ' '.get_class($e).': '.mb_substr($e->getMessage(), 0, 300).' @ '.str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine() : ''));

        return $response;
    }

    /** @param  array<string, mixed>  $attrs */
    private function lead(User $owner, string $name, array $attrs = []): SalesLead
    {
        $lead = new SalesLead;
        $lead->forceFill(array_merge([
            'name' => $name, 'phone' => '011'.random_int(10000000, 99999999), 'source' => 'social', 'stage' => 'new_lead', 'priority' => 'normal',
            'assigned_to' => $owner->id, 'created_by' => $owner->id, 'last_contacted_at' => now(), 'next_follow_up_at' => now()->subHour(), 'follow_up_channel' => 'call',
            'interest_type_id' => DB::table('sales_interest_types')->value('id'), 'category_id' => DB::table('sales_lead_categories')->value('id'),
        ], $attrs))->save();

        return $lead;
    }

    private function approvedOrderFor(string $phone, float $amount, bool $autoLink, SalesLead $lead): Order
    {
        $student = User::forceCreate([
            'name' => 'Smoke Convert Student', 'email' => 'smoke-cs-'.Str::lower(Str::random(6)).'@example.test', 'phone' => $phone,
            'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true,
        ]);
        // نحاكي حالة «رقم مختلف»: نخلي الـ lead خارج نافذة الربط التلقائي قبل الاعتماد
        if (! $autoLink) {
            SalesLead::whereKey($lead->id)->update(['updated_at' => now()->subDays(200), 'last_contacted_at' => now()->subDays(200)]);
        }
        $wallet = Wallet::create(['name' => 'Smoke Wallet '.Str::random(4), 'type' => 'vodafone_cash', 'balance' => 0, 'is_active' => true]);
        $order = Order::forceCreate([
            'user_id' => $student->id, 'advanced_course_id' => AdvancedCourse::where('is_active', true)->value('id'), 'original_amount' => $amount,
            'discount_amount' => 0, 'amount' => $amount, 'payment_method' => 'bank_transfer', 'wallet_id' => $wallet->id, 'status' => Order::STATUS_PENDING,
        ]);
        $this->actingAs($this->admin)->postJson("/admin/orders/{$order->id}/approve")->assertOk();
        $this->assertNull($lead->fresh()->order_id, 'precondition: not auto-linked');

        return $order->fresh();
    }
}
