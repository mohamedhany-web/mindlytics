@extends('layouts.admin')

@section('title', 'اتفاقيات التقسيط')
@section('header', 'اتفاقيات التقسيط')

@section('content')
@php
    $summary = $summary ?? [];
    $pageStats = [
        ['label' => 'نشطة', 'value' => number_format($summary['active'] ?? 0), 'desc' => 'اتفاقيات تتطلب متابعة', 'icon' => 'fas fa-bolt', 'theme' => 'sky'],
        ['label' => 'إجمالي الممول', 'value' => number_format($summary['total_amount'] ?? 0, 2), 'desc' => 'ج.م — حسب التصفية', 'icon' => 'fas fa-coins', 'theme' => 'emerald'],
        ['label' => 'دفعات مقدمة', 'value' => number_format($summary['deposit_amount'] ?? 0, 2), 'desc' => 'ج.م — مجموع التصفية', 'icon' => 'fas fa-hand-holding-usd', 'theme' => 'amber'],
        ['label' => 'متأخرة', 'value' => number_format($summary['overdue'] ?? 0), 'desc' => 'مكتملة: ' . number_format($summary['completed'] ?? 0), 'icon' => 'fas fa-exclamation-circle', 'theme' => 'rose'],
    ];
@endphp
<div class="space-y-6">
    @include('admin.installments.partials.header', [
        'title' => 'اتفاقيات تقسيط الكورسات',
        'description' => 'إدارة خطط السداد للكورسات الأونلاين والأوفلاين مع متابعة الأقساط والربط المحاسبي.',
        'icon' => 'fa-handshake',
        'iconGradient' => 'from-emerald-500 to-teal-600',
        'meta' => number_format($summary['total_count'] ?? 0) . ' نتيجة بحسب التصفية',
        'actions' => [
            ['route' => 'admin.installments.agreements.manual-booking', 'label' => 'حجز + تقسيط', 'icon' => 'fa-user-plus', 'style' => 'warning'],
            ['route' => 'admin.installments.agreements.create', 'label' => 'اتفاقية جديدة', 'icon' => 'fa-plus', 'style' => 'success'],
        ],
    ])

    @include('admin.installments.partials.nav', ['active' => 'agreements'])

    @include('admin.installments.partials.stats', ['stats' => $pageStats])

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            <div>
                <h2 class="text-base font-black text-slate-900">قائمة الاتفاقيات</h2>
                <p class="text-xs text-slate-500 mt-0.5">بحث بالطالب، تصفية بالحالة ونوع الكورس.</p>
            </div>
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم الطالب، البريد، الجوال…"
                       class="min-w-[200px] flex-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                <select name="course_type" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 min-w-[200px]">
                    @foreach($courseTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('course_type', '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 min-w-[160px]">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                    <i class="fas fa-search"></i>
                    تطبيق
                </button>
                @if(request()->hasAny(['search', 'status', 'course_type']))
                    <a href="{{ route('admin.installments.agreements.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">إعادة ضبط</a>
                @endif
            </form>
        </div>

        <div class="p-4 space-y-6">
        @if($agreements->count())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @foreach($agreements as $agreement)
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50/80 to-white p-5 flex flex-col gap-4 shadow-sm hover:border-sky-200 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $agreement->student->name ?? 'طالب غير معروف' }}</p>
                                <p class="text-xs text-sky-700 mt-0.5 truncate">{{ $agreement->display_course_title }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">
                                    @if($agreement->student_course_enrollment_id)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-medium">أونلاين</span>
                                    @elseif($agreement->offline_course_enrollment_id)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 font-medium">أوفلاين</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                    · بداية {{ optional($agreement->start_date)->format('Y-m-d') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0
                                @class([
                                    'bg-emerald-100 text-emerald-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_ACTIVE,
                                    'bg-amber-100 text-amber-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_OVERDUE,
                                    'bg-violet-100 text-violet-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_COMPLETED,
                                    'bg-rose-100 text-rose-800' => $agreement->status === \App\Models\InstallmentAgreement::STATUS_CANCELLED,
                                    'bg-slate-100 text-slate-700' => ! in_array($agreement->status, [\App\Models\InstallmentAgreement::STATUS_ACTIVE, \App\Models\InstallmentAgreement::STATUS_OVERDUE, \App\Models\InstallmentAgreement::STATUS_COMPLETED, \App\Models\InstallmentAgreement::STATUS_CANCELLED])
                                ])">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                {{ $statuses[$agreement->status] ?? $agreement->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">إجمالي الاتفاقية</p>
                                <p class="mt-0.5 font-bold text-slate-900">{{ number_format($agreement->total_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">دفعة مقدمة</p>
                                <p class="mt-0.5 font-bold text-slate-900">{{ number_format($agreement->deposit_amount ?? 0, 2) }} ج.م</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">عدد الأقساط</p>
                                <p class="mt-0.5 font-bold text-slate-900">{{ $agreement->installments_count }}</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-semibold text-slate-500 uppercase">القسط التالي</p>
                                <p class="mt-0.5 font-bold text-slate-900">
                                    @php
                                        $next = $agreement->payments->where('status', \App\Models\InstallmentPayment::STATUS_PENDING)->sortBy('due_date')->first();
                                    @endphp
                                    {{ $next?->due_date?->format('Y-m-d') ?? '—' }}
                                </p>
                            </div>
                        </div>

                        @if($agreement->notes)
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ $agreement->notes }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <a href="{{ route('admin.installments.agreements.show', $agreement) }}" class="flex-1 min-w-[140px] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                                <i class="fas fa-eye"></i>
                                التفاصيل والجدول
                            </a>
                            <a href="{{ route('admin.installments.agreements.edit', $agreement) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                {{ $agreements->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center text-slate-500 py-16 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50">
                <i class="fas fa-folder-open text-4xl mb-3 text-slate-300"></i>
                <p class="font-bold text-slate-700">لا توجد اتفاقيات ضمن هذه التصفية.</p>
                <p class="text-sm text-slate-500 mt-2">جرّب تغيير البحث أو أضف اتفاقية جديدة.</p>
            </div>
        @endif
        </div>
    </section>
</div>
@endsection
