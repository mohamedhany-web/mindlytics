@extends('layouts.admin')

@section('title', 'الخطط الاستثمارية')
@section('header', 'الخطط الاستثمارية')

@section('content')
@include('admin.investment._styles')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')

    @include('admin.investment._header', [
        'title' => 'الخطط الاستثمارية',
        'subtitle' => 'تعريف فرص الاستثمار، الحدود، العوائد، والشروط لكل خطة',
        'icon' => 'fas fa-layer-group',
        'actions' => '<a href="' . route('admin.investment.plans.create') . '" class="' . $invBtnPrimary . '"><i class="fas fa-plus"></i><span>خطة جديدة</span></a>',
    ])

    @include('admin.investment._stats-grid', ['cards' => [
        ['label' => 'إجمالي الخطط', 'value' => number_format($overview['plans_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => number_format($overview['plans_active'] ?? 0) . ' نشطة'],
        ['label' => 'طلبات المستثمرين', 'value' => number_format($overview['inquiries_total'] ?? 0), 'icon' => 'fas fa-inbox', 'description' => number_format($overview['pending'] ?? 0) . ' جديد'],
        ['label' => 'قيد المراجعة', 'value' => number_format($overview['under_review'] ?? 0), 'icon' => 'fas fa-search', 'description' => 'تحتاج متابعة'],
        ['label' => 'مبالغ مقترحة', 'value' => number_format($overview['proposed_total'] ?? 0, 0), 'icon' => 'fas fa-coins', 'description' => 'EGP'],
    ]])

    @include('admin.investment._nav', ['active' => 'plans'])

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-filter',
            'title' => 'البحث والفلترة',
        ])
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $invLabelClass }}"><i class="fas fa-search text-amber-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الخطة أو الرابط" class="{{ $invInputClass }}">
                </div>
                <div>
                    <label class="{{ $invLabelClass }}"><i class="fas fa-tag text-amber-600 text-sm"></i> نوع الاستثمار</label>
                    <select name="plan_type" class="{{ $invSelectClass }}">
                        <option value="">كل الأنواع</option>
                        @foreach(\App\Models\InvestmentPlan::planTypeLabels() as $val => $lbl)
                            <option value="{{ $val }}" @selected(request('plan_type') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $invBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->anyFilled(['search', 'plan_type']))
                        <a href="{{ route('admin.investment.plans.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-layer-group',
            'title' => 'قائمة الخطط',
            'subtitle' => '<span class="font-bold text-amber-600">' . $plans->total() . '</span> خطة',
        ])
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الخطة</th>
                        <th class="px-6 py-4 text-right">النوع</th>
                        <th class="px-6 py-4 text-center">الحد الأدنى</th>
                        <th class="px-6 py-4 text-center">الطلبات</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($plans as $plan)
                        <tr class="inv-table-row">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $plan->title }}</p>
                                <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $plan->slug }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $plan->planTypeLabel() }}</td>
                            <td class="px-6 py-4 text-center font-bold tabular-nums">{{ $plan->formattedMinInvestment() }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums">{{ $plan->inquiries_count }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $plan->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $plan->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.investment.plans.show', $plan) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                    <a href="{{ route('admin.investment.plans.edit', $plan) }}" class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg shadow-sm" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500 font-medium">لا توجد خطط بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $plans->links() }}</div>@endif
    </section>
</div>
@endsection
