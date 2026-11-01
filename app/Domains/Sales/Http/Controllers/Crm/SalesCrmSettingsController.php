<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Services\SalesAuditService;
use App\Domains\Sales\Support\CrmPermissions;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/**
 * إعدادات CRM للإدارة في مكان واحد (زي Admin في SuiteCRM): صلاحيات الأدوار، استثناءات الموظفين،
 * وروابط كل إعدادات المبيعات الموجودة. للأدمن بس (admin/crm/settings).
 */
class SalesCrmSettingsController extends Controller
{
    use InteractsWithCrm;

    public const TABS = ['roles' => 'الأدوار والصلاحيات', 'users' => 'استثناءات الموظفين', 'setup' => 'إعدادات المبيعات'];

    public function index(Request $request): View
    {
        $scope = $this->adminScope($request);
        $tab = array_key_exists((string) $request->query('tab'), self::TABS) ? (string) $request->query('tab') : 'roles';
        $staff = $this->staff();
        $selected = $request->integer('user') ? $staff->firstWhere('id', $request->integer('user')) : null;
        $all = CrmPermissions::all();

        return $this->view($scope, 'settings', 'settings.index', [
            'tab' => $tab,
            'matrix' => $all['roles'],
            'overrides' => $all['users'],
            'staff' => $staff,
            'selected' => $selected,
            'selectedRole' => $selected ? $this->roleOf($selected) : null,
            'setup' => $this->setupLinks(),
        ]);
    }

    public function updateRoles(Request $request): RedirectResponse
    {
        $this->adminScope($request);
        $old = CrmPermissions::all()['roles'];

        if ($request->boolean('reset')) {
            $new = [];
            foreach (array_keys(CrmPermissions::ROLES) as $role) {
                $new[$role] = CrmPermissions::defaults($role);
            }
        } else {
            $input = (array) $request->input('perm', []);
            $new = [];
            foreach (array_keys(CrmPermissions::ROLES) as $role) {
                foreach (array_keys(CrmPermissions::defaults($role)) as $key) {
                    $new[$role][$key] = (bool) ($input[$role][$this->field($key)] ?? false);
                }
            }
        }

        CrmPermissions::saveRoles($new);
        SalesAuditService::log('sales_crm_role_permissions_updated', null, $this->changed($old, $new, true), $this->changed($old, $new, false), 'تعديل صلاحيات أدوار CRM');

        return redirect()->route('admin.crm.settings.index', ['tab' => 'roles'])
            ->with('success', $request->boolean('reset') ? 'رجعت الصلاحيات للافتراضي.' : 'اتحفظت صلاحيات الأدوار.');
    }

    public function updateUser(Request $request, int $user): RedirectResponse
    {
        $this->adminScope($request);
        $member = $this->staff()->firstWhere('id', $user);
        abort_unless($member, 404);
        $role = $this->roleOf($member);

        $old = CrmPermissions::all()['users'][$member->id] ?? [];
        $input = (array) $request->input('perm', []);
        $new = [];
        if (! $request->boolean('reset')) {
            foreach (array_keys(CrmPermissions::defaults($role)) as $key) {
                $v = (string) ($input[$this->field($key)] ?? '');
                if ($v === 'allow' || $v === 'deny') {
                    $new[$key] = $v === 'allow';
                }
            }
        }

        CrmPermissions::saveUser($member->id, $new);
        SalesAuditService::log('sales_crm_user_permissions_updated', $member, $old, $new, 'تعديل استثناءات صلاحيات CRM للموظف '.$member->name);

        return redirect()->route('admin.crm.settings.index', ['tab' => 'users', 'user' => $member->id])
            ->with('success', $new === [] ? 'الموظف رجع لصلاحيات دوره.' : 'اتحفظت استثناءات '.$member->name.'.');
    }

