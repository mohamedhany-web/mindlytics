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
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">{{ $plan->name }}</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $plan->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        <span class="w-2 h-2 rounded-full {{ $plan->is_active ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ $plan->is_active ? 'خطة نشطة' : 'خطة معطلة' }}
                    </span>
                    @if($plan->auto_generate_on_enrollment)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                            <i class="fas fa-robot"></i>
                            توليد تلقائي عند التسجيل
                        </span>
                    @endif
                </div>
                <p class="mt-4 text-white/80 max-w-2xl leading-relaxed">
                    {{ $plan->description ?: 'لا توجد ملاحظات إضافية لهذه الخطة.' }}
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-book text-xs"></i>
                        {{ $plan->course->title ?? 'خطة عامة غير مرتبطة بكورس محدد' }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-calendar-alt text-xs"></i>
                        كل {{ $plan->frequency_interval }} {{ $frequencyLabel }} · {{ $plan->installments_count }} دفعة
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-clock text-xs"></i>
                        فترة سماح {{ $plan->grace_period_days }} يوم
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.plans.edit', $plan) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل الخطة
                </a>
                <a href="{{ route('admin.installments.plans.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-500">إجمالي المبلغ</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($plan->total_amount ?? 0, 2) }} ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                    <i class="fas fa-coins text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">القيمة الكاملة للخطة قبل الدفعات المقدمة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-500">الدفعة المقدمة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($plan->deposit_amount ?? 0, 2) }} ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-hand-holding-usd text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">المبلغ المطلوب دفعه مقدماً قبل بدء التقسيط.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-500">عدد الاتفاقيات</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreementsCount) }}</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-users text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">{{ number_format($activeAgreements) }} اتفاقيات نشطة مرتبطة بالخطة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-500">متوسط الأقساط</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($averageInstallments, 1) }}</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-line text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">متوسط عدد الدفعات للاتفاقيات المرتبطة.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
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

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
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
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
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

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
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
