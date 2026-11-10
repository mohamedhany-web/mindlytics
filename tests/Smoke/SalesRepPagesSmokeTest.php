<?php

namespace Tests\Smoke;

use App\Domains\Sales\Models\SalesLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use ReflectionMethod;

/**
 * كل صفحات موظف المبيعات (/employee/sales و /employee/crm) بتفتح من غير أخطاء لموظف مبيعات حقيقي في النسخة.
 * الصفحات اللي فيها {lead} أو غيره بتاخد سجل من بيانات الموظف نفسه لو موجود.
 * REVIEW_DUMP=1 بيحفظ HTML كل صفحة في public/_review للمراجعة البصرية (مش بيترفع).
 */
class SalesRepPagesSmokeTest extends SmokeTestCase
{
    public function test_every_sales_rep_page_opens_without_errors(): void
    {
        $rep = User::salesEmployees()->where('is_active', true)
            ->whereIn('id', SalesLead::query()->select('assigned_to'))->first()
            ?? User::salesEmployees()->where('is_active', true)->firstOrFail();
        $this->actingAs($rep);

        $dump = (bool) getenv('REVIEW_DUMP');
        if ($dump) {
            @mkdir(public_path('_review'), 0777, true);
        }

        $failures = [];
        $checked = 0;
        foreach (RouteFacade::getRoutes()->getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true) || ! preg_match('#^employee/(sales|crm)(/|$)#', $route->uri())) {
                continue;
            }
            // صفحات المدير والأدمن (الاعتمادات، الصلاحيات، الإدارة، الحملات) بترفض الموظف عن قصد ومش ظاهرة في قايمته
            if (preg_match('#^employee/crm/(win-approvals|admin|access|campaigns)(/|$)#', $route->uri()) || $route->uri() === 'employee/crm/quotes/{order}') {
                continue;
            }
            if (str_contains($route->uri(), '/poll') || str_contains($route->uri(), '/media') || str_ends_with($route->uri(), '/status') || str_contains($route->uri(), 'print-pdf')) {
                continue; // JSON/ملفات، مش صفحات
            }
            $params = $this->paramsFor($route, $rep);
            if ($params === null) {
                if ($dump) {
                    file_put_contents(public_path('_review/_log.txt'), 'SKIP '.$route->uri()."\n", FILE_APPEND);
                }

                continue; // مفيش سجل للموظف ده
            }

            $url = url(preg_replace_callback('/\{(\w+)\??\}/', fn ($m) => $params[$m[1]] ?? '', $route->uri()));
            $response = $this->get($url);
            $checked++;
            $status = $response->getStatusCode();
            $html = (string) $response->getContent();

            if ($status >= 500 || ($status === 200 && preg_match('/(Undefined (variable|array key|property)|Call to a member function|htmlspecialchars\(\)|\{\{ *\$)/', $html, $m))) {
                $error = $response->exception?->getMessage() ?? ($m[0] ?? '');
                $failures[] = "{$status} {$route->uri()} — ".mb_substr($error, 0, 220);
            } elseif ($status === 403 || $status === 404) {
                $failures[] = "{$status} {$route->uri()}";
            }
            if ($dump) {
                file_put_contents(public_path('_review/_log.txt'), $status.' '.$route->uri().($response->isRedirect() ? ' -> '.$response->headers->get('Location') : '')."\n", FILE_APPEND);
            }
            if ($dump && $status === 200 && str_contains($html, '<html')) {
                file_put_contents(public_path('_review/'.str_replace(['/', '{', '}'], ['__', '', ''], $route->uri()).'.html'), $html);
            }
        }

        $this->assertGreaterThan(30, $checked);

        // ستايل الـ CRM بيتعرض من Laravel لو public_html على الاستضافة ما فيهوش public/css
        $this->get('/css/sales-crm.css')->assertOk()->assertHeader('Content-Type', 'text/css; charset=UTF-8');
        $this->get('/css/missing.css')->assertNotFound();
        $this->get('/css/..%2F.env')->assertNotFound();
        $this->assertSame([], $failures, implode("\n", $failures));
    }

    /** @return array<string, int|string>|null */
    private function paramsFor(Route $route, User $rep): ?array
    {
        $names = $route->parameterNames();
        if ($names === []) {
            return [];
        }
        $action = $route->getActionName();
        $types = [];
        if (str_contains($action, '@')) {
            [$class, $method] = explode('@', $action);
            if (method_exists($class, $method)) {
                foreach ((new ReflectionMethod($class, $method))->getParameters() as $p) {
                    $type = $p->getType()?->getName();
                    if ($type && is_subclass_of($type, Model::class)) {
                        $types[$p->getName()] = $type;
                    }
                }
            }
        }

        $out = [];
        foreach ($names as $name) {
            if ($name === 'user') {
                $out[$name] = $rep->id;

                continue;
            }
            $class = $types[$name] ?? null;
            if (! $class) {
                return null;
            }
            /** @var Model $model */
            $model = new $class;
            $query = $class::query();
            $table = $model->getTable();
            foreach (['assigned_to', 'user_id', 'owner_id', 'created_by', 'employee_id'] as $col) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, $col)) {
                    $mine = (clone $query)->where($col, $rep->id)->latest($model->getKeyName())->first();
                    if ($mine) {
                        $out[$name] = $mine->getRouteKey();

                        continue 2;
                    }
                }
            }
            $any = $query->latest($model->getKeyName())->first();
            if (! $any) {
                return null;
            }
            $out[$name] = $any->getRouteKey();
        }

        return $out;
    }
}
