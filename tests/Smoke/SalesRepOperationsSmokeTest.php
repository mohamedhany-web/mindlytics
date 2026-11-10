<?php

namespace Tests\Smoke;

use App\Domains\Sales\Models\SalesLead;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

/**
 * يوم شغل موظف مبيعات من أوله لآخره (من نفس فورمات الصفحات):
 * عميل جديد ← ملفه في الـ CRM ← نشاط ومكالمة ومتابعة ومرحلة ← حساب وجهة اتصال وحملة وتذكرة ومستند ونشاط ← جروب ← التقرير اليومي.
 * كل خطوة لازم تنجح من غير 500 ولا أخطاء validation، وصفحات التفاصيل اللي اتعملت لازم تفتح.
 */
class SalesRepOperationsSmokeTest extends SmokeTestCase
{
    private array $problems = [];

    public function test_a_sales_rep_can_do_the_whole_day_from_the_panel(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $rep = User::salesEmployees()->where('is_active', true)->firstOrFail();
        $this->actingAs($rep);
        $phone = '0105'.random_int(1000000, 9999999);

        // عميل جديد (فورم /employee/crm/leads/create)
        $this->step('lead store', $this->post(route('employee.sales.leads.store'), [
            'stage' => 'new_lead', 'priority' => 'normal', 'name' => 'Smoke Rep Lead', 'phone' => $phone, 'email' => 'smoke-rep@example.test', 'source' => 'whatsapp',
            'interest_type_id' => (int) \Illuminate\Support\Facades\DB::table('sales_interest_types')->value('id'), 'follow_up_channel' => 'call',
            'next_follow_up_at' => now()->addDay()->format('Y-m-d\TH:i'), 'notes' => 'smoke', 'interest' => 'Data',
        ]));
        $lead = SalesLead::where('phone', $phone)->first();
        $this->assertNotNull($lead, 'lead was not created: '.implode(' | ', $this->problems));
        $this->assertSame($rep->id, (int) $lead->assigned_to);

        foreach (['employee.crm.leads.show', 'employee.crm.leads.edit', 'employee.crm.deals.show', 'employee.crm.opportunities.show'] as $name) {
            $this->page($name, route($name, $lead->id));
        }

        $outcome = array_key_first(\App\Domains\Sales\Models\SalesActivity::OUTCOMES);
        $this->step('activity', $this->post(route('employee.sales.leads.activities.store', $lead), ['type' => 'call', 'outcome' => $outcome, 'body' => 'smoke call', 'duration_seconds' => 60]));
        $this->step('quick activity', $this->post(route('employee.sales.leads.quick-activity', $lead), ['type' => 'whatsapp', 'body' => 'smoke wa']));
        $this->step('next follow', $this->post(route('employee.sales.leads.next-follow', $lead), ['next_follow_up_at' => now()->addDays(2)->format('Y-m-d\TH:i'), 'follow_up_channel' => 'whatsapp']));
        $next = array_search('تأهيل', SalesLead::STAGES, true) ?: 'qualified';
        $this->step('pipeline stage', $this->post(route('employee.sales.leads.pipeline', $lead), ['stage' => $next, 'notes' => 'smoke stage move notes', 'call_answered' => '1', 'next_follow_up_at' => now()->addDays(3)->format('Y-m-d\TH:i'), 'follow_up_channel' => 'call']));
        $this->step('lead update', $this->put(route('employee.sales.leads.update', $lead), [
            'name' => 'Smoke Rep Lead 2', 'phone' => $phone, 'source' => 'whatsapp', 'stage' => $lead->fresh()->stage, 'priority' => $lead->priority ?? 'normal',
            'interest_type_id' => $lead->interest_type_id, 'follow_up_channel' => 'call', 'next_follow_up_at' => now()->addDays(4)->format('Y-m-d\TH:i'),
        ]));
        $this->page('lead after updates', route('employee.crm.leads.show', $lead->id));
        if (! $rep->hasSalesManagerPortalAccess()) {
            // قايمة «إجراءات» كلها للمدير: ما تظهرش فاضية للموظف
            $this->get(route('employee.crm.leads.show', $lead->id))->assertDontSee('@click="actions = ! actions"', false);
        }

        // وحدات الـ CRM
        $this->step('account store', $this->post(route('employee.crm.accounts.store'), ['name' => 'Smoke Account', 'phone' => '0100', 'account_type' => 'customer', 'assigned_to' => $rep->id]));
        $account = \App\Domains\Sales\Models\CrmAccount::where('name', 'Smoke Account')->first();
        $this->step('contact store', $this->post(route('employee.crm.contacts.store'), ['first_name' => 'Smoke', 'last_name' => 'Contact', 'crm_account_id' => $account?->id, 'phone' => '0101', 'assigned_to' => $rep->id]));
        // الحملات مش من صلاحيات الموظف
        $this->post(route('employee.crm.campaigns.store'), ['name' => 'Smoke Campaign', 'status' => 'planning', 'campaign_type' => 'whatsapp', 'assigned_to' => $rep->id])->assertForbidden();
        $this->step('case store', $this->post(route('employee.crm.cases.store'), ['subject' => 'Smoke Case', 'status' => 'new', 'priority' => 'medium', 'sales_lead_id' => $lead->id, 'assigned_to' => $rep->id]));
        $this->step('document store', $this->post(route('employee.crm.documents.store'), ['title' => 'Smoke Doc', 'sales_lead_id' => $lead->id, 'assigned_to' => $rep->id, 'file' => UploadedFile::fake()->create('smoke.pdf', 20, 'application/pdf')]));
        $this->step('crm activity store', $this->post(route('employee.crm.activities.store'), ['type' => 'meeting', 'status' => 'planned', 'title' => 'Smoke meeting', 'sales_lead_id' => $lead->id, 'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i')]));

