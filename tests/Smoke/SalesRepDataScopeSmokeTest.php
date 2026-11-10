<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * الموظف ما يشوفش بيانات موظف تاني في أي صفحة من صفحات القايمة بتاعته (عملاء، أنشطة، حسابات، تذاكر، مستندات، حملات، تقارير).
 */
class SalesRepDataScopeSmokeTest extends SmokeTestCase
{
    public function test_rep_pages_never_show_another_reps_records(): void
    {
        $salesJob = EmployeeJob::where('code', 'sales')->firstOrFail();
        $rep = User::where('employee_job_id', $salesJob->id)->where('is_active', true)->firstOrFail();
        $other = User::forceCreate([
            'name' => 'Smoke Other Rep '.Str::random(4), 'email' => 'smoke-other-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $salesJob->id,
        ]);
        $mark = 'ZZOTHER'.Str::upper(Str::random(5));
        $by = ['assigned_to' => $other->id, 'created_by' => $other->id];

        $lead = SalesLead::create(['name' => $mark.' Lead', 'phone' => '0109'.random_int(1000000, 9999999), 'source' => 'call', 'stage' => 'offer_sent', 'priority' => 'normal',
            'assigned_to' => $other->id, 'next_follow_up_at' => now()->addHour(), 'follow_up_channel' => 'call', 'expected_value' => 999]);
        \App\Domains\Sales\Models\SalesActivity::create(['sales_lead_id' => $lead->id, 'user_id' => $other->id, 'type' => 'call', 'title' => $mark.' Call', 'body' => $mark.' body']);
        $account = \App\Domains\Sales\Models\CrmAccount::create(['name' => $mark.' Account'] + $by);
        \App\Domains\Sales\Models\CrmContact::create(['first_name' => $mark, 'last_name' => 'Contact', 'name' => $mark.' Contact', 'crm_account_id' => $account->id] + $by);
        \App\Domains\Sales\Models\CrmCampaign::create(['name' => $mark.' Campaign', 'status' => 'active'] + $by);
        \App\Domains\Sales\Models\CrmCase::create(['subject' => $mark.' Case', 'status' => 'new', 'priority' => 'high', 'sales_lead_id' => $lead->id] + $by);
        \App\Domains\Sales\Models\CrmDocument::create(['title' => $mark.' Doc', 'disk' => 'local', 'path' => 'x/y.pdf', 'original_name' => 'y.pdf', 'mime' => 'application/pdf', 'size' => 1] + $by);

        $this->actingAs($rep);
        $leaks = [];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || ! preg_match('#^employee/(crm|sales)(/[a-z/-]*)?$#', $uri) || str_contains($uri, '{')) {
                continue;
            }
            if (preg_match('#/(poll|status|templates|suggested-templates)$#', $uri)) {
                continue;
            }
            $response = $this->get('/'.$uri);
            if ($response->getStatusCode() === 200 && str_contains((string) $response->getContent(), $mark)) {
                $leaks[] = $uri;
            }
        }
        foreach (['employee/crm/leads?quick=all', 'employee/crm/opportunities?quick=all', 'employee/crm/activities?scope=all', 'employee/crm/calendar?view=month'] as $uri) {
            if (str_contains((string) $this->get('/'.$uri)->getContent(), $mark)) {
                $leaks[] = $uri;
            }
        }

        $this->assertSame([], $leaks, "Another rep's records show on:\n".implode("\n", $leaks));
    }

    public function test_rep_menu_matches_rep_permissions(): void
    {
        $rep = User::where('employee_job_id', EmployeeJob::where('code', 'sales')->value('id'))->where('is_active', true)->firstOrFail();
        $html = (string) $this->actingAs($rep)->get('/employee/crm')->assertOk()->getContent();
        preg_match('#<nav class="crm-nav".*?</nav>#s', $html, $nav);
        if (getenv('NAV_DUMP')) {
            file_put_contents(getenv('NAV_DUMP'), $html);
        }

        // اللي الموظف محتاجه في يومه
        $this->assertStringContainsString(route('employee.sales.daily-reports.index'), $html, 'daily report link');
        $this->assertStringContainsString(route('employee.sales.policy.index'), $html, 'sales rules link');
        $this->assertStringContainsString(route('employee.sales.commissions.index'), $html);
        // مش شغله
        $this->assertStringNotContainsString(route('employee.crm.campaigns.index'), $html, 'campaigns are for managers');
        $this->assertStringNotContainsString(route('employee.crm.campaigns.create'), $html);
        $this->assertStringNotContainsString('/employee/sales-manager', $html);
        $this->assertStringNotContainsString('/admin/', $nav[0] ?? '');
        $this->assertSame(1, substr_count($html, route('employee.crm.reports.social')), 'social report once');
        // ومقفولة بالرابط كمان
        $this->get(route('employee.crm.campaigns.index'))->assertForbidden();
        $this->post(route('employee.crm.campaigns.store'), ['name' => 'x', 'status' => 'planning'])->assertForbidden();

        // المدير لسه شايف الحملات وتقاريره اليومية
        $manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();
        $mhtml = (string) $this->actingAs($manager)->get('/employee/crm')->assertOk()->getContent();
        $this->assertStringContainsString(route('employee.crm.campaigns.index'), $mhtml);
        $this->assertStringContainsString(route('employee.sales-manager.daily-reports.index'), $mhtml, 'manager team daily reports link');
    }
}
