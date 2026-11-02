<?php

namespace App\Domains\Sales\Support;

use Illuminate\Support\Facades\Route;

/**
 * الشريط العلوي لشاشات CRM: وحدات ثابتة، لكل وحدة قائمة (زي SuiteCRM).
 * الروابط تشير للشاشات الموجودة لكل دور، وأي route غير مسجّل يُحذف تلقائياً بدل ما يكسر الصفحة.
 */
final class CrmNavigation
{
    /**
     * @return list<array{key: string, label: string, url: string, items: list<array{label: string, url: string, divider?: bool}>}>
     */
    public static function tabs(CrmScope $scope): array
    {
        $r = $scope->isRep() ? 'employee.sales.' : 'employee.sales-manager.';

        $tabs = [
            [
                'key' => 'home', 'label' => 'الرئيسية', 'route' => $scope->route('home'),
                'items' => [],
            ],
            [
                'key' => 'leads', 'label' => 'العملاء المحتملون', 'route' => $scope->route('leads.index'),
                'items' => array_merge(
                    [['عرض العملاء المحتملين', $scope->route('leads.index')]],
                    $scope->isRep() ? [['＋ إنشاء عميل محتمل', 'employee.sales.leads.create'], ['المجموعات', 'employee.sales.groups.index'], ['متابعاتي', 'employee.sales.follow-ups.index']] : [],
                    $scope->isManager() ? [['متابعات الفريق', 'employee.sales-manager.follow-ups.index'], ['توزيع العملاء', 'employee.sales-manager.distribution.index'], ['تحويل بيانات موظف', 'employee.sales-manager.transfer.index']] : [],
                    $scope->isAdmin() ? [['＋ إنشاء عميل محتمل', 'admin.sales.leads.create'], ['استيراد دفعة', 'admin.sales.leads.import'], ['المجموعات', 'admin.sales.groups.index'], ['لوحة التوزيع', 'admin.sales.distribution.index']] : [],
                ),
            ],
            [
                'key' => 'deals', 'label' => 'الصفقات', 'route' => $scope->route('deals.index'),
                'items' => array_merge(
                    [['عرض الصفقات', $scope->route('deals.index')]],
                    $scope->isRep() ? [['عمولاتي', 'employee.sales.commissions.index']] : [],
                    $scope->isManager() ? [['عمولات الفريق', 'employee.sales-manager.commissions.index'], ['Pipeline الفريق', 'employee.sales-manager.pipeline']] : [],
                    $scope->isAdmin() ? [['اعتماد صفقات الفوز', 'admin.sales.win-approvals.index'], ['كوميشن المبيعات', 'admin.sales.commissions.index']] : [],
                ),
            ],
            [
                'key' => 'students', 'label' => 'الطلاب', 'route' => $scope->route('students.index'),
                'items' => array_merge(
                    [['عرض الطلاب', $scope->route('students.index')]],
                    $scope->isAdmin() ? [['إدارة المستخدمين', 'admin.users.index']] : [],
                ),
            ],
            [
                'key' => 'activities', 'label' => 'الأنشطة', 'route' => $scope->route('activities.index'),
                'items' => array_merge(
                    [['عرض الأنشطة', $scope->route('activities.index')]],
                    $scope->can('activities.create') ? [['＋ جدولة نشاط', $scope->route('activities.create')]] : [],
                    [['المتأخرة', $scope->route('activities.index'), ['quick' => 'overdue']]],
                ),
            ],
            [
                'key' => 'inbox', 'label' => 'المحادثات', 'route' => $scope->route('inbox'),
                'items' => array_merge(
                    [['كل المحادثات', $scope->route('inbox')], ['غير المقروءة', $scope->route('inbox'), ['quick' => 'unread']]],
                    $scope->isRep() ? [['واتساب', 'employee.sales.whatsapp.inbox.index'], ['Messenger و Instagram', 'employee.sales.meta-social.inbox.index']] : [],
                    $scope->isManager() ? [['واتساب الفريق', 'employee.sales-manager.whatsapp.inbox.index'], ['طلبات جديدة بدون موظف', 'employee.sales-manager.whatsapp.queue.index'], ['Messenger و Instagram', 'employee.sales.meta-social.inbox.index']] : [],
                    $scope->isAdmin() ? [['صندوق واتساب', 'admin.whatsapp.inbox'], ['Messenger و Instagram', 'admin.meta-social.inbox.index']] : [],
                ),
            ],
            [
                'key' => 'calendar', 'label' => 'الكاليندر', 'route' => $scope->route('calendar'),
                'items' => array_merge(
                    [['كاليندر الأنشطة', $scope->route('calendar')]],
                    $scope->isRep() || ! $scope->can('calendar.shared') ? [] : [['الكاليندر المشترك للفريق', $scope->route('calendar'), ['view' => 'shared']]],
                    $scope->isRep() ? [['جدول الشيفتات', 'employee.sales.shifts.index']] : [],
                    $scope->isManager() ? [['تقويم الحضور والإجازات', 'employee.sales-manager.schedule-calendar.index'], ['شيفتات الفريق', 'employee.sales-manager.shifts.index'], ['طلبات تبديل الشيفت', 'employee.sales-manager.shift-swaps.index']] : [],
                    $scope->isAdmin() ? [['تقويم الشيفت', 'admin.sales.schedule-calendar.index'], ['الشيفتات والقنوات', 'admin.sales.shifts.index']] : [],
                ),
            ],
            [
                'key' => 'reports', 'label' => 'التقارير', 'route' => $scope->route('reports.index'),
                'items' => array_merge(
                    [['كل التقارير', $scope->route('reports.index')]],
                    array_values(array_filter([['أداء الموظفين', $scope->route('reports.show'), ['report' => 'performance']], ['النشاط اليومي', $scope->route('reports.show'), ['report' => 'daily']], ['الصفقات والإيراد', $scope->route('reports.show'), ['report' => 'deals']]], fn ($i) => $scope->can('reports.'.$i[2]['report']))),
                    $scope->isRep() ? [['KPIs والأداء', 'employee.sales.kpi.index'], ['التقرير اليومي', 'employee.sales.daily-reports.index'], ['تقارير الأداء', 'employee.sales.reports.index'], ['خصومات KPI', 'employee.sales.penalties.index']] : [],
                    $scope->isManager() ? [['مركز الرقابة اليومية', 'employee.sales-manager.scorecard.index'], ['KPIs والأهداف', 'employee.sales-manager.kpi.index'], ['اللوحة الحية', 'employee.sales-manager.live-board'], ['التقارير اليومية', 'employee.sales-manager.daily-reports.index'], ['تقرير الفريق للإدارة', 'employee.sales-manager.team-reports.index'], ['تقارير الكامبين', 'employee.sales-manager.campaign-reports.index']] : [],
                    $scope->isAdmin() ? [['تقارير المبيعات', 'admin.sales.reports.index'], ['مراقبة KPIs', 'admin.sales.kpi.index'], ['التقارير اليومية', 'admin.sales.daily-reports.index'], ['تقارير الفرق', 'admin.sales.team-daily-reports.index'], ['Insights الموظفين', 'admin.sales.insights.index'], ['تدقيق استخدام CRM', 'admin.sales.crm-compliance.index']] : [],
                ),
            ],
            [
                'key' => 'more', 'label' => 'المزيد', 'route' => null,
                'items' => array_merge(
                    $scope->isRep() ? [['معلومات الكورسات', 'employee.sales.course-board.index'], ['قواعد المبيعات', 'employee.sales.policy.index'], ['الواجهة القديمة للمبيعات', 'employee.sales.dashboard'], ['لوحة الموظف', 'employee.dashboard']] : [],
                    $scope->isManager() ? [['حضور الفريق', 'employee.sales-manager.attendance.index'], ['الحضور الحي', 'employee.sales-manager.presence.index'], ['هيكل الفريق', 'employee.sales-manager.org-chart.index'], ['أهداف الشهر', 'employee.sales-manager.kpi.targets'], ['الواجهة القديمة للمدير', 'employee.sales-manager.dashboard'], ['لوحة الموظف', 'employee.dashboard']] : [],
                    $scope->isAdmin() ? [['⚙ إعدادات CRM والصلاحيات', 'admin.crm.settings.index'], ['فرق المبيعات', 'admin.sales.sales-teams.index'], ['تصنيفات العملاء', 'admin.sales.categories.index'], ['أنواع الاهتمام', 'admin.sales.interest-types.index'], ['تخصصات السيلز', 'admin.sales.specialties.index'], ['قواعد السيلز', 'admin.sales.policy.index'], ['أهداف المبيعات', 'admin.sales.kpi.targets'], ['سجل أنشطة المبيعات', 'admin.sales.audit-log.index'], ['لوحة الإدارة', 'admin.dashboard']] : [],
                ),
            ],
        ];

        $out = [];
        foreach ($tabs as $tab) {
            if (isset(CrmPermissions::MODULES[$tab['key']]) && ! $scope->can($tab['key'].'.access')) {
                continue;
            }
            $items = [];
            foreach ($tab['items'] as $item) {
                [$label, $route] = $item;
                if (Route::has($route)) {
                    $items[] = ['label' => $label, 'url' => route($route, $item[2] ?? [])];
                }
            }
            $url = $tab['route'] && Route::has($tab['route']) ? route($tab['route']) : ($items[0]['url'] ?? null);
            if ($url === null && $items === []) {
                continue;
            }
            $out[] = ['key' => $tab['key'], 'label' => $tab['label'], 'url' => $url ?? '#', 'items' => $items];
        }

        return $out;
    }

