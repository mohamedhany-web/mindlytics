<?php

namespace Tests\Smoke;

use App\Domains\Hr\Models\EmployeeJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;

/**
 * كل صفحات مدير المبيعات (/employee/sales-manager و /employee/crm) وكل صفحات المبيعات في الإدارة
 * (/admin/sales و /admin/crm واللوحات والإرشاد) بتفتح من غير أخطاء، والصفحات اللي فيها {lead} وغيره بتاخد سجلات حقيقية.
 * REVIEW_DUMP=1 بيحفظ HTML كل صفحة في public/_review للمراجعة البصرية (مش بيترفع).
 */
class SalesManagerAdminPagesSmokeTest extends SmokeTestCase
{
    public function test_every_sales_manager_page_opens(): void
    {
        $manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();
        // موظف عنده عملاء جوه فريق المدير (زي SalesCrmSmokeTest)
        $team = \App\Domains\Sales\Models\SalesTeam::where('manager_id', $manager->id)->firstOrFail();
        $this->seedOneOfEach($manager);
        $rep = User::salesEmployees()->where('is_active', true)->whereIn('id', \App\Domains\Sales\Models\SalesLead::query()->select('assigned_to'))->firstOrFail();
        \Illuminate\Support\Facades\DB::table('sales_team_members')->updateOrInsert(['sales_team_id' => $team->id, 'user_id' => $rep->id], ['role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        // اعتماد الفوز وصلاحيات الـ CRM للأدمن بس، ومش ظاهرين في قايمة المدير
        $this->crawl($manager, '#^employee/(sales-manager|crm)(/|$)#', 'mgr', ['#^employee/crm/(win-approvals|access)#']);
    }

    public function test_every_admin_sales_page_opens(): void
    {
        $admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $this->seedOneOfEach($admin);
        $this->crawl($admin, '#^admin/(sales|crm|sales-course-board|sales-diploma-board|advising)(/|$)#', 'adm');
    }

    /** سجل واحد من كل جدول فاضي في النسخة، عشان صفحات التفاصيل والتعديل تتفتح (بيتلغي بعد الاختبار). */
    private function seedOneOfEach(User $owner): void
    {
        $rep = User::salesEmployees()->where('is_active', true)->whereIn('id', \App\Domains\Sales\Models\SalesLead::query()->select('assigned_to'))->first() ?? $owner;
        $by = ['assigned_to' => $rep->id, 'created_by' => $owner->id];
        $account = \App\Domains\Sales\Models\CrmAccount::create(['name' => 'Smoke Seed Account'] + $by);
        \App\Domains\Sales\Models\CrmContact::create(['first_name' => 'Smoke', 'last_name' => 'Seed', 'name' => 'Smoke Seed', 'crm_account_id' => $account->id] + $by);
        \App\Domains\Sales\Models\CrmCampaign::create(['name' => 'Smoke Seed Campaign', 'status' => 'planning'] + $by);
        \App\Domains\Sales\Models\CrmCase::create(['subject' => 'Smoke Seed Case', 'status' => 'new', 'priority' => 'medium'] + $by);
        \App\Domains\Sales\Models\CrmDocument::create(['title' => 'Smoke Seed Doc', 'disk' => 'local', 'path' => 'smoke/seed.pdf', 'original_name' => 'seed.pdf', 'mime' => 'application/pdf', 'size' => 10] + $by);
        \App\Domains\Sales\Models\SalesLeadGroup::create(['name' => 'Smoke Seed Group'] + $by);
        \App\Domains\Sales\Models\SalesCourseBoardEntry::create(['name' => 'Smoke Seed Course Board', 'is_active' => true]);
        \App\Domains\Sales\Models\SalesDiplomaBoardEntry::create(['name' => 'Smoke Seed Diploma Board', 'is_active' => true]);
        if ($section = \App\Domains\Sales\Models\SalesPolicySection::query()->value('id')) {
            \App\Domains\Sales\Models\SalesPolicyRule::create(['section_id' => $section, 'rule_number' => '99', 'title' => 'Smoke Seed Rule', 'content' => 'smoke', 'sort_order' => 99, 'is_active' => true]);
        }
    }

    /** @param list<string> $expectedForbidden */
    private function crawl(User $user, string $pattern, string $tag, array $expectedForbidden = []): void
    {
        $this->actingAs($user);
        $dump = (bool) getenv('REVIEW_DUMP');
        if ($dump) {
            @mkdir(public_path('_review'), 0777, true);
        }

        $failures = [];
        $checked = 0;
        foreach (RouteFacade::getRoutes()->getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods(), true) || ! preg_match($pattern, $uri)) {
                continue;
            }
            if (preg_match('#/(poll|status|media|export|download|print-pdf|csv|pdf)(/|$)|\.(csv|xlsx|pdf)$#', $uri)) {
                continue; // ملفات و JSON
            }
            $params = $this->paramsFor($route, $user);
            if ($params === null) {
                $dump && file_put_contents(public_path("_review/_{$tag}.txt"), "SKIP {$uri}\n", FILE_APPEND);

                continue;
            }
            $response = $this->get(url(preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => $params[$m[1]] ?? '', $uri)));
            $checked++;
            $status = $response->getStatusCode();
            $html = (string) $response->getContent();
            $dump && file_put_contents(public_path("_review/_{$tag}.txt"), $status.' '.$uri.($response->isRedirect() ? ' -> '.$response->headers->get('Location') : '')."\n", FILE_APPEND);

            $forbiddenOk = collect($expectedForbidden)->contains(fn ($p) => preg_match($p, $uri));
            if ($status >= 500 || ($status === 200 && preg_match('/(Undefined (variable|array key|property)|Call to a member function|\{\{ *\$)/', $html, $m))) {
                $failures[] = "{$status} {$uri} — ".mb_substr($response->exception?->getMessage() ?? ($m[0] ?? ''), 0, 240);
            } elseif (in_array($status, [403, 404], true) && ! $forbiddenOk) {
                $failures[] = "{$status} {$uri}";
            }
            if ($dump && $status === 200 && str_contains($html, '<html')) {
                file_put_contents(public_path("_review/{$tag}__".str_replace(['/', '{', '}'], ['__', '', ''], $uri).'.html'), $html);
            }
        }

