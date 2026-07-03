@extends('layouts.admin')

@section('title', 'لوحة الاستثمار - Mindlytics')
@section('header', 'قسم الاستثمار')

@section('content')
@include('admin.investment._styles')

@php $o = $overview; @endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')

    @include('admin.investment._header', [
        'title' => 'قسم الاستثمار',
        'subtitle' => 'إدارة خطط الاستثمار، طلبات المستثمرين، والإطار القانوني — قسم مستقل بالكامل',
        'icon' => 'fas fa-chart-pie',
        'actions' => '<a href="' . route('admin.investment.plans.create') . '" class="' . $invBtnPrimary . '"><i class="fas fa-plus"></i><span>خطة جديدة</span></a>',
    ])

    @include('admin.investment._stats-grid', ['cards' => [
        ['label' => 'الخطط الاستثمارية', 'value' => number_format($o['plans_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => number_format($o['plans_active'] ?? 0) . ' نشطة'],
        ['label' => 'طلبات المستثمرين', 'value' => number_format($o['inquiries_total'] ?? 0), 'icon' => 'fas fa-inbox', 'description' => number_format($o['pending'] ?? 0) . ' جديد'],
        ['label' => 'قيد المراجعة', 'value' => number_format($o['under_review'] ?? 0), 'icon' => 'fas fa-search', 'description' => 'تحتاج متابعة'],
        ['label' => 'مبالغ مقترحة', 'value' => number_format($o['proposed_total'] ?? 0, 0), 'icon' => 'fas fa-coins', 'description' => 'EGP — طلبات نشطة'],
    ]])

    @include('admin.investment._nav', ['active' => 'dashboard'])

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-filter',
            'title' => 'البحث والفلترة',
            'subtitle' => 'ابحث في الخطط وفلتر حسب الحالة',
        ])
        <div class="px-6 py-5">
            <form method="GET" action="{{ route('admin.investment.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $invLabelClass }}"><i class="fas fa-search text-amber-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الخطة أو الرابط (slug)" class="{{ $invInputClass }}">
                </div>
                <div>
                    <label class="{{ $invLabelClass }}"><i class="fas fa-toggle-on text-amber-600 text-sm"></i> الحالة</label>
                    <select name="status" class="{{ $invSelectClass }}">
                        <option value="">جميع الحالات</option>
                        <option value="active" @selected(request('status') === 'active')>نشطة</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>متوقفة</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $invBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.investment.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors" title="مسح الفلتر"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-layer-group',
            'title' => 'قائمة الخطط الاستثمارية',
            'subtitle' => '<span class="font-bold text-amber-600">' . $plans->total() . '</span> خطة',
            'actions' => '<a href="' . route('admin.investment.inquiries.index', ['status' => 'pending']) . '" class="' . $invBtnSecondary . '"><i class="fas fa-inbox text-amber-600"></i><span>طلبات جديدة (' . number_format($o['pending'] ?? 0) . ')</span></a>',
        ])
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-layer-group text-amber-600"></i><span>الخطة</span></div></th>
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-tag text-amber-600"></i><span>النوع</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-coins text-amber-600"></i><span>الحد الأدنى</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-inbox text-amber-600"></i><span>الطلبات</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-toggle-on text-amber-600"></i><span>الحالة</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-cog text-amber-600"></i><span>الإجراءات</span></div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($plans as $plan)
                        <tr class="inv-table-row">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 text-base">{{ $plan->title }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5" dir="ltr">{{ $plan->slug }}</p>
                                @if($plan->is_featured)
                                    <span class="inline-block mt-1 text-[10px] font-bold bg-amber-200 text-amber-900 px-2 py-0.5 rounded-full">مميز</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-800">{{ $plan->planTypeLabel() }}</p>
                                <p class="text-xs text-slate-500">{{ $plan->riskLevelLabel() }}</p>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800 tabular-nums">{{ $plan->formattedMinInvestment() }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums">{{ $plan->inquiries_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $plan->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $plan->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.investment.plans.show', $plan) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                    <a href="{{ route('admin.investment.plans.edit', $plan) }}" class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                    <a href="{{ route('investment.show', $plan->slug) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="معاينة عامة"><i class="fas fa-external-link-alt text-sm"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center"><i class="fas fa-layer-group text-3xl text-amber-600"></i></div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg mb-1">لا توجد خطط بعد</p>
                                        <p class="text-sm text-slate-600 font-medium">أنشئ أول خطة استثمارية لبدء استقبال الطلبات</p>
                                    </div>
                                    <a href="{{ route('admin.investment.plans.create') }}" class="{{ $invBtnPrimary }}"><i class="fas fa-plus"></i><span>خطة جديدة</span></a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $plans->links() }}</div>
        @endif
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="inv-card rounded-2xl shadow-lg overflow-hidden">
            @include('admin.investment._section-head', [
                'icon' => 'fas fa-inbox',
                'title' => 'آخر طلبات المستثمرين',
                'subtitle' => 'أحدث الطلبات الواردة',
                'actions' => '<a href="' . route('admin.investment.inquiries.index') . '" class="text-sm font-semibold text-amber-700 hover:underline">عرض الكل</a>',
            ])
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right">المستثمر</th>
                            <th class="px-4 py-3 text-right">الخطة</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentInquiries as $inq)
                            <tr class="inv-table-row cursor-pointer" onclick="window.location='{{ route('admin.investment.inquiries.show', $inq) }}'">
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-900">{{ $inq->full_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $inq->email }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $inq->plan?->title ?? '—' }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800">{{ $inq->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-slate-500 font-medium">لا توجد طلبات بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="{{ $invSectionClass }}">
            @include('admin.investment._section-head', [
                'icon' => 'fas fa-bolt',
                'title' => 'إجراءات سريعة',
                'subtitle' => 'الوصول السريع لأقسام الاستثمار',
            ])
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                <a href="{{ route('admin.investment.plans.create') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-amber-300 hover:shadow-md transition-all inv-card">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm mb-3"><i class="fas fa-plus text-lg"></i></div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">خطة جديدة</h4>
                    <p class="text-xs text-slate-600">إنشاء فرصة استثمارية جديدة</p>
                </a>
                <a href="{{ route('admin.investment.inquiries.index') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-amber-300 hover:shadow-md transition-all inv-card">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm mb-3"><i class="fas fa-handshake text-lg"></i></div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">طلبات المستثمرين</h4>
                    <p class="text-xs text-slate-600">مراجعة وتحديث حالة الطلبات</p>
                </a>
                <a href="{{ route('admin.investment.policies.edit') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-amber-300 hover:shadow-md transition-all inv-card">
                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 shadow-sm mb-3"><i class="fas fa-gavel text-lg"></i></div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">الإطار القانوني</h4>
                    <p class="text-xs text-slate-600">الشروط والسياسات والامتثال</p>
                </a>
                <a href="{{ route('investment.index') }}" target="_blank" rel="noopener" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-amber-300 hover:shadow-md transition-all inv-card">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 shadow-sm mb-3"><i class="fas fa-external-link-alt text-lg"></i></div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">الصفحة العامة</h4>
                    <p class="text-xs text-slate-600">معاينة صفحة الاستثمار للزوار</p>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
