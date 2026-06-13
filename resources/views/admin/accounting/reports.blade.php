@extends('layouts.admin')

@section('title', 'التقارير المحاسبية - Mindlytics')
@section('header', 'التقارير المحاسبية')

@section('content')
@php
    $reportQuery = ['period' => $period];
    if ($period === 'custom') {
        $reportQuery['start_date'] = $filter['filterStart'] ?? $startDate->format('Y-m-d');
        $reportQuery['end_date'] = $filter['filterEnd'] ?? $endDate->format('Y-m-d');
    }
@endphp
<div class="w-full space-y-6">
    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-sky-200 bg-sky-50/80 px-4 py-3 text-sm">
        <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 font-semibold text-sky-800 hover:text-sky-950">
            <i class="fas fa-th-large"></i>
            مركز المحاسبة
        </a>
        <span class="text-slate-300">|</span>
        <a href="{{ route('admin.accounting.chart') }}" class="inline-flex items-center gap-2 font-semibold text-sky-800 hover:text-sky-950">
            <i class="fas fa-sitemap"></i>
            شجرة الحسابات
        </a>
        <span class="text-slate-300">|</span>
        <a href="{{ route('admin.accounting.receivables') }}" class="inline-flex items-center gap-2 font-semibold text-amber-800 hover:text-amber-950">
            <i class="fas fa-hand-holding-usd"></i>
            المديونية
        </a>
    </div>
    <!-- فلترة الفترة الزمنية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">التقارير المحاسبية</h2>
                    <p class="text-sm text-slate-500 mt-1">تقارير شاملة عن جميع العمليات المالية في الأكاديمية مع تصدير Excel منسّق</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'all'])) }}" 
                       class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-file-excel"></i>
                        تصدير Excel شامل
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <i class="fas fa-download"></i>
                            تصدير محدد
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute left-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-200 z-50">
                            <div class="p-2 space-y-1">
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'summary'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-chart-pie w-4"></i>
                                    الملخص المالي
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'invoices'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-file-invoice w-4"></i>
                                    الفواتير
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'payments'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-money-bill-wave w-4"></i>
                                    المدفوعات
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'transactions'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-exchange-alt w-4"></i>
                                    المعاملات
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'expenses'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-receipt w-4"></i>
                                    المصروفات
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'wallets'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-wallet w-4"></i>
                                    المحافظ
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'orders'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-shopping-cart w-4"></i>
                                    الطلبات
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'subscriptions'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-calendar-check w-4"></i>
                                    الاشتراكات
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'withdrawals'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-hand-holding-usd w-4"></i>
                                    سحوبات المدربين
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'installments'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-handshake w-4"></i>
                                    اتفاقيات التقسيط
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'offline_enrollments'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-building w-4"></i>
                                    تسجيلات أوفلاين
                                </a>
                                <a href="{{ route('admin.accounting.reports.export', array_merge($reportQuery, ['type' => 'chart'])) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-sitemap w-4"></i>
                                    شجرة الحسابات (جدول)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.accounting.reports') }}" id="reportPeriodForm" class="space-y-5">
                @include('admin.accounting.partials.report-period-filter')
            </form>
        </div>
    </section>

    <!-- الإحصائيات الرئيسية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900">ملخص مالي شامل</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $periodLabel ?? '' }} — من {{ $startDate->format('Y-m-d') }} إلى {{ $endDate->format('Y-m-d') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            <!-- إجمالي الإيرادات -->
            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-green-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">إجمالي الإيرادات</p>
                        <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($stats['total_revenue'], 2) }} ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-arrow-down text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- إجمالي المصروفات -->
            <div class="rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-red-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-rose-600">إجمالي المصروفات</p>
                        <p class="mt-3 text-3xl font-black text-rose-700">{{ number_format($stats['total_expenses'], 2) }} ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-rose-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- الربح الصافي -->
            <div class="rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-blue-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-sky-600">الربح الصافي</p>
                        <p class="mt-3 text-3xl font-black {{ $stats['net_profit'] >= 0 ? 'text-sky-700' : 'text-rose-700' }}">
                            {{ number_format($stats['net_profit'], 2) }} ج.م
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-sky-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- نسبة الربحية -->
            <div class="rounded-2xl border border-purple-200 bg-gradient-to-br from-purple-50 to-indigo-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-purple-600">نسبة الربحية</p>
                        <p class="mt-3 text-3xl font-black text-purple-700">
                            @if($stats['total_revenue'] > 0)
                                {{ number_format(($stats['net_profit'] / $stats['total_revenue']) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-percentage text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- صف إضافي: محافظ المنصة + الطلبات -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8 pt-0">
            <div class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-purple-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-violet-600">محافظ المنصة</p>
                        <p class="mt-3 text-2xl font-black text-violet-700">{{ $stats['wallet_stats']['total_wallets'] }}</p>
                        <p class="text-xs text-slate-600 mt-1">{{ $stats['wallet_stats']['active_wallets'] }} نشطة</p>
                    </div>
                    <div class="w-12 h-12 bg-violet-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500">إجمالي الأرصدة: <strong>{{ number_format($stats['wallet_stats']['total_balance'], 2) }} ج.م</strong></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">الطلبات (الفترة)</p>
                        <p class="mt-3 text-2xl font-black text-amber-700">{{ $stats['order_stats']['total_orders'] }}</p>
                        <p class="text-xs text-slate-600 mt-1">{{ $stats['order_stats']['approved_orders'] }} معتمدة · {{ $stats['order_stats']['pending_orders'] }} معلقة</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500">مبالغ معتمدة: <strong>{{ number_format($stats['order_stats']['approved_amount'], 2) }} ج.م</strong></p>
            </div>
        </div>
        @php $ia = $stats['invoice_amounts'] ?? []; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8 pt-0">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold text-slate-500">إجمالي الفواتير (مبلغ)</p>
                <p class="mt-2 text-xl font-black text-slate-900">{{ number_format($ia['invoiced_total'] ?? 0, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold text-emerald-700">المحصّل فعلياً</p>
                <p class="mt-2 text-xl font-black text-emerald-800">{{ number_format($ia['collected_total'] ?? 0, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-xs font-semibold text-amber-700">متبقي على العملاء</p>
                <p class="mt-2 text-xl font-black text-amber-800">{{ number_format($ia['outstanding_total'] ?? 0, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">
                <p class="text-xs font-semibold text-violet-700">إجمالي الخصومات</p>
                <p class="mt-2 text-xl font-black text-violet-800">{{ number_format($ia['discount_total'] ?? 0, 2) }} ج.م</p>
            </div>
        </div>
        @php $as = $stats['academy_stats'] ?? []; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 p-5 sm:p-8 pt-0">
            <div class="rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-50 to-cyan-50 p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-600">الأكاديمية — اشتراكات جديدة</p>
                <p class="mt-2 text-2xl font-black text-teal-800">{{ $as['subscriptions_new_period'] ?? 0 }}</p>
                <p class="text-xs text-slate-600 mt-1">قيمة: {{ number_format($as['subscriptions_value_period'] ?? 0, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-fuchsia-200 bg-gradient-to-br from-fuchsia-50 to-pink-50 p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-fuchsia-600">التقسيط</p>
                <p class="mt-2 text-2xl font-black text-fuchsia-800">{{ $as['installment_agreements_active'] ?? 0 }} <span class="text-sm font-semibold text-fuchsia-600">اتفاقية نشطة</span></p>
                <p class="text-xs text-slate-600 mt-1">عقود بالفترة: {{ number_format($as['installment_contracts_value_period'] ?? 0, 2) }} ج.م · أقساط معلقة: {{ number_format($as['installment_pending_scheduled'] ?? 0, 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-stone-200 bg-gradient-to-br from-stone-50 to-neutral-50 p-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-600">سحوبات وأوفلاين</p>
                <p class="text-sm text-slate-700 mt-2">سحب مكتمل بالفترة: <strong>{{ number_format($as['withdrawals_completed_period'] ?? 0, 2) }} ج.م</strong></p>
                <p class="text-sm text-slate-700">سحب معلق: <strong>{{ number_format($as['withdrawals_pending_amount'] ?? 0, 2) }} ج.م</strong></p>
                <p class="text-xs text-slate-500 mt-2">أوفلاين محصّل بالفترة: {{ number_format($as['offline_collected_period'] ?? 0, 2) }} ج.م · متبقي إجمالي: {{ number_format($as['offline_outstanding_total'] ?? 0, 2) }} ج.م</p>
            </div>
        </div>
    </section>

    <!-- الإحصائيات التفصيلية -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- الفواتير -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">الفواتير</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">إجمالي الفواتير</span>
                    <span class="text-lg font-bold text-slate-900">{{ number_format($stats['total_invoices']) }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">مدفوعة</span>
                    <span class="text-lg font-bold text-emerald-600">{{ number_format($stats['paid_invoices']) }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">معلقة</span>
                    <span class="text-lg font-bold text-amber-600">{{ number_format($stats['pending_invoices']) }}</span>
                </div>
            </div>
        </section>

        <!-- المدفوعات -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">المدفوعات</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">إجمالي المدفوعات</span>
                    <span class="text-lg font-bold text-slate-900">{{ number_format($stats['total_payments']) }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">مكتملة</span>
                    <span class="text-lg font-bold text-emerald-600">{{ number_format($stats['completed_payments']) }}</span>
                </div>
            </div>
        </section>

        <!-- المعاملات -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">المعاملات</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">إجمالي المعاملات</span>
                    <span class="text-lg font-bold text-slate-900">{{ number_format($stats['total_transactions']) }}</span>
                </div>
            </div>
        </section>
    </div>

    <!-- أقسام التقارير — عرض كل قسم في صفحة منفصلة -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">عرض التفاصيل حسب القسم</h3>
            <p class="text-sm text-slate-500 mt-1">كل قسم في صفحة منفصلة مع ترقيم الصفحات وتصدير Excel</p>
        </div>
        <div class="p-5 sm:p-8">
            @php $query = $reportQuery; @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.accounting.reports.invoices', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center"><i class="fas fa-file-invoice text-sky-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">الفواتير</p>
                        <p class="text-xs text-slate-500">{{ $stats['total_invoices'] }} فاتورة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="{{ route('admin.accounting.reports.payments', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center"><i class="fas fa-money-bill-wave text-emerald-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المدفوعات</p>
                        <p class="text-xs text-slate-500">{{ $stats['total_payments'] }} دفعة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="{{ route('admin.accounting.reports.transactions', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center"><i class="fas fa-exchange-alt text-violet-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المعاملات المالية</p>
                        <p class="text-xs text-slate-500">{{ $stats['total_transactions'] }} معاملة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="{{ route('admin.accounting.reports.expenses', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center"><i class="fas fa-receipt text-rose-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المصروفات</p>
                        <p class="text-xs text-slate-500">عرض المصروفات في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="{{ route('admin.accounting.reports.wallets', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-wallet text-amber-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">محافظ المنصة</p>
                        <p class="text-xs text-slate-500">{{ $stats['wallet_stats']['total_wallets'] }} محفظة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="{{ route('admin.accounting.reports.orders', $query) }}" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-shopping-cart text-indigo-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">الطلبات</p>
                        <p class="text-xs text-slate-500">{{ $stats['order_stats']['total_orders'] }} طلب في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
            </div>
        </div>
    </section>

    @php
        $dr = $detailedReport ?? [];
        $pl = $dr['profit_loss'] ?? [];
        $statusLabels = ['paid' => 'مدفوعة', 'pending' => 'معلقة', 'overdue' => 'متأخرة', 'cancelled' => 'ملغاة', 'draft' => 'مسودة'];
        $typeLabels = ['course' => 'كورس', 'offline_course' => 'كورس أوفلاين', 'subscription' => 'اشتراك', 'installment' => 'تقسيط', 'other' => 'أخرى'];
        $paymentMethodLabels = ['cash' => 'نقدي', 'wallet' => 'محفظة', 'bank_transfer' => 'تحويل بنكي', 'online' => 'أونلاين', 'card' => 'بطاقة'];
    @endphp

    <!-- قائمة الدخل التفصيلية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">قائمة الدخل (الفترة)</h3>
            <p class="text-sm text-slate-500 mt-1">ملخص الإيرادات والمصروفات والسحوبات خلال الفترة المحددة</p>
        </div>
        <div class="p-5 sm:p-8 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 text-right font-semibold">البند</th>
                        <th class="py-3 px-4 text-left font-semibold">المبلغ (ج.م)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="py-3 px-4 text-emerald-700 font-medium">+ إيرادات محصّلة (مدفوعات مكتملة)</td><td class="py-3 px-4 text-left font-bold text-emerald-700">{{ number_format($pl['revenue'] ?? 0, 2) }}</td></tr>
                    <tr><td class="py-3 px-4 text-slate-700 font-medium">− مصروفات من إيرادات الكورسات</td><td class="py-3 px-4 text-left font-bold">{{ number_format($pl['expenses_from_revenue'] ?? 0, 2) }}</td></tr>
                    <tr class="bg-emerald-50/50"><td class="py-3 px-4 font-semibold text-emerald-900">= صافي تشغيلي (بر الأمان)</td><td class="py-3 px-4 text-left font-bold {{ ($pl['operational_net'] ?? 0) >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">{{ number_format($pl['operational_net'] ?? 0, 2) }}</td></tr>
                    <tr><td class="py-3 px-4 text-amber-700 font-medium">− مصروفات من جيب الشركة (تمويل ذاتي)</td><td class="py-3 px-4 text-left font-bold text-amber-700">{{ number_format($pl['expenses_out_of_pocket'] ?? 0, 2) }}</td></tr>
                    <tr><td class="py-3 px-4 text-rose-700 font-medium">− سحوبات مدربين مكتملة</td><td class="py-3 px-4 text-left font-bold text-rose-700">{{ number_format($pl['withdrawals'] ?? 0, 2) }}</td></tr>
                    <tr class="bg-sky-50"><td class="py-3 px-4 font-bold text-sky-900">= صافي نهائي</td><td class="py-3 px-4 text-left font-black text-sky-900">{{ number_format($pl['net'] ?? 0, 2) }}</td></tr>
                </tbody>
            </table>
            @if(!empty($pl['break_even']['label']))
            <p class="mt-4 text-sm rounded-xl border px-4 py-3 {{ ($pl['break_even']['tone'] ?? '') === 'good' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : (($pl['break_even']['tone'] ?? '') === 'bad' ? 'border-rose-200 bg-rose-50 text-rose-900' : 'border-amber-200 bg-amber-50 text-amber-900') }}">
                <strong>بر الأمان:</strong> {{ $pl['break_even']['label'] }} — {{ $pl['break_even']['detail'] ?? '' }}
            </p>
            @endif
        </div>
    </section>

    <!-- تفصيل الفواتير والمدفوعات -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-5 border-b border-slate-200"><h3 class="font-bold text-slate-900">الفواتير حسب الحالة</h3></div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="text-slate-500 border-b"><th class="py-2 text-right">الحالة</th><th class="py-2 text-right">العدد</th><th class="py-2 text-left">المبلغ</th><th class="py-2 text-left">خصم</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dr['invoice_by_status'] ?? [] as $row)
                        <tr>
                            <td class="py-2">{{ $statusLabels[$row->status] ?? $row->status }}</td>
                            <td class="py-2">{{ $row->count }}</td>
                            <td class="py-2 text-left font-semibold">{{ number_format($row->total_amount, 2) }}</td>
                            <td class="py-2 text-left text-amber-700">{{ number_format($row->discount_amount ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 text-center text-slate-500">لا توجد فواتير في هذه الفترة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-5 border-b border-slate-200"><h3 class="font-bold text-slate-900">الفواتير حسب النوع</h3></div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="text-slate-500 border-b"><th class="py-2 text-right">النوع</th><th class="py-2 text-right">العدد</th><th class="py-2 text-left">المبلغ</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dr['invoice_by_type'] ?? [] as $row)
                        <tr>
                            <td class="py-2">{{ $typeLabels[$row->type] ?? ($row->type ?: '—') }}</td>
                            <td class="py-2">{{ $row->count }}</td>
                            <td class="py-2 text-left font-semibold">{{ number_format($row->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-6 text-center text-slate-500">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-5 border-b border-slate-200"><h3 class="font-bold text-slate-900">المدفوعات حسب الحالة</h3></div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="text-slate-500 border-b"><th class="py-2 text-right">الحالة</th><th class="py-2 text-right">العدد</th><th class="py-2 text-left">المبلغ</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dr['payments_by_status'] ?? [] as $row)
                        <tr>
                            <td class="py-2">{{ $row->status }}</td>
                            <td class="py-2">{{ $row->count }}</td>
                            <td class="py-2 text-left font-semibold">{{ number_format($row->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-6 text-center text-slate-500">لا توجد مدفوعات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-5 border-b border-slate-200"><h3 class="font-bold text-slate-900">تحصيلات حسب طريقة الدفع</h3></div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="text-slate-500 border-b"><th class="py-2 text-right">الطريقة</th><th class="py-2 text-right">العدد</th><th class="py-2 text-left">المبلغ</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dr['payments_by_method'] ?? [] as $row)
                        <tr>
                            <td class="py-2">{{ $paymentMethodLabels[$row->payment_method] ?? ($row->payment_method ?: '—') }}</td>
                            <td class="py-2">{{ $row->count }}</td>
                            <td class="py-2 text-left font-semibold text-emerald-700">{{ number_format($row->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-6 text-center text-slate-500">لا توجد تحصيلات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- آخر العمليات -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden xl:col-span-1">
            <div class="px-5 py-5 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-900">آخر الفواتير</h3>
                <a href="{{ route('admin.accounting.reports.invoices', $query) }}" class="text-xs text-sky-600 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($dr['recent_invoices'] ?? [] as $inv)
                <div class="px-5 py-3 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-slate-900">{{ $inv->invoice_number }}</span>
                        <span class="font-bold">{{ number_format($inv->total_amount, 2) }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $inv->user->name ?? '—' }} · {{ $inv->created_at->format('Y-m-d') }}</p>
                </div>
                @empty
                <p class="p-6 text-center text-slate-500 text-sm">لا توجد فواتير</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden xl:col-span-1">
            <div class="px-5 py-5 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-900">آخر المدفوعات</h3>
                <a href="{{ route('admin.accounting.reports.payments', $query) }}" class="text-xs text-sky-600 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($dr['recent_payments'] ?? [] as $pay)
                <div class="px-5 py-3 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-emerald-800">{{ number_format($pay->amount, 2) }} ج.م</span>
                        <span class="text-xs text-slate-500">{{ $pay->paid_at?->format('Y-m-d') }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $pay->user->name ?? '—' }} · {{ $paymentMethodLabels[$pay->payment_method] ?? $pay->payment_method }}</p>
                </div>
                @empty
                <p class="p-6 text-center text-slate-500 text-sm">لا توجد مدفوعات</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden xl:col-span-1">
            <div class="px-5 py-5 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-900">آخر المصروفات</h3>
                <a href="{{ route('admin.accounting.reports.expenses', $query) }}" class="text-xs text-sky-600 hover:underline">عرض الكل</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($dr['recent_expenses'] ?? [] as $exp)
                <div class="px-5 py-3 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-rose-800">{{ number_format($exp->amount, 2) }} ج.م</span>
                        <span class="text-xs text-slate-500">{{ $exp->expense_date?->format('Y-m-d') }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $exp->title ?? '—' }} · {{ \App\Models\Expense::categoryLabel($exp->category) }}</p>
                </div>
                @empty
                <p class="p-6 text-center text-slate-500 text-sm">لا توجد مصروفات</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- الإيرادات والمصروفات اليومية -->
    @if(count($dailyData['days'] ?? []) > 0)
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">الإيرادات والمصروفات اليومية</h3>
            <p class="text-sm text-slate-500 mt-1">عرض يومي ضمن الفترة (حتى 90 يوماً)</p>
        </div>
        <div class="p-5 sm:p-8 space-y-3 max-h-[28rem] overflow-y-auto">
            @foreach($dailyData['days'] as $index => $day)
            @php
                $dayRev = $dailyData['revenues'][$index] ?? 0;
                $dayExp = $dailyData['expenses'][$index] ?? 0;
                $dayMax = max($dayRev, $dayExp, 1);
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1 text-xs">
                    <span class="font-medium text-slate-700">{{ $day }}</span>
                    <div class="flex gap-3">
                        <span class="text-emerald-600">+{{ number_format($dayRev, 2) }}</span>
                        <span class="text-rose-600">−{{ number_format($dayExp, 2) }}</span>
                        <span class="font-semibold text-sky-700">{{ number_format($dayRev - $dayExp, 2) }}</span>
                    </div>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden flex">
                    <div class="bg-emerald-500" style="width: {{ ($dayRev / $dayMax) * 100 }}%"></div>
                    <div class="bg-rose-400" style="width: {{ ($dayExp / $dayMax) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- تقارير الإيرادات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">تفاصيل الإيرادات</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الإيرادات من المدفوعات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب طريقة الدفع</h4>
                    <div class="space-y-3">
                        @forelse($revenueReports['from_payments'] as $item)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $item->payment_method }}</p>
                                <p class="text-xs text-slate-500">{{ $item->count }} دفعة</p>
                            </div>
                            <p class="text-sm font-bold text-emerald-600">{{ number_format($item->total, 2) }} ج.م</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>

                <!-- الإيرادات من المعاملات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب الفئة</h4>
                    <div class="space-y-3">
                        @forelse($revenueReports['from_transactions'] as $item)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $item->category ?? 'غير محدد' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->count }} معاملة</p>
                            </div>
                            <p class="text-sm font-bold text-emerald-600">{{ number_format($item->total, 2) }} ج.م</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- تقارير المصروفات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">تفاصيل المصروفات</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المصروفات من جدول المصروفات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب الفئة</h4>
                    <div class="space-y-3">
                        @forelse($expenseReports['from_expenses'] as $item)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ \App\Models\Expense::categoryLabel($item->category) }}</p>
                                <p class="text-xs text-slate-500">{{ $item->count }} مصروف</p>
                            </div>
                            <p class="text-sm font-bold text-rose-600">{{ number_format($item->total, 2) }} ج.م</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>

                <!-- المصروفات من المعاملات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">من المعاملات</h4>
                    <div class="space-y-3">
                        @forelse($expenseReports['from_transactions'] as $item)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $item->category ?? 'غير محدد' }}</p>
                                <p class="text-xs text-slate-500">{{ $item->count }} معاملة</p>
                            </div>
                            <p class="text-sm font-bold text-rose-600">{{ number_format($item->total, 2) }} ج.م</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- الرسم البياني الشهري -->
    @if(count($monthlyData['months']) > 0)
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">الإيرادات والمصروفات الشهرية</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="space-y-4">
                @foreach($monthlyData['months'] as $index => $month)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($month . '-01')->format('Y-m') }}</span>
                        <div class="flex items-center gap-4 text-xs">
                            <span class="text-emerald-600">إيرادات: {{ number_format($monthlyData['revenues'][$index], 2) }} ج.م</span>
                            <span class="text-rose-600">مصروفات: {{ number_format($monthlyData['expenses'][$index], 2) }} ج.م</span>
                            <span class="text-sky-600 font-semibold">صافي: {{ number_format($monthlyData['revenues'][$index] - $monthlyData['expenses'][$index], 2) }} ج.م</span>
                        </div>
                    </div>
                    <div class="relative h-8 bg-slate-100 rounded-full overflow-hidden">
                        <div class="absolute inset-0 flex">
                            <div class="bg-emerald-500" style="width: {{ $monthlyData['revenues'][$index] > 0 ? min(($monthlyData['revenues'][$index] / max($monthlyData['revenues'][$index], $monthlyData['expenses'][$index])) * 100, 100) : 0 }}%"></div>
                            <div class="bg-rose-500" style="width: {{ $monthlyData['expenses'][$index] > 0 ? min(($monthlyData['expenses'][$index] / max($monthlyData['revenues'][$index], $monthlyData['expenses'][$index])) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>
@endsection

