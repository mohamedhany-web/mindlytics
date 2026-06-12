@extends('layouts.admin')

@section('title', 'خطط التقسيط - Mindlytics')
@section('header', 'خطط التقسيط والاشتراكات')

@section('content')
@php
    $statusColors = [
        true => 'bg-emerald-100 text-emerald-700',
        false => 'bg-rose-100 text-rose-700',
    ];
    $pageStats = [
        ['label' => 'إجمالي القيم', 'value' => number_format($stats['total_amount'] ?? 0, 2), 'desc' => 'ج.م — مبالغ الخطط', 'icon' => 'fas fa-coins', 'theme' => 'sky'],
        ['label' => 'الدفعات المقدمة', 'value' => number_format($stats['total_deposit'] ?? 0, 2), 'desc' => 'ج.م — عند الاشتراك', 'icon' => 'fas fa-piggy-bank', 'theme' => 'emerald'],
        ['label' => 'متوسط الأقساط', 'value' => number_format($stats['average_installments'] ?? 0, 1), 'desc' => 'دفعة لكل خطة', 'icon' => 'fas fa-chart-area', 'theme' => 'purple'],
        ['label' => 'خطط جديدة (الشهر)', 'value' => number_format($monthlyNew ?? 0), 'desc' => number_format($monthlyAmount ?? 0, 2) . ' ج.م', 'icon' => 'fas fa-calendar-plus', 'theme' => 'amber'],
    ];
@endphp
<div class="space-y-6">
    @include('admin.installments.partials.header', [
        'title' => 'خطط التقسيط',
        'description' => 'أداء خطط الدفع، القيم الإجمالية، وعدد الاتفاقيات المرتبطة.',
        'icon' => 'fa-layer-group',
        'iconGradient' => 'from-sky-500 to-blue-600',
        'meta' => 'إجمالي ' . number_format($stats['total'] ?? 0) . ' خطة · نشطة ' . number_format($stats['active'] ?? 0) . ' · توليد تلقائي ' . number_format($stats['auto_generate'] ?? 0),
        'actions' => [
            ['route' => 'admin.installments.plans.create', 'label' => 'إضافة خطة', 'icon' => 'fa-plus', 'style' => 'primary'],
        ],
    ])

    @include('admin.installments.partials.nav', ['active' => 'plans'])

    @include('admin.installments.partials.stats', ['stats' => $pageStats])

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">توزيع حسب دورية السداد</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                        <i class="fas fa-clock text-xs"></i>
                        {{ $frequencyBreakdown->sum('plans_count') }} خطة
                    </span>
                </div>
                <div class="space-y-4">
                    @forelse($frequencyBreakdown as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-100 text-sky-600">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $unitLabels[$item->frequency_unit] ?? $item->frequency_unit }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($item->plans_count) }} خطة</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-sky-600">{{ number_format($item->total_amount, 2) }} ج.م</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">لا توجد بيانات للتوزيع حالياً.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-slate-900 mb-4">أعلى الخطط قيمة</h2>
                <div class="space-y-4">
                    @forelse($highValuePlans as $plan)
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">{{ $plan->name }}</p>
                                <span class="text-xs text-gray-500">{{ optional($plan->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-sky-600 mt-1">{{ $plan->course->title ?? 'خطة عامة' }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($plan->total_amount ?? 0, 2) }} ج.م</span>
                                <a href="{{ route('admin.installments.plans.show', $plan) }}" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                                    تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">لا توجد خطط مميزة بعد.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
            <h2 class="text-lg font-black text-slate-900 mb-4">أحدث الخطط</h2>
            <div class="space-y-4">
                @forelse($recentPlans as $recent)
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900">{{ $recent->name }}</p>
                            <span class="text-xs text-gray-500">{{ optional($recent->created_at)->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-sky-600 mt-1">{{ $recent->course->title ?? 'خطة عامة' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($recent->installments_count) }} دفعة · كل {{ $recent->frequency_interval }} {{ $unitLabels[$recent->frequency_unit] ?? $recent->frequency_unit }}</p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($recent->total_amount ?? 0, 2) }} ج.م</span>
                            <a href="{{ route('admin.installments.plans.show', $recent) }}" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                                عرض سريع <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">لا توجد خطط حديثة.</div>
                @endforelse
            </div>
        </div>
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-900">قائمة خطط التقسيط</h2>
            <p class="text-xs text-slate-500 mt-0.5">كل الخطط المتاحة مع تفاصيل المبالغ، الدورية، وعدد الاتفاقيات المرتبطة.</p>
        </div>
        <div class="p-4 space-y-6">
        @if($plans->count())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="rounded-3xl border border-gray-100 bg-white shadow-lg hover:shadow-xl transition-all p-6 flex flex-col gap-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-gray-900">{{ $plan->name }}</h3>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$plan->is_active] ?? 'bg-gray-100 text-gray-700' }}">
                                        <span class="w-2 h-2 rounded-full {{ $plan->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $plan->is_active ? 'نشطة' : 'معطلة' }}
                                    </span>
                                    @if($plan->auto_generate_on_enrollment)
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-600">
                                            <i class="fas fa-robot"></i>
                                            توليد تلقائي
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-sky-600 mt-2">{{ $plan->course->title ?? 'خطة عامة' }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">إجمالي المبلغ</p>
                                <p class="mt-1 text-base font-black text-gray-900">{{ number_format($plan->total_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">دفعة مقدمة</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ number_format($plan->deposit_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">عدد الأقساط</p>
                                <p class="mt-1 font-semibold text-gray-900">{{ $plan->installments_count }} دفعة</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">الدورية</p>
                                <p class="mt-1 font-semibold text-gray-900">كل {{ $plan->frequency_interval }} {{ $unitLabels[$plan->frequency_unit] ?? $plan->frequency_unit }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>أضيفت {{ optional($plan->created_at)->diffForHumans() }}</span>
                            <span>اتفاقيات مرتبطة: {{ number_format($plan->agreements_count ?? 0) }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.installments.plans.show', $plan) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-sky-100 text-sky-600 font-semibold hover:bg-sky-200 transition-all">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <a href="{{ route('admin.installments.plans.edit', $plan) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.installments.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-rose-600 hover:bg-rose-50 transition-all" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                {{ $plans->withQueryString()->links() }}
            </div>
        @else
            <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p class="font-semibold">لا توجد خطط تقسيط بعد</p>
                <p class="text-sm text-gray-400 mt-2">ابدأ بإنشاء أول خطة لتفعيل نظام الأقساط.</p>
            </div>
        @endif
        </div>
    </section>
</div>
@endsection