        foreach ([
            'accounts' => \App\Domains\Sales\Models\CrmAccount::class, 'contacts' => \App\Domains\Sales\Models\CrmContact::class,
            'cases' => \App\Domains\Sales\Models\CrmCase::class,
            'documents' => \App\Domains\Sales\Models\CrmDocument::class,
        ] as $module => $class) {
            $row = $class::query()->where('assigned_to', $rep->id)->latest('id')->first();
            if (! $row) {
                $this->problems[] = "{$module}: nothing was created";

                continue;
            }
            $this->page("{$module} show", route("employee.crm.{$module}.show", $row->id));
            if (\Illuminate\Support\Facades\Route::has("employee.crm.{$module}.edit")) {
                $this->page("{$module} edit", route("employee.crm.{$module}.edit", $row->id));
            }
        }
        $activity = \App\Domains\Sales\Models\SalesActivity::where('sales_lead_id', $lead->id)->latest('id')->first();
        if ($activity) {
            $this->page('activity show', route('employee.crm.activities.show', $activity->id));
        }

        // جروب عملاء
        $this->step('group store', $this->post(route('employee.sales.groups.store'), ['name' => 'Smoke Group', 'description' => 'smoke']));
        $group = \App\Domains\Sales\Models\SalesLeadGroup::where('name', 'Smoke Group')->latest('id')->first();
        if ($group) {
            $this->page('group show', route('employee.sales.groups.show', $group));
        }

        // التقرير اليومي
        $this->step('daily report', $this->post(route('employee.sales.daily-reports.store'), [
            'action' => 'submit', 'report_date' => now()->toDateString(), 'messages_replied' => 3, 'leads_qualified' => 1, 'bookings_from_leads' => 0, 'numbers_worked' => 5,
            'followups_done' => 2, 'calls_made' => 4, 'meetings_held' => 0, 'calls_answered' => 2, 'activity_notes' => 'smoke', 'productivity_notes' => 'smoke',
            'contacts' => array_map(fn ($i) => ['interaction_type' => 'call', 'sales_lead_id' => $lead->id, 'contact_name' => 'Smoke Rep Lead '.$i, 'contact_phone' => $phone, 'client_status' => 'interested', 'client_problems' => 'smoke problem '.$i], range(1, 4)),
        ]));
        $this->page('daily reports list', route('employee.sales.daily-reports.index'));

        // القوايم بعد البيانات الجديدة
        foreach (['employee.crm.home', 'employee.crm.leads.index', 'employee.crm.calendar.index', 'employee.crm.tasks.index', 'employee.crm.activities.index', 'employee.crm.reports.daily', 'employee.crm.reports.kpi'] as $name) {
            $this->page($name, route($name));
        }

        $this->assertSame([], $this->problems, implode("\n", $this->problems));
    }

    private function step(string $label, TestResponse $response): void
    {
        $errors = session('errors')?->all() ?? [];
        if ($response->getStatusCode() >= 400 || $errors !== []) {
            $this->problems[] = "{$label}: {$response->getStatusCode()} ".mb_substr(implode(' / ', $errors) ?: ($response->exception?->getMessage() ?? ''), 0, 300);
        }
        session()->forget('errors');
    }

    private function page(string $label, string $url): void
    {
        $response = $this->get($url);
        $html = (string) $response->getContent();
        if ($response->getStatusCode() !== 200) {
            $this->problems[] = "{$label}: {$response->getStatusCode()} ".mb_substr($response->exception?->getMessage() ?? ($response->headers->get('Location') ?? ''), 0, 300);
        } elseif (preg_match('/(Undefined (variable|array key|property)|Call to a member function)/', $html, $m)) {
            $this->problems[] = "{$label}: page shows «{$m[0]}»";
        }
        if (getenv('REVIEW_DUMP') && $response->getStatusCode() === 200) {
            @mkdir(public_path('_review'), 0777, true);
            file_put_contents(public_path('_review/op__'.preg_replace('/\W+/', '_', $label).'.html'), $html);
        }
    }
}
