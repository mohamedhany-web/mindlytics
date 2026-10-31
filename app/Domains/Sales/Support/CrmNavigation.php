<?php

namespace App\Domains\Sales\Support;

use Illuminate\Support\Facades\Route;

/**
 * شريط وحدات SuiteCRM داخل CRM المبيعات.
 */
final class CrmNavigation
{
    /**
     * @return list<array{key: string, label: string, url: string, items: list<array{label: string, url: string}>}>
     */
    public static function tabs(CrmScope $scope): array
    {
        $c = $scope->routePrefix; // employee.crm. | admin.crm.

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
                    $scope->isRep() ? ['＋ عميل محتمل', 'employee.sales.leads.create'] : null,
                    $scope->isAdmin() ? ['＋ عميل محتمل', 'admin.sales.leads.create'] : null,
                    $scope->isAdmin() ? ['استيراد', 'admin.sales.leads.import'] : null,
                    $scope->isManager() ? ['توزيع الفريق', 'employee.sales-manager.distribution.index'] : null,
                ])),
            ],
            [
                'key' => 'opportunities', 'label' => 'الفرص', 'route' => $c.'opportunities.index',
                'items' => [
                    ['عرض الفرص', $c.'opportunities.index'],
                    ['بانتظار الاعتماد', $c.'opportunities.index', ['quick' => 'won_pending']],
                ],
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
                'items' => [
                    ['تقويم CRM', $c.'calendar.index'],
                ],
            ],
            [
                'key' => 'campaigns', 'label' => 'الحملات', 'route' => $c.'campaigns.index',
                'items' => [
                    ['عرض الحملات', $c.'campaigns.index'],
                    ['＋ حملة جديدة', $c.'campaigns.create'],
                ],
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
                'items' => [
                    ['الطلبات والعروض', $c.'quotes.index'],
                ],
            ],
            [
                'key' => 'reports', 'label' => 'التقارير', 'route' => $c.'reports.index',
                'items' => [
                    ['مركز التقارير', $c.'reports.index'],
                    ['KPI والأداء', $c.'reports.kpi'],
                    ['التقارير اليومية', $c.'reports.daily'],
                    ['الكامبينات', $c.'reports.campaigns'],
                ],
            ],
            [
                'key' => 'inbox', 'label' => 'المحادثات',
                'route' => $scope->isRep()
                    ? 'employee.sales.whatsapp.inbox.index'
                    : ($scope->isManager() ? 'employee.sales-manager.whatsapp.inbox.index' : 'admin.whatsapp.inbox'),
                'items' => array_values(array_filter([
                    $scope->isRep() ? ['واتساب', 'employee.sales.whatsapp.inbox.index'] : null,
                    $scope->isRep() ? ['Messenger / Instagram', 'employee.sales.meta-social.inbox.index'] : null,
                    $scope->isManager() ? ['واتساب الفريق', 'employee.sales-manager.whatsapp.inbox.index'] : null,
                    $scope->isManager() ? ['طابور بلا موظف', 'employee.sales-manager.whatsapp.queue.index'] : null,
                    $scope->isAdmin() ? ['صندوق الوارد', 'admin.whatsapp.inbox'] : null,
                ])),
            ],
            [
                'key' => 'admin', 'label' => 'الإدارة', 'route' => $c.'admin.index',
                'items' => [
                    ['لوحة إدارة CRM', $c.'admin.index'],
                ],
            ],
        ];

        $out = [];
        foreach ($tabs as $tab) {
            if ($tab['key'] === 'admin' && $scope->isRep()) {
                continue;
            }

            $items = [];
            foreach ($tab['items'] as $item) {
                $label = $item[0];
                $routeName = $item[1];
                $params = $item[2] ?? [];
                if (! Route::has($routeName)) {
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
        $candidates = array_values(array_filter([
            $scope->isRep() ? ['عميل محتمل', 'employee.sales.leads.create'] : null,
            $scope->isAdmin() ? ['عميل محتمل', 'admin.sales.leads.create'] : null,
            ['حساب', $c.'accounts.create'],
            ['جهة اتصال', $c.'contacts.create'],
            ['نشاط', $c.'activities.create'],
            ['حملة', $c.'campaigns.create'],
            ['تذكرة', $c.'cases.create'],
            ['مستند', $c.'documents.create'],
        ]));

        $out = [];
        foreach ($candidates as [$label, $routeName]) {
            if (Route::has($routeName)) {
                $out[] = ['label' => $label, 'url' => route($routeName)];
            }
        }

        return $out;
    }
}
