@extends('layouts.admin')

@section('title', 'تفاصيل خطة التقسيط')
@section('header', 'تفاصيل خطة التقسيط')

@section('content')
@php
    $agreements = $plan->agreements ?? collect();
    $agreementsCount = $agreements->count();
    $activeAgreements = $agreements->where('status', 'active')->count();
    $totalFinanced = $agreements->sum('total_amount');
    $totalDeposits = $agreements->sum('deposit_amount');
    $averageInstallments = $agreementsCount > 0 ? round($agreements->avg('installments_count'), 1) : $plan->installments_count;
    $frequencyLabel = $frequencyUnits[$plan->frequency_unit] ?? $plan->frequency_unit;
    $pageStats = [
        ['label' => 'إجمالي المبلغ', 'value' => number_format($plan->total_amount ?? 0, 2), 'desc' => 'ج.م — قيمة الخطة', 'icon' => 'fas fa-coins', 'theme' => 'sky'],
        ['label' => 'الدفعة المقدمة', 'value' => number_format($plan->deposit_amount ?? 0, 2), 'desc' => 'ج.م — عند التعاقد', 'icon' => 'fas fa-hand-holding-usd', 'theme' => 'amber'],
        ['label' => 'عدد الاتفاقيات', 'value' => number_format($agreementsCount), 'desc' => number_format($activeAgreements) . ' نشطة', 'icon' => 'fas fa-users', 'theme' => 'emerald'],
        ['label' => 'متوسط الأقساط', 'value' => number_format($averageInstallments, 1), 'desc' => 'دفعة لكل اتفاقية', 'icon' => 'fas fa-chart-line', 'theme' => 'purple'],
    ];
@endphp
<div class="space-y-6">
    @include('admin.installments.partials.header', [
        'title' => $plan->name,
        'description' => $plan->description ?: 'لا توجد ملاحظات إضافية لهذه الخطة.',
        'icon' => 'fa-layer-group',
        'iconGradient' => 'from-sky-500 to-blue-600',
        'meta' => ($plan->course->title ?? 'خطة عامة') . ' · كل ' . $plan->frequency_interval . ' ' . $frequencyLabel . ' · ' . $plan->installments_count . ' دفعة',
        'actions' => [
            ['route' => 'admin.installments.plans.edit', 'label' => 'تعديل', 'icon' => 'fa-edit', 'style' => 'primary', 'params' => [$plan]],
            ['route' => 'admin.installments.plans.index', 'label' => 'قائمة الخطط', 'icon' => 'fa-list'],
        ],
    ])
    @include('admin.installments.partials.nav', ['active' => 'plans'])

    @include('admin.installments.partials.stats', ['stats' => $pageStats])

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6 space-y-6">
                <h2 class="text-lg font-black text-gray-900">تفاصيل الخطة</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الكورس المرتبط</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ $plan->course->title ?? 'خطة عامة' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">القيمة الممولة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ number_format($totalFinanced, 2) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">إجمالي الدفعات المقدمة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ number_format($totalDeposits, 2) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">ملاحظات إضافية</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ data_get($plan->metadata, 'notes', '—') }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-2xl px-4 py-3 text-xs text-gray-500">
                    عند تفعيل "التوليد التلقائي" سيتم إنشاء خطة أقساط مباشرة للطالب عند تسجيله في الكورس المرتبط.
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">الاتفاقيات المرتبطة</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                        <i class="fas fa-file-signature text-xs"></i>
                        {{ $agreementsCount }} اتفاقية
                    </span>
                </div>
                @if($agreementsCount > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($agreements as $agreement)
                            <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70 space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">{{ $agreement->user->name ?? 'مستخدم غير معروف' }}</p>
                                    <span class="text-xs text-gray-500">{{ optional($agreement->created_at)->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-sky-600">{{ $agreement->course->title ?? 'خطة عامة' }}</p>
                                <div class="grid grid-cols-2 gap-3 text-xs text-gray-600">
                                    <div>
                                        <p class="uppercase text-[11px]">إجمالي الاتفاقية</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($agreement->total_amount ?? 0, 2) }} ج.م</p>
                                    </div>
                                    <div>
                                        <p class="uppercase text-[11px]">دفعة مقدمة</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($agreement->deposit_amount ?? 0, 2) }} ج.م</p>
                                    </div>
                                    <div>
                                        <p class="uppercase text-[11px]">عدد الأقساط</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $agreement->installments_count }} دفعة</p>
                                    </div>
                                    <div>
                                        <p class="uppercase text-[11px]">الحالة</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $agreement->status }}</p>
                                    </div>
                                </div>
                                @if($agreement->notes)
                                    <p class="text-xs text-gray-500 leading-relaxed">{{ $agreement->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-500 text-center py-10">
                        <i class="fas fa-folder-open text-3xl mb-3"></i>
                        لا توجد اتفاقيات مرتبطة بهذه الخطة حتى الآن.
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">مخطط الدفعات</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-hand-holding-usd mt-1 text-sky-500"></i>
                        الدفعة المقدمة: {{ number_format($plan->deposit_amount ?? 0, 2) }} ج.م تُسدد عند التعاقد.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-calendar-alt mt-1 text-sky-500"></i>
                        الأقساط: {{ $plan->installments_count }} دفعة، كل {{ $plan->frequency_interval }} {{ $frequencyLabel }}.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-clock mt-1 text-sky-500"></i>
                        فترة سماح قبل إاعتبار القسط متأخراً: {{ $plan->grace_period_days }} يوم.
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">إجراءات سريعة</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.installments.plans.edit', $plan) }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-sky-100 bg-sky-50/70 text-sky-600 hover:border-sky-200 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-100 text-sky-600">
                                <i class="fas fa-pen"></i>
                            </span>
                            <span class="text-sm font-semibold">تعديل بيانات الخطة</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <a href="{{ route('admin.installments.plans.index') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-gray-100 bg-gray-50/70 text-gray-600 hover:border-gray-200 transition-all">
                        <div class="flex items-center_gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600">
                                <i class="fas fa-list"></i>
                            </span>
                            <span class="text-sm font-semibold">العودة إلى قائمة الخطط</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
