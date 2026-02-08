@extends('layouts.admin')

@section('title', 'اتفاقيات التقسيط')
@section('header', 'اتفاقيات التقسيط')

@section('content')
@php
    $agreements = $agreements ?? collect();
    $total = $agreements->total() ?? $agreements->count();
    $activeCount = ($agreements instanceof \Illuminate\Pagination\LengthAwarePaginator)
        ? $agreements->where('status', \App\Models\InstallmentAgreement::STATUS_ACTIVE)->count()
        : $agreements->where('status', \App\Models\InstallmentAgreement::STATUS_ACTIVE)->count();
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">إدارة اتفاقيات التقسيط</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-file-signature text-xs"></i>
                        إجمالي {{ number_format($total) }} اتفاقية
                    </span>
                </div>
                <p class="mt-3 text-white/75 max-w-2xl">
                    راقب اتفاقيات التقسيط للطلاب، حالات السداد، والمبالغ المتبقية لتضمن متابعة دقيقة لكل خطة.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.installments.plans.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                    <i class="fas fa-layer-group"></i>
                    إدارة الخطط
                </a>
                <a href="{{ route('admin.installments.agreements.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    إنشاء اتفاقية جديدة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-500">اتفاقيات نشطة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreements->where('status', \App\Models\InstallmentAgreement::STATUS_ACTIVE)->count()) }}</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                    <i class="fas fa-bolt text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">الانتقالات الحالية التي تتطلب متابعة دورية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-500">إجمالي المبالغ الممولة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreements->sum('total_amount'), 2) }} ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-coins text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">القيمة الإجمالية التي تغطيها جميع الاتفاقيات.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-500">دفعات مقدمة</p>
                    <p class="mt-2 text-3xl.font-black text-gray-900">{{ number_format($agreements->sum('deposit_amount'), 2) }} ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-hand-holding-usd text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">إجمالي المبالغ المحصلة كدفعات مقدمة.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-500">اتفاقيات متأخرة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($agreements->where('status', \App\Models\InstallmentAgreement::STATUS_OVERDUE)->count()) }}</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">الاتفاقيات التي تحتاج تدخلاً بسبب تأخر السداد.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900">جميع الاتفاقيات</h2>
                <p class="text-sm text-gray-500">تتبع حالة الطلاب، المبالغ المتبقية، وجداول السداد.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <select name="status" class="px-4 py-2 rounded-2xl border border-gray-200 bg-gray-50 text-sm text-gray-700">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                    <i class="fas fa-filter"></i>
                    تصفية
                </button>
            </form>
        </div>

        @if($agreements->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($agreements as $agreement)
                    <div class="rounded-3xl border border-gray-100 bg-gray-50/70 p-6 flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $agreement->student->name ?? 'طالب غير معروف' }}</p>
                                <p class="text-xs text-sky-600 mt-1">{{ $agreement->course->title ?? 'خطة عامة' }}</p>
                                <p class="text-[11px] text-gray-500 mt-1">بداية {{ optional($agreement->start_date)->format('Y-m-d') }}</p>
                            </div>
                            <span class="inline-flex items-center_gap-2 px-3 py-1 rounded-full text-xs font-semibold
                                @class([
                                    'bg-emerald-100 text-emerald-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_ACTIVE,
                                    'bg-amber-100 text-amber-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_OVERDUE,
                                    'bg-purple-100 text-purple-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_COMPLETED,
                                    'bg-rose-100 text-rose-700' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_CANCELLED,
                                    'bg-gray-100 text-gray-700' => ! in_array($agreement->status, [\App\Models\InstallmentAgreement::STATUS_ACTIVE, \App\Models\InstallmentAgreement::STATUS_OVERDUE, \App\Models\InstallmentAgreement::STATUS_COMPLETED, \App\Models\InstallmentAgreement::STATUS_CANCELLED])
                                ])">
                                <span class="w-2 h-2 rounded-full bg-current/60"></span>
                                {{ $statuses[$agreement->status] ?? $agreement->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">إجمالي الاتفاقية</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ number_format($agreement->total_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">دفعة مقدمة</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ number_format($agreement->deposit_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">عدد الأقساط</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ $agreement->installments_count }} دفعة</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">القسط التالي</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">
                                    {{ optional($agreement->payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date')->first())->due_date?->format('Y-m-d') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        @if($agreement->notes)
                            <p class="text-xs text-gray-500 leading-relaxed">{{ $agreement->notes }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.installments.agreements.show', $agreement) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-sky-100 text-sky-600 font-semibold hover:bg-sky-200 transition-all">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <a href="{{ route('admin.installments.agreements.edit', $agreement) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($agreements instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div>
                    {{ $agreements->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="text-center text-gray-500 py-12">
                <i class="fas fa-folder-open text-4xl mb-3"></i>
                <p class="font-semibold">لا توجد أي اتفاقيات تقسيط بعد.</p>
                <p class="text-sm text-gray-400 mt-2">ابدأ بإضافة أول اتفاقية لربط الطلاب بخطط التقسيط المتاحة.</p>
            </div>
        @endif
    </div>
</div>
@endsection
