<?php

namespace App\Domains\Sales\Support;

use Illuminate\Support\Facades\Route;

/**
 * شريط وحدات SuiteCRM + أدوات التشغيل (كلها داخل CRM layout).
 */
final class CrmNavigation
{
    /**
     * @return list<array{key: string, label: string, url: string, items: list<array{label: string, url: string}>}>
     */
    public static function tabs(CrmScope $scope): array
    {
        $c = $scope->routePrefix;

        $tabs = [
            ['key' => 'home', 'label' => 'الرئيسية', 'route' => $c.'home', 'items' => []],
            [
                'key' => 'accounts', 'label' => 'الحسابات', 'route' => $c.'accounts.index',
                'items' => [
                    ['عرض الحسابات', $c.'accounts.index'],
                    ['＋ حساب جديد', $c.'accounts.create'],
                ],
            ],
            [
                'key' => 'contacts', 'label' => 'جهات الاتصال', 'route' => $c.'contacts.index',
                'items' => [
                    ['عرض جهات الاتصال', $c.'contacts.index'],
                    ['＋ جهة اتصال جديدة', $c.'contacts.create'],
                ],
            ],
            [
                'key' => 'leads', 'label' => 'العملاء المحتملون', 'route' => $c.'leads.index',
                'items' => array_values(array_filter([
                    ['عرض العملاء المحتملين', $c.'leads.index'],
                    ['＋ عميل محتمل', $c.'leads.create'],
                    $scope->isRep() ? ['مجموعات العملاء', 'employee.sales.groups.index'] : null,
                    $scope->isAdmin() ? ['مجموعات العملاء', 'admin.sales.groups.index'] : null,
                    $scope->isAdmin() ? ['استيراد دفعة', 'admin.sales.leads.import'] : null,
                    $scope->isAdmin() ? ['تصنيفات العملاء', 'admin.sales.categories.index'] : null,
                    $scope->isAdmin() ? ['أنواع الاهتمام', 'admin.sales.interest-types.index'] : null,
                ])),
            ],
            [
                'key' => 'opportunities', 'label' => 'الفرص', 'route' => $c.'opportunities.index',
                'items' => array_values(array_filter([
                    ['عرض الفرص', $c.'opportunities.index'],
                    ['بانتظار الاعتماد', $c.'opportunities.index', ['quick' => 'won_pending']],
                    $scope->isAdmin() ? ['اعتماد الفوز', 'admin.sales.win-approvals.index'] : null,
                    $scope->isAdmin() ? ['الكوميشن', 'admin.sales.commissions.index'] : null,
                ])),
            ],
            [
                'key' => 'activities', 'label' => 'الأنشطة', 'route' => $c.'activities.index',
                'items' => [
                    ['كل الأنشطة', $c.'activities.index'],
                    ['المكالمات', $c.'calls.index'],
                    ['الاجتماعات', $c.'meetings.index'],
                    ['المهام', $c.'tasks.index'],
                    ['الملاحظات', $c.'notes.index'],
                    ['البريد', $c.'emails.index'],
                    ['＋ نشاط جديد', $c.'activities.create'],
                ],
            ],
            [
                'key' => 'calendar', 'label' => 'التقويم', 'route' => $c.'calendar.index',
                'items' => array_values(array_filter([
                    ['تقويم CRM', $c.'calendar.index'],
                    $scope->isRep() ? ['شيفتاتي', 'employee.sales.shifts.index'] : null,
                    $scope->isManager() ? ['تقويم الفريق', 'employee.sales-manager.schedule-calendar.index'] : null,
                    $scope->isManager() ? ['شيفتات الفريق', 'employee.sales-manager.shifts.index'] : null,
                    $scope->isManager() ? ['تبديل الشيفتات', 'employee.sales-manager.shift-swaps.index'] : null,
                    $scope->isAdmin() ? ['تقويم الشيفت', 'admin.sales.schedule-calendar.index'] : null,
                    $scope->isAdmin() ? ['شيفتات وقنوات', 'admin.sales.shifts.index'] : null,
                    $scope->isAdmin() ? ['جدول Blocks', 'admin.sales.day-blocks.index'] : null,
                ])),
            ],
            [
                'key' => 'campaigns', 'label' => 'الحملات', 'route' => $c.'campaigns.index',
                'items' => array_values(array_filter([
                    ['عرض الحملات', $c.'campaigns.index'],
                    ['＋ حملة جديدة', $c.'campaigns.create'],
                    $scope->isAdmin() ? ['مجموعات العملاء', 'admin.sales.groups.index'] : null,
                ])),
            ],
            [
                'key' => 'cases', 'label' => 'التذاكر', 'route' => $c.'cases.index',
                'items' => [
                    ['عرض التذاكر', $c.'cases.index'],
                    ['＋ تذكرة جديدة', $c.'cases.create'],
                ],
            ],
            [
                'key' => 'documents', 'label' => 'المستندات', 'route' => $c.'documents.index',
                'items' => [
                    ['عرض المستندات', $c.'documents.index'],
                    ['＋ مستند', $c.'documents.create'],
                ],
            ],
            [
                'key' => 'quotes', 'label' => 'عروض الأسعار', 'route' => $c.'quotes.index',
                'items' => array_values(array_filter([
                    ['الطلبات والعروض', $c.'quotes.index'],
                    $scope->isRep() || $scope->isManager() ? ['معلومات الكورسات', 'employee.sales.course-board.index'] : null,
                    $scope->isAdmin() ? ['معلومات الكورسات', 'admin.sales-course-board.index'] : null,
                    $scope->isAdmin() ? ['توصيف الدبلومات', 'admin.sales-diploma-board.index'] : null,
                ])),
            ],
            [
                'key' => 'reports', 'label' => 'التقارير', 'route' => $c.'reports.index',
                'items' => array_values(array_filter([
                    ['مركز التقارير', $c.'reports.index'],
                    ['KPI CRM', $c.'reports.kpi'],
                    $scope->isRep() ? ['KPI التفصيلي', 'employee.sales.kpi.index'] : null,
                    $scope->isManager() ? ['KPI الفريق', 'employee.sales-manager.kpi.index'] : null,
                    $scope->isManager() ? ['Scorecard', 'employee.sales-manager.scorecard.index'] : null,
                    $scope->isManager() ? ['تقارير الكامبين', 'employee.sales-manager.campaign-reports.index'] : null,
                    $scope->isRep() ? ['التقرير اليومي', 'employee.sales.daily-reports.index'] : null,
                    $scope->isManager() ? ['تقارير يومية', 'employee.sales-manager.daily-reports.index'] : null,
                    $scope->isRep() ? ['العمولات', 'employee.sales.commissions.index'] : null,
                    $scope->isManager() ? ['عمولات الفريق', 'employee.sales-manager.commissions.index'] : null,
                    ['تقرير ماسنجر وواتساب', $c.'reports.social'],
                    $scope->isAdmin() ? ['تقارير المبيعات', 'admin.sales.reports.index'] : null,
                    $scope->isAdmin() ? ['التقارير اليومية', 'admin.sales.daily-reports.index'] : null,
                    $scope->isAdmin() ? ['إعداد التقرير اليومي', 'admin.sales.daily-reports.settings'] : null,
                    $scope->isAdmin() ? ['تقارير الفرق', 'admin.sales.team-daily-reports.index'] : null,
                    $scope->isAdmin() ? ['مراقبة KPIs', 'admin.sales.kpi.index'] : null,
                    $scope->isAdmin() ? ['أهداف المبيعات', 'admin.sales.kpi.targets'] : null,
                    $scope->isAdmin() ? ['Insights', 'admin.sales.insights.index'] : null,
                    $scope->isAdmin() ? ['الكوميشن', 'admin.sales.commissions.index'] : null,
                ])),
            ],
            [
                'key' => 'inbox', 'label' => 'المحادثات', 'route' => $c.'inbox.index',
                'items' => array_values(array_filter([
                    ['مركز المحادثات', $c.'inbox.index'],
                    $scope->isRep() ? ['واتساب', 'employee.sales.whatsapp.inbox.index'] : null,
                    $scope->isRep() ? ['Messenger / Instagram', 'employee.sales.meta-social.inbox.index'] : null,
                    $scope->isManager() ? ['واتساب الفريق', 'employee.sales-manager.whatsapp.inbox.index'] : null,
                    $scope->isManager() ? ['طابور بلا موظف', 'employee.sales-manager.whatsapp.queue.index'] : null,
                    $scope->isAdmin() ? ['واتساب', 'admin.whatsapp.inbox'] : null,
                    $scope->isAdmin() ? ['Messenger / Instagram', 'admin.meta-social.inbox.index'] : null,
                    $scope->isAdmin() ? ['مركز ليدات Meta', 'admin.meta-social.leads.index'] : null,
                    $scope->isAdmin() ? ['ربط موظفي Meta', 'admin.meta-social.agents.index'] : null,
                    $scope->isAdmin() ? ['إعدادات واتساب', 'admin.whatsapp.settings'] : null,
                    ['تقرير ماسنجر وواتساب', $c.'reports.social'],
                ])),
            ],
            [
                'key' => 'admin', 'label' => 'الإدارة', 'route' => $c.'admin.index',
                'items' => array_values(array_filter([
                    ['لوحة إدارة CRM', $c.'admin.index'],
                    $scope->isAdmin() ? ['اعتماد الفوز', $c.'win-approvals.index'] : null,
                    $scope->isAdmin() ? ['اعتماد الفوز (تفصيلي)', 'admin.sales.win-approvals.index'] : null,
                    $scope->isAdmin() ? ['لوحة التوزيع', 'admin.sales.distribution.index'] : null,
                    $scope->isAdmin() ? ['هيكل السيلز', 'admin.sales.org-chart.index'] : null,
                    $scope->isAdmin() ? ['فرق المبيعات', 'admin.sales.sales-teams.index'] : null,
                    $scope->isAdmin() ? ['تخصصات السيلز', 'admin.sales.specialties.index'] : null,
                    $scope->isAdmin() ? ['تحويل بيانات موظف', 'admin.sales.transfer.index'] : null,
                    $scope->isAdmin() ? ['قواعد السيلز', 'admin.sales.policy.index'] : null,
                    $scope->isAdmin() ? ['تدقيق CRM', 'admin.sales.crm-compliance.index'] : null,
                    $scope->isAdmin() ? ['سجل أنشطة', 'admin.sales.audit-log.index'] : null,
                    $scope->isAdmin() ? ['صلاحيات CRM', $c.'access.index'] : null,
                    $scope->isManager() ? ['توزيع الاهتمام', 'employee.sales-manager.distribution.index'] : null,
                    $scope->isManager() ? ['اللوحة الحية', 'employee.sales-manager.live-board'] : null,
                    $scope->isManager() ? ['متابعة الفريق', 'employee.sales-manager.ops-board'] : null,
                    $scope->isManager() ? ['هيكل الفريق', 'employee.sales-manager.org-chart.index'] : null,
                    $scope->isManager() ? ['حضور الفريق', 'employee.sales-manager.attendance.index'] : null,
                    $scope->isManager() ? ['مراقبة التواجد', 'employee.sales-manager.presence.index'] : null,
                    $scope->isManager() ? ['تحويل Leads', 'employee.sales-manager.transfer.index'] : null,
                    $scope->isRep() ? ['قواعد المبيعات', 'employee.sales.policy.index'] : null,
                ])),
            ],
        ];

        $access = app(\App\Domains\Sales\Services\CrmAccessService::class);

        $out = [];
        foreach ($tabs as $tab) {
            if ($tab['key'] === 'home') {
                $out[] = [
                    'key' => $tab['key'],
                    'label' => $tab['label'],
                    'url' => Route::has($tab['route']) ? route($tab['route']) : '#',
                    'items' => [],
                ];

                continue;
            }

            if (! $access->allowsTab($scope->user, $tab['key'], $scope)) {
                continue;
            }

            if ($tab['key'] === 'admin' && $scope->isRep()) {
                // المندوب يرى السياسة فقط من قائمة الإدارة المبسّطة
                $tab['items'] = array_values(array_filter($tab['items'], fn ($i) => ($i[1] ?? '') === 'employee.sales.policy.index'));
                if ($tab['items'] === []) {
                    continue;
                }
                $tab['route'] = 'employee.sales.policy.index';
            }

            $items = [];
            foreach ($tab['items'] as $item) {
                $label = $item[0];
                $routeName = $item[1];
                $params = $item[2] ?? [];
                if (! Route::has($routeName)) {
                    continue;
                }
                if (! $access->allowsRouteHint($scope->user, $routeName, $scope)) {
                    continue;
                }
                $items[] = ['label' => $label, 'url' => route($routeName, $params)];
            }

            $url = null;
            if (! empty($tab['route']) && Route::has($tab['route'])) {
                $url = route($tab['route']);
            } elseif ($items !== []) {
                $url = $items[0]['url'];
            }

            if ($url === null) {
                continue;
            }

            $out[] = [
                'key' => $tab['key'],
                'label' => $tab['label'],
                'url' => $url,
                'items' => $items,
            ];
        }

        return $out;
    }

    /** @return list<array{label: string, url: string}> */
    public static function quickCreate(CrmScope $scope): array
    {
        $c = $scope->routePrefix;
        $candidates = [
            ['عميل محتمل', $c.'leads.create'],
            ['حساب', $c.'accounts.create'],
            ['جهة اتصال', $c.'contacts.create'],
            ['نشاط', $c.'activities.create'],
            ['حملة', $c.'campaigns.create'],
            ['تذكرة', $c.'cases.create'],
            ['مستند', $c.'documents.create'],
        ];

        $out = [];
        foreach ($candidates as [$label, $routeName]) {
            if (Route::has($routeName)) {
                $out[] = ['label' => $label, 'url' => route($routeName)];
            }
        }

        return $out;
    }
}