    /** زر «+» للإنشاء السريع. @return list<array{label: string, url: string}> */
    public static function quickCreate(CrmScope $scope): array
    {
        $candidates = match (true) {
            $scope->isRep() => [['عميل محتمل', 'employee.sales.leads.create'], ['مجموعة عملاء', 'employee.sales.groups.create']],
            $scope->isAdmin() => [['عميل محتمل', 'admin.sales.leads.create'], ['مجموعة عملاء', 'admin.sales.groups.create'], ['فريق مبيعات', 'admin.sales.sales-teams.create']],
            default => [],
        };
        if ($scope->can('activities.access') && $scope->can('activities.create')) {
            $candidates = array_merge($candidates, [['مكالمة', $scope->route('activities.create')], ['اجتماع', $scope->route('activities.create'), ['kind' => 'meeting']], ['مهمة', $scope->route('activities.create'), ['kind' => 'task']]]);
        }

        return array_values(array_map(
            fn ($c) => ['label' => $c[0], 'url' => route($c[1], $c[2] ?? [])],
            array_filter($candidates, fn ($c) => Route::has($c[1]))
        ));
    }

    /** التاب اللي يتعلّم عليه لصفحات إدارة المبيعات (admin.sales.*) لما تتعرض بإطار CRM. */
    public static function moduleForAdminRoute(?string $name): string
    {
        $section = explode('.', (string) preg_replace('/^admin\.sales\./', '', (string) $name))[0];

        return match ($section) {
            'leads', 'groups', 'categories', 'interest-types', 'distribution', 'transfer' => 'leads',
            'win-approvals', 'commissions', 'course-commission' => 'deals',
            'schedule-calendar', 'shifts', 'day-blocks' => 'calendar',
            'reports', 'kpi', 'daily-reports', 'team-daily-reports', 'insights', 'crm-compliance' => 'reports',
            default => 'more',
        };
    }
}
