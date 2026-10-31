<?php

namespace App\Domains\Sales\Support;

/**
 * كتالوج وحدات/أدوات CRM اللي تظهر في الشريط ولوحة الإدارة.
 * الافتراضي حسب دور CRM؛ التجاوزات في جدول crm_feature_access.
 */
final class CrmFeatureCatalog
{
    /**
     * @return list<array{key: string, label: string, group: string, roles: list<string>}>
     */
    public static function all(): array
    {
        return [
            // وحدات الشريط
            ['key' => 'module.accounts', 'label' => 'الحسابات', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.contacts', 'label' => 'جهات الاتصال', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.leads', 'label' => 'العملاء المحتملون', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.opportunities', 'label' => 'الفرص', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.activities', 'label' => 'الأنشطة', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.calendar', 'label' => 'التقويم', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.campaigns', 'label' => 'الحملات', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.cases', 'label' => 'التذاكر', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.documents', 'label' => 'المستندات', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.quotes', 'label' => 'عروض الأسعار', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.reports', 'label' => 'التقارير', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.inbox', 'label' => 'المحادثات', 'group' => 'modules', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'module.admin', 'label' => 'قائمة الإدارة', 'group' => 'modules', 'roles' => ['manager', 'admin']],

            // أدوات تشغيل
            ['key' => 'ops.win_approvals', 'label' => 'اعتماد الفوز', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.distribution', 'label' => 'لوحة التوزيع', 'group' => 'ops', 'roles' => ['admin', 'manager']],
            ['key' => 'ops.org_chart', 'label' => 'هيكل السيلز', 'group' => 'ops', 'roles' => ['admin', 'manager']],
            ['key' => 'ops.sales_teams', 'label' => 'فرق المبيعات', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.specialties', 'label' => 'تخصصات السيلز', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.transfer', 'label' => 'تحويل بيانات موظف', 'group' => 'ops', 'roles' => ['admin', 'manager']],
            ['key' => 'ops.policy', 'label' => 'قواعد السيلز', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.kpi', 'label' => 'مراقبة KPIs / أهداف', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.reports_admin', 'label' => 'تقارير المبيعات (أدمن)', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.daily_reports', 'label' => 'التقارير اليومية', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.commissions', 'label' => 'الكوميشن', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.groups', 'label' => 'مجموعات العملاء', 'group' => 'ops', 'roles' => ['rep', 'admin']],
            ['key' => 'ops.import', 'label' => 'استيراد عملاء', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.shifts', 'label' => 'شيفتات / تقويم / Blocks', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.compliance', 'label' => 'تدقيق CRM', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.audit', 'label' => 'سجل الأنشطة', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.course_board', 'label' => 'معلومات الكورسات / الدبلومات', 'group' => 'ops', 'roles' => ['rep', 'admin']],
            ['key' => 'ops.whatsapp', 'label' => 'واتساب', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.meta', 'label' => 'Messenger / Instagram', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.meta_agents', 'label' => 'ربط موظفي Meta', 'group' => 'ops', 'roles' => ['admin']],
            ['key' => 'ops.social_report', 'label' => 'تقرير ماسنجر وواتساب', 'group' => 'ops', 'roles' => ['rep', 'manager', 'admin']],
            ['key' => 'ops.live_board', 'label' => 'اللوحة الحية / متابعة الفريق', 'group' => 'ops', 'roles' => ['manager']],
            ['key' => 'ops.attendance', 'label' => 'حضور / تواجد الفريق', 'group' => 'ops', 'roles' => ['manager']],
            ['key' => 'ops.scorecard', 'label' => 'Scorecard', 'group' => 'ops', 'roles' => ['manager']],
            ['key' => 'ops.access_matrix', 'label' => 'إدارة صلاحيات CRM', 'group' => 'ops', 'roles' => ['admin']],
        ];
    }

    /** @return array<string, string> */
    public static function groups(): array
    {
        return [
            'modules' => 'وحدات الشريط',
            'ops' => 'أدوات التشغيل',
        ];
    }

    /** @return array<string, array{key: string, label: string, group: string, roles: list<string>}> */
    public static function keyed(): array
    {
        $out = [];
        foreach (self::all() as $row) {
            $out[$row['key']] = $row;
        }

        return $out;
    }

    public static function defaultAllowed(string $featureKey, string $crmRole): bool
    {
        $row = self::keyed()[$featureKey] ?? null;
        if ($row === null) {
            return true;
        }

        return in_array($crmRole, $row['roles'], true);
    }

    /**
     * ربط مفتاح وحدة الشريط → feature.
     *
     * @return array<string, string>
     */
    public static function tabFeatureMap(): array
    {
        return [
            'accounts' => 'module.accounts',
            'contacts' => 'module.contacts',
            'leads' => 'module.leads',
            'opportunities' => 'module.opportunities',
            'activities' => 'module.activities',
            'calendar' => 'module.calendar',
            'campaigns' => 'module.campaigns',
            'cases' => 'module.cases',
            'documents' => 'module.documents',
            'quotes' => 'module.quotes',
            'reports' => 'module.reports',
            'inbox' => 'module.inbox',
            'admin' => 'module.admin',
        ];
    }

    /**
     * ربط route name (或部分) → feature لإخفاء عناصر القوائم.
     *
     * @return array<string, string>
     */
    public static function routeFeatureHints(): array
    {
        return [
            'win-approvals' => 'ops.win_approvals',
            'distribution' => 'ops.distribution',
            'org-chart' => 'ops.org_chart',
            'sales-teams' => 'ops.sales_teams',
            'specialties' => 'ops.specialties',
            'transfer' => 'ops.transfer',
            'policy' => 'ops.policy',
            'kpi' => 'ops.kpi',
            'scorecard' => 'ops.scorecard',
            'reports.index' => 'ops.reports_admin',
            'daily-reports' => 'ops.daily_reports',
            'commissions' => 'ops.commissions',
            'groups' => 'ops.groups',
            'leads.import' => 'ops.import',
            'shifts' => 'ops.shifts',
            'schedule-calendar' => 'ops.shifts',
            'day-blocks' => 'ops.shifts',
            'crm-compliance' => 'ops.compliance',
            'audit-log' => 'ops.audit',
            'sales-course-board' => 'ops.course_board',
            'sales-diploma-board' => 'ops.course_board',
            'course-board' => 'ops.course_board',
            'whatsapp' => 'ops.whatsapp',
            'meta-social' => 'ops.meta',
            'meta-social.agents' => 'ops.meta_agents',
            'reports.social' => 'ops.social_report',
            'live-board' => 'ops.live_board',
            'ops-board' => 'ops.live_board',
            'attendance' => 'ops.attendance',
            'presence' => 'ops.attendance',
            'access' => 'ops.access_matrix',
        ];
    }
}
