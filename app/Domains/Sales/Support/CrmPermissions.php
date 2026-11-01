<?php

namespace App\Domains\Sales\Support;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * صلاحيات CRM القابلة للتعديل من الإدارة (زي Role Management في SuiteCRM): وحدة × إجراء لكل دور،
 * واستثناءات لموظف بعينه. نطاق البيانات (عملاؤه / فريقه / الكل) ثابت في CrmScope ومش بيتغير من هنا.
 *
 * - الأدمن دايماً كل الصلاحيات (زي admin في SuiteCRM).
 * - الافتراضي = السلوك الحالي، فمفيش حاجة بتتغير لحد ما الإدارة تعدّل.
 * - التخزين: ملف JSON على الـ local disk (مش public)، من غير تغيير في قاعدة البيانات.
 */
final class CrmPermissions
{
    private const STORAGE_PATH = 'sales/crm_permissions.json';

    /** الأدوار اللي صلاحياتها بتتعدّل. */
    public const ROLES = [
        CrmScope::ROLE_REP => 'موظف المبيعات',
        CrmScope::ROLE_MANAGER => 'مدير المبيعات / Business Developer',
    ];

    /**
     * الوحدات والإجراءات. `roles` = الأدوار اللي الإجراء له معنى عندها (غير كده بيبان «—» في الجدول).
     *
     * @var array<string, array{label: string, icon: string, actions: array<string, array{label: string, roles?: list<string>}>}>
     */
    public const MODULES = [
        'leads' => ['label' => 'العملاء المحتملون', 'icon' => 'fa-user-plus', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
            'export' => ['label' => 'تصدير CSV'],
            'transfer' => ['label' => 'تحويل لموظف (جماعي)', 'roles' => ['manager']],
            'convert' => ['label' => 'تحويل لطالب (ربط بطلب)', 'roles' => ['manager']],
        ]],
        'deals' => ['label' => 'الصفقات', 'icon' => 'fa-handshake', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
        ]],
        'students' => ['label' => 'الطلاب', 'icon' => 'fa-user-graduate', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
        ]],
        'activities' => ['label' => 'الأنشطة', 'icon' => 'fa-phone', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
            'create' => ['label' => 'جدولة نشاط'],
            'assign_others' => ['label' => 'تكليف موظف تاني', 'roles' => ['manager']],
            'edit_others' => ['label' => 'تعديل أو إلغاء أنشطة الفريق', 'roles' => ['manager']],
        ]],
        'calendar' => ['label' => 'الكاليندر', 'icon' => 'fa-calendar-days', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
            'shared' => ['label' => 'الكاليندر المشترك', 'roles' => ['manager']],
        ]],
        'inbox' => ['label' => 'المحادثات', 'icon' => 'fa-comments', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
            'unassigned' => ['label' => 'طابور المحادثات من غير موظف', 'roles' => ['manager']],
        ]],
        'reports' => ['label' => 'التقارير', 'icon' => 'fa-chart-column', 'actions' => [
            'access' => ['label' => 'دخول الوحدة'],
            'export' => ['label' => 'تصدير CSV'],
            'performance' => ['label' => 'تقرير أداء الموظفين'],
            'daily' => ['label' => 'تقرير النشاط اليومي'],
            'funnel' => ['label' => 'تقرير قمع المراحل'],
            'sources' => ['label' => 'تقرير المصادر'],
            'activities' => ['label' => 'تقرير الأنشطة المخططة'],
            'deals' => ['label' => 'تقرير الصفقات والإيراد'],
            'lost' => ['label' => 'تقرير أسباب الخسارة'],
        ]],
    ];

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /** كل مفاتيح الصلاحيات: `module.action`. @return list<string> */
    public static function keys(): array
    {
        $keys = [];
        foreach (self::MODULES as $module => $m) {
            foreach (array_keys($m['actions']) as $action) {
                $keys[] = $module.'.'.$action;
            }
        }

        return $keys;
    }

    public static function applies(string $role, string $key): bool
    {
        [$module, $action] = array_pad(explode('.', $key, 2), 2, '');
        $def = self::MODULES[$module]['actions'][$action] ?? null;

        return $def !== null && (! isset($def['roles']) || in_array($role, $def['roles'], true));
    }

    /** الافتراضي: كل إجراء له معنى عند الدور مسموح (= السلوك قبل الصلاحيات). */
    public static function defaults(string $role): array
    {
        $out = [];
        foreach (self::keys() as $key) {
            if (self::applies($role, $key)) {
                $out[$key] = true;
            }
        }

        return $out;
    }

    /** @return array{roles: array<string, array<string, bool>>, users: array<int, array<string, bool>>} */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $data = [];
        try {
            $disk = Storage::disk('local');
            if ($disk->exists(self::STORAGE_PATH)) {
                $decoded = json_decode((string) $disk->get(self::STORAGE_PATH), true);
                $data = is_array($decoded) ? $decoded : [];
            }
        } catch (\Throwable) {
            $data = [];
        }

        $roles = [];
        foreach (array_keys(self::ROLES) as $role) {
            $saved = is_array($data['roles'][$role] ?? null) ? $data['roles'][$role] : [];
            $roles[$role] = [];
            foreach (self::defaults($role) as $key => $default) {
                $roles[$role][$key] = array_key_exists($key, $saved) ? (bool) $saved[$key] : $default;
            }
        }

        $users = [];
        foreach ((is_array($data['users'] ?? null) ? $data['users'] : []) as $userId => $overrides) {
            if ((int) $userId > 0 && is_array($overrides)) {
                $clean = array_map('boolval', array_intersect_key($overrides, array_flip(self::keys())));
                if ($clean !== []) {
                    $users[(int) $userId] = $clean;
                }
            }
        }

        return self::$cache = ['roles' => $roles, 'users' => $users];
    }

    /** الصلاحية الفعلية: استثناء الموظف لو موجود، وإلا صلاحية دوره. الأدمن دايماً مسموح. */
    public static function allows(User $user, string $role, string $key): bool
    {
        if ($role === CrmScope::ROLE_ADMIN) {
            return true;
        }
        if (! self::applies($role, $key)) {
            return false;
        }

        $all = self::all();
        $override = $all['users'][(int) $user->id][$key] ?? null;

        return $override ?? ($all['roles'][$role][$key] ?? false);
    }

    /** @param  array<string, array<string, bool>>  $roles */
    public static function saveRoles(array $roles): void
    {
        $all = self::all();
        foreach (array_keys(self::ROLES) as $role) {
            foreach (array_keys(self::defaults($role)) as $key) {
                $all['roles'][$role][$key] = (bool) ($roles[$role][$key] ?? false);
            }
        }
        self::write($all);
    }

    /** @param  array<string, bool|null>  $overrides  null = يرجع لصلاحية الدور */
    public static function saveUser(int $userId, array $overrides): void
    {
        $all = self::all();
        $clean = [];
        foreach (self::keys() as $key) {
            if (array_key_exists($key, $overrides) && $overrides[$key] !== null) {
                $clean[$key] = (bool) $overrides[$key];
            }
        }
        if ($clean === []) {
            unset($all['users'][$userId]);
        } else {
            $all['users'][$userId] = $clean;
        }
        self::write($all);
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    private static function write(array $all): void
    {
        $ok = Storage::disk('local')->put(self::STORAGE_PATH, json_encode([
            'roles' => $all['roles'],
            'users' => (object) $all['users'],
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        self::flush();
        if (! $ok) {
            throw new \RuntimeException('Could not save CRM permissions to storage/app/private/'.self::STORAGE_PATH);
        }
    }
}
