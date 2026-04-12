<?php

/**
 * شجرة الحسابات المرجعية للأكاديمية (عرض إداري — ربط منطقي بالوحدات في النظام).
 * الأكواد للتنظيم والتقارير؛ يمكن توسيعها لاحقاً لربط دفتر أستاذ عام (GL).
 */
return [
    'currency' => 'EGP',
    'roots' => [
        [
            'code' => '1',
            'name' => 'الأصول',
            'type' => 'asset',
            'description' => 'ما تملكه الأكاديمية من نقدية ومستحقات وذمم مدينة.',
            'children' => [
                [
                    'code' => '11',
                    'name' => 'الأصول المتداولة',
                    'type' => 'asset',
                    'description' => 'تتحول إلى سيولة خلال سنة أو دورة تشغيل.',
                    'children' => [
                        [
                            'code' => '111',
                            'name' => 'النقدية وما في حكمها',
                            'type' => 'asset',
                            'description' => 'أرصدة المحافظ والتحويلات المعتمدة في المنصة.',
                            'children' => [
                                ['code' => '1111', 'name' => 'محافظ المنصة (بنوك / فودافون / إنستاباي)', 'type' => 'asset', 'source' => 'wallets', 'route' => 'admin.wallets.index', 'icon' => 'fa-wallet'],
                                ['code' => '1112', 'name' => 'مدفوعات مكتملة — سجل المدفوعات', 'type' => 'asset', 'source' => 'payments', 'route' => 'admin.payments.index', 'icon' => 'fa-credit-card'],
                                ['code' => '1113', 'name' => 'عمليات بوابات الدفع (كاشير / فواتيرك)', 'type' => 'asset', 'source' => 'gateway_payments', 'route' => 'admin.accounting.gateway-operations', 'icon' => 'fa-plug'],
                            ],
                        ],
                        [
                            'code' => '112',
                            'name' => 'المدينون (ذمم الطلاب والعملاء)',
                            'type' => 'asset',
                            'description' => 'مستحقات لم تُحصّل بعد.',
                            'children' => [
                                ['code' => '1121', 'name' => 'فواتير معلقة / غير مدفوعة', 'type' => 'asset', 'source' => 'invoices', 'route' => 'admin.invoices.index', 'icon' => 'fa-file-invoice'],
                                ['code' => '1122', 'name' => 'طلبات شراء معلقة (أونلاين)', 'type' => 'asset', 'source' => 'orders', 'route' => 'admin.orders.index', 'icon' => 'fa-shopping-cart'],
                                ['code' => '1123', 'name' => 'أقساط تقسيط — مستحقة التحصيل', 'type' => 'asset', 'source' => 'installment_receivable', 'route' => 'admin.accounting.installments', 'icon' => 'fa-percentage'],
                                ['code' => '1124', 'name' => 'قائمة اتفاقيات التقسيط (تفصيلي)', 'type' => 'asset', 'source' => 'installments', 'route' => 'admin.installments.agreements.index', 'icon' => 'fa-handshake'],
                            ],
                        ],
                        [
                            'code' => '113',
                            'name' => 'مستحقات تسجيل وحجوزات',
                            'type' => 'asset',
                            'children' => [
                                ['code' => '1131', 'name' => 'حجوزات كورسات أوفلاين (قيد المراجعة)', 'type' => 'asset', 'source' => 'offline_bookings', 'route' => 'admin.offline-course-bookings.index', 'icon' => 'fa-building'],
                                ['code' => '1132', 'name' => 'حجوزات مجموعات أونلاين', 'type' => 'asset', 'source' => 'online_bookings', 'route' => 'admin.online-course-bookings.index', 'icon' => 'fa-calendar-check'],
                            ],
                        ],
                    ],
                ],
                [
                    'code' => '12',
                    'name' => 'أصول ثابتة (عند التسجيل)',
                    'type' => 'asset',
                    'children' => [
                        ['code' => '121', 'name' => 'معدات وتجهيزات — عبر مصروفات أو أصول يدوية', 'type' => 'asset', 'source' => 'expenses', 'route' => 'admin.expenses.index', 'icon' => 'fa-toolbox'],
                    ],
                ],
            ],
        ],
        [
            'code' => '2',
            'name' => 'الخصوم',
            'type' => 'liability',
            'description' => 'التزامات على الأكاديمية تجاه المدربين والموظفين والمشتركين.',
            'children' => [
                [
                    'code' => '21',
                    'name' => 'خصوم متداولة',
                    'type' => 'liability',
                    'children' => [
                        ['code' => '211', 'name' => 'مستحقات مدربين — رواتب ونسب', 'type' => 'liability', 'source' => 'salaries', 'route' => 'admin.salaries.index', 'icon' => 'fa-money-check-alt'],
                        ['code' => '212', 'name' => 'طلبات سحب مدربين', 'type' => 'liability', 'source' => 'withdrawals', 'route' => 'admin.withdrawals.index', 'icon' => 'fa-hand-holding-usd'],
                        ['code' => '213', 'name' => 'التزامات اتفاقيات موظفين', 'type' => 'liability', 'source' => 'employee_agreements', 'route' => 'admin.employee-agreements.index', 'icon' => 'fa-users-cog'],
                        ['code' => '214', 'name' => 'إيراد مقدّم — اشتراكات وخدمات غير مقدّمة', 'type' => 'liability', 'source' => 'subscriptions', 'route' => 'admin.subscriptions.index', 'icon' => 'fa-calendar-alt'],
                    ],
                ],
            ],
        ],
        [
            'code' => '3',
            'name' => 'حقوق الملكية',
            'type' => 'equity',
            'description' => 'صافي أصول الأكاديمية (يُستنتج من القوائم والإدارة اليدوية).',
            'children' => [
                ['code' => '31', 'name' => 'رأس المال والاحتياطيات', 'type' => 'equity', 'description' => 'تُدار خارج النظام أو عبر تقارير مخصصة.', 'icon' => 'fa-balance-scale'],
            ],
        ],
        [
            'code' => '4',
            'name' => 'الإيرادات',
            'type' => 'revenue',
            'description' => 'إيرادات التشغيل من التعليم والخدمات المساندة.',
            'children' => [
                [
                    'code' => '41',
                    'name' => 'إيرادات الكورسات والتعليم',
                    'type' => 'revenue',
                    'children' => [
                        ['code' => '411', 'name' => 'كورسات أونلاين — طلبات وتسجيلات', 'type' => 'revenue', 'source' => 'orders_online', 'route' => 'admin.online-enrollments.index', 'icon' => 'fa-laptop'],
                        ['code' => '412', 'name' => 'كورسات أوفلاين — حجوزات وتسجيلات حضوري', 'type' => 'revenue', 'source' => 'offline_bookings', 'route' => 'admin.offline-course-bookings.index', 'icon' => 'fa-chalkboard-teacher'],
                        ['code' => '413', 'name' => 'مسارات تعليمية', 'type' => 'revenue', 'source' => 'learning_paths', 'route' => 'admin.learning-path-enrollments.index', 'icon' => 'fa-route'],
                        [
                            'code' => '414',
                            'name' => 'تقسيط الكورسات — عند تحصيل القسط',
                            'type' => 'revenue',
                            'description' => 'يُثبت الإيراد عادة عند تسجيل القسط كمدفوع (فاتورة + دفعة + معاملة دائن).',
                            'children' => [
                                ['code' => '4141', 'name' => 'لوحة التقسيط المحاسبية', 'type' => 'revenue', 'route' => 'admin.accounting.installments', 'icon' => 'fa-tachometer-alt'],
                                ['code' => '4142', 'name' => 'أقساط كورس أونلاين', 'type' => 'revenue', 'route' => 'admin.installments.agreements.index', 'icon' => 'fa-graduation-cap'],
                                ['code' => '4143', 'name' => 'أقساط كورس أوفلاين', 'type' => 'revenue', 'route' => 'admin.installments.agreements.manual-booking', 'icon' => 'fa-school'],
                                ['code' => '4144', 'name' => 'خطط التقسيط (هياكل الأقساط)', 'type' => 'revenue', 'route' => 'admin.installments.plans.index', 'icon' => 'fa-layer-group'],
                            ],
                        ],
                        ['code' => '415', 'name' => 'حجوزات مجموعات أونلاين — إيراد عند التأكيد', 'type' => 'revenue', 'source' => 'online_groups', 'route' => 'admin.online-course-bookings.index', 'icon' => 'fa-video'],
                    ],
                ],
                [
                    'code' => '42',
                    'name' => 'إيرادات أخرى',
                    'type' => 'revenue',
                    'children' => [
                        ['code' => '421', 'name' => 'اشتراكات المنصة', 'type' => 'revenue', 'source' => 'subscriptions', 'route' => 'admin.subscriptions.index', 'icon' => 'fa-calendar-alt'],
                        ['code' => '422', 'name' => 'خصومات وكوبونات (تخفيض إيراد)', 'type' => 'revenue', 'source' => 'coupons', 'route' => 'admin.coupons.index', 'icon' => 'fa-ticket-alt'],
                        ['code' => '423', 'name' => 'برامج إحالة', 'type' => 'revenue', 'source' => 'referrals', 'route' => 'admin.referral-programs.index', 'icon' => 'fa-gift'],
                    ],
                ],
            ],
        ],
        [
            'code' => '5',
            'name' => 'المصروفات',
            'type' => 'expense',
            'description' => 'تكاليف التشغيل والتزامات مدينة في المعاملات.',
            'children' => [
                [
                    'code' => '51',
                    'name' => 'مصروفات تشغيلية',
                    'type' => 'expense',
                    'children' => [
                        ['code' => '511', 'name' => 'مصروفات مسجلة — جدول المصروفات', 'type' => 'expense', 'source' => 'expenses', 'route' => 'admin.expenses.index', 'icon' => 'fa-receipt'],
                        ['code' => '512', 'name' => 'معاملات مدينة — سجل المعاملات', 'type' => 'expense', 'source' => 'transactions', 'route' => 'admin.transactions.index', 'icon' => 'fa-exchange-alt'],
                        ['code' => '515', 'name' => 'عمولات بوابات الدفع (قيد fee)', 'type' => 'expense', 'source' => 'gateway_fees', 'route' => 'admin.accounting.gateway-operations', 'icon' => 'fa-percentage'],
                        ['code' => '513', 'name' => 'أتعاب مدربين — حسابات المدربين', 'type' => 'expense', 'source' => 'instructor_accounts', 'route' => 'admin.accounting.instructor-accounts.index', 'icon' => 'fa-user-tie'],
                    ],
                ],
            ],
        ],
    ],
];