    /** اسم الحقل في الفورم: النقطة بتتقلب لـ `__` عشان PHP ما يحوّلهاش في أسماء المصفوفات. */
    public static function field(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    private function adminScope(Request $request): CrmScope
    {
        $scope = $this->scope($request);
        abort_unless($scope->isAdmin(), 403);

        return $scope;
    }

    /** موظفو المبيعات ومديروها (اللي صلاحياتهم بتتعدّل من هنا). @return Collection<int, User> */
    private function staff(): Collection
    {
        return User::query()->employees()->where('is_active', true)
            ->whereHas('employeeJob', fn ($q) => $q->whereRaw('LOWER(code) IN (?, ?, ?)', ['sales', 'sales_manager', 'business_developer']))
            ->with('employeeJob:id,code,name')->orderBy('name')->get(['id', 'name', 'email', 'employee_job_id']);
    }

    private function roleOf(User $user): string
    {
        return $user->hasSalesManagerPortalAccess() ? CrmScope::ROLE_MANAGER : CrmScope::ROLE_REP;
    }

    private function changed(array $old, array $new, bool $before): array
    {
        $out = [];
        foreach ($new as $role => $keys) {
            foreach ($keys as $key => $value) {
                if (($old[$role][$key] ?? null) !== $value) {
                    $out[$role.':'.$key] = $before ? ($old[$role][$key] ?? null) : $value;
                }
            }
        }

        return $out;
    }

    /** @return list<array{label: string, icon: string, links: list<array{label: string, url: string}>}> */
    private function setupLinks(): array
    {
        $groups = [
            ['الفريق والهيكل', 'fa-sitemap', [['فرق المبيعات', 'admin.sales.sales-teams.index'], ['الهيكل التنظيمي', 'admin.sales.org-chart.index'], ['تخصصات السيلز', 'admin.sales.specialties.index'], ['لوحة التوزيع', 'admin.sales.distribution.index'], ['تحويل بيانات موظف', 'admin.sales.transfer.index']]],
            ['إعداد العملاء', 'fa-address-card', [['تصنيفات العملاء', 'admin.sales.categories.index'], ['أنواع الاهتمام', 'admin.sales.interest-types.index'], ['مجموعات العملاء', 'admin.sales.groups.index'], ['استيراد عملاء', 'admin.sales.leads.import']]],
            ['القواعد والأهداف', 'fa-scale-balanced', [['قواعد المبيعات', 'admin.sales.policy.index'], ['أهداف KPIs', 'admin.sales.kpi.targets'], ['إعدادات التقرير اليومي', 'admin.sales.daily-reports.settings'], ['أيام الإيقاف', 'admin.sales.day-blocks.index'], ['الشيفتات والقنوات', 'admin.sales.shifts.index']]],
            ['الصفقات والعمولات', 'fa-sack-dollar', [['اعتماد صفقات الفوز', 'admin.sales.win-approvals.index'], ['كوميشن المبيعات', 'admin.sales.commissions.index'], ['اتفاقيات عمولة الكورسات', 'admin.sales.course-commission.courses']]],
            ['الرقابة والتدقيق', 'fa-shield-halved', [['سجل أنشطة المبيعات', 'admin.sales.audit-log.index'], ['تدقيق استخدام CRM', 'admin.sales.crm-compliance.index'], ['Insights الموظفين', 'admin.sales.insights.index']]],
            ['صلاحيات النظام العامة', 'fa-key', [['الأدوار', 'admin.roles.index'], ['صلاحيات المستخدمين', 'admin.user-permissions.index'], ['المستخدمين', 'admin.users.index']]],
        ];

        $out = [];
        foreach ($groups as [$label, $icon, $links]) {
            $links = array_values(array_map(fn ($l) => ['label' => $l[0], 'url' => route($l[1])], array_filter($links, fn ($l) => Route::has($l[1]))));
            if ($links !== []) {
                $out[] = ['label' => $label, 'icon' => $icon, 'links' => $links];
            }
        }

        return $out;
    }
}
