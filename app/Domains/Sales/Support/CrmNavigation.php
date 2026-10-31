<?php

namespace App\Domains\Sales\Support;

use Illuminate\Support\Facades\Route;

/**
 * شريط وحدات SuiteCRM — روابط CRM فقط (بدون واجهات قديمة).
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
                'items' => [
                    ['عرض العملاء المحتملين', $c.'leads.index'],
                    ['＋ عميل محتمل', $c.'leads.create'],
                ],
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
                'items' => [['تقويم CRM', $c.'calendar.index']],
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
                'items' => [['الطلبات والعروض', $c.'quotes.index']],
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
                'key' => 'inbox', 'label' => 'المحادثات', 'route' => $c.'inbox.index',
                'items' => [
                    ['واتساب / Meta', $c.'inbox.index'],
                ],
            ],
            [
                'key' => 'admin', 'label' => 'الإدارة', 'route' => $c.'admin.index',
                'items' => array_values(array_filter([
                    ['لوحة إدارة CRM', $c.'admin.index'],
                    $scope->isAdmin() ? ['اعتماد الفوز', $c.'win-approvals.index'] : null,
                ])),
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