        $this->assertGreaterThan(20, $checked);
        $this->assertSame([], $failures, implode("\n", $failures));
    }

    /** @return array<string, int|string>|null */
    private function paramsFor(Route $route, User $user): ?array
    {
        $names = $route->parameterNames();
        if ($names === []) {
            return [];
        }
        $types = [];
        $action = $route->getActionName();
        if (str_contains($action, '@')) {
            [$class, $method] = explode('@', $action);
            if (method_exists($class, $method)) {
                foreach ((new ReflectionMethod($class, $method))->getParameters() as $p) {
                    $type = $p->getType()?->getName();
                    if ($type && is_subclass_of($type, Model::class)) {
                        $types[$p->getName()] = $type;
                    } elseif ($p->getName() === 'lead') {
                        $types['lead'] = \App\Domains\Sales\Models\SalesLead::class;
                    }
                }
            }
        }

        $teamIds = \App\Domains\Sales\Models\SalesTeam::where('manager_id', $user->id)->with('members')->get()->flatMap(fn ($t) => $t->members->pluck('user_id'));
        $rep = $user->role === 'super_admin'
            ? User::salesEmployees()->where('is_active', true)->whereIn('id', \App\Domains\Sales\Models\SalesLead::query()->select('assigned_to'))->first()
            : User::whereIn('id', $teamIds)->where('is_active', true)->first();

        $out = [];
        foreach ($names as $name) {
            $class = $types[$name] ?? $types[\Illuminate\Support\Str::camel($name)] ?? match (true) {
                $name === 'lead' => \App\Domains\Sales\Models\SalesLead::class,
                $name === 'id' && str_contains($route->uri(), 'daily-reports') => \App\Domains\Sales\Models\SalesDailyReport::class,
                default => null,
            };
            if (in_array($name, ['employee', 'user'], true) && (! $class || $class === User::class)) {
                if ($name === 'user' && str_contains($route->uri(), '/access/')) {
                    $out[$name] = $user->id;

                    continue;
                }
                if (! $rep) {
                    return null;
                }
                $out[$name] = $rep->id;

                continue;
            }
            if ($name === 'order') {
                $orderId = \App\Domains\Sales\Models\SalesLead::query()->whereNotNull('order_id')
                    ->when($user->role !== 'super_admin', fn ($q) => $q->whereIn('assigned_to', $teamIds->push($user->id)))->value('order_id');
                if (! $orderId) {
                    return null;
                }
                $out[$name] = $orderId;

                continue;
            }
            if (! $class) {
                return null;
            }
            $model = new $class;
            $query = $class::query();
            if ($class === \App\Domains\Sales\Models\SalesLead::class && $user->role !== 'super_admin') {
                $teamIds = \App\Domains\Sales\Models\SalesTeam::where('manager_id', $user->id)->with('members')->get()->flatMap(fn ($t) => $t->members->pluck('user_id'))->push($user->id);
                $query->whereIn('assigned_to', $teamIds);
            }
            $row = $query->latest($model->getKeyName())->first();
            if (! $row) {
                return null;
            }
            $out[$name] = $row->getRouteKey();
        }

        return $out;
    }
}
