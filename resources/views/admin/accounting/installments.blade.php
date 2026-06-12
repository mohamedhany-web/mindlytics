@extends('layouts.admin')

@section('title', 'لوحة التقسيط المحاسبية')
@section('header', 'لوحة التقسيط المحاسبية')

@section('content')
@php
    $primaryStats = [
        ['label' => 'اتفاقيات (الكل)', 'value' => number_format($stats['agreements_total']), 'desc' => 'نشطة: ' . number_format($stats['agreements_active']), 'icon' => 'fas fa-handshake', 'theme' => 'violet'],
        ['label' => 'متأخرة', 'value' => number_format($stats['agreements_overdue']), 'desc' => 'اتفاقيات بحاجة متابعة', 'icon' => 'fas fa-exclamation-triangle', 'theme' => 'amber'],
        ['label' => 'مكتملة', 'value' => number_format($stats['agreements_completed']), 'desc' => 'ملغاة: ' . number_format($stats['agreements_cancelled']), 'icon' => 'fas fa-check-circle', 'theme' => 'emerald'],
        ['label' => 'أونلاين / أوفلاين', 'value' => number_format($stats['online_linked']) . ' / ' . number_format($stats['offline_linked']), 'desc' => 'اتفاقيات مرتبطة بالكورسات', 'icon' => 'fas fa-laptop', 'theme' => 'sky'],
    ];
    $financialStats = [
        ['label' => 'إجمالي الاتفاقيات', 'value' => number_format($stats['total_financed'], 2), 'desc' => 'ج.م — مجموع المبالغ', 'icon' => 'fas fa-coins', 'theme' => 'green'],
        ['label' => 'أقساط معلقة', 'value' => number_format($stats['pending_installments_sum'], 2), 'desc' => number_format($stats['pending_count']) . ' قسط', 'icon' => 'fas fa-clock', 'theme' => 'indigo'],
        ['label' => 'أقساط متأخرة', 'value' => number_format($stats['overdue_installments_sum'], 2), 'desc' => number_format($stats['overdue_payments_count']) . ' قسط', 'icon' => 'fas fa-fire', 'theme' => 'rose'],
        ['label' => 'محصّل الشهر', 'value' => number_format($stats['paid_installments_month'], 2), 'desc' => $stats['month_label'] . ' — ' . number_format($stats['paid_count_month']) . ' عملية', 'icon' => 'fas fa-receipt', 'theme' => 'teal'],
    ];
    $hasAlerts = ($stats['overdue_payments_count'] ?? 0) > 0 || ($stats['agreements_overdue'] ?? 0) > 0;
@endphp

<div class="space-y-6">
    @include('admin.installments.partials.header', [
        'title' => 'لوحة تحكم التقسيط',
        'description' => 'متابعة اتفاقيات الكورسات، الأقساط المستحقة، التحصيل الشهري، والربط المحاسبي.',
        'icon' => 'fa-percentage',
        'iconGradient' => 'from-violet-500 to-purple-600',
        'meta' => $stats['month_label'] . ' · ' . number_format($stats['active_plans']) . ' خطة نشطة',
        'actions' => [
            ['route' => 'admin.accounting.chart', 'label' => 'شجرة الحسابات', 'icon' => 'fa-sitemap'],
            ['route' => 'admin.installments.agreements.manual-booking', 'label' => 'حجز + تقسيط', 'icon' => 'fa-user-plus', 'style' => 'warning'],
            ['route' => 'admin.installments.agreements.index', 'label' => 'الاتفاقيات', 'icon' => 'fa-handshake', 'style' => 'success'],
        ],
    ])

    @include('admin.installments.partials.nav', ['active' => 'dashboard'])

    @if($hasAlerts)
    <section class="rounded-2xl border border-rose-200 bg-rose-50 shadow-lg overflow-hidden">
        <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2 text-rose-900">
                <i class="fas fa-bell"></i>
                <span class="text-sm font-bold">تنبيه تقسيط</span>
            </div>
            <p class="text-xs font-semibold text-rose-800">
                {{ number_format($stats['agreements_overdue']) }} اتفاقية متأخرة ·
                {{ number_format($stats['overdue_payments_count']) }} قسط متأخر
                ({{ number_format($stats['overdue_installments_sum'], 2) }} ج.م)
            </p>
        </div>
    </section>
    @endif

    @include('admin.installments.partials.stats', ['stats' => $primaryStats])
    @include('admin.installments.partials.stats', ['stats' => $financialStats])

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white text-sm">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">أقساط خلال 14 يوماً</h3>
                        <p class="text-[11px] text-slate-500">معلقة أو متأخرة</p>
                    </div>
                </div>
                <a href="{{ route('admin.installments.agreements.index') }}" class="text-xs font-bold text-violet-700 hover:text-violet-900">كل الاتفاقيات ←</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                @forelse($upcomingDues as $p)
                    @php $a = $p->agreement; @endphp
                    <div class="px-4 py-3 flex flex-wrap items-center gap-3 hover:bg-slate-50/80">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $a?->student?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $a?->display_course_title ?? '—' }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-bold text-slate-800 tabular-nums">{{ number_format((float) $p->amount, 2) }} ج.م</p>
                            <p class="text-[11px] text-slate-500">استحقاق {{ $p->due_date?->format('Y-m-d') }} — قسط {{ $p->sequence_number }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-bold
                            {{ $p->status === \App\Models\InstallmentPayment::STATUS_OVERDUE ? 'bg-rose-100 text-rose-800' : 'bg-amber-50 text-amber-800' }}">
                            {{ $p->status === \App\Models\InstallmentPayment::STATUS_OVERDUE ? 'متأخر' : 'مستحق' }}
                        </span>
                        @if($a)
                            <a href="{{ route('admin.installments.agreements.show', $a) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-violet-100 hover:text-violet-700" title="فتح">
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-sm text-slate-500">لا توجد أقساط مستحقة في هذه النافذة.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">آخر عمليات السداد</h3>
                        <p class="text-[11px] text-slate-500">تسجيل «مدفوع» على الأقساط</p>
                    </div>
                </div>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">المدفوعات ←</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                @forelse($recentPayments as $p)
                    @php $a = $p->agreement; @endphp
                    <div class="px-4 py-3 flex flex-wrap items-center gap-3 hover:bg-slate-50/80">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $a?->student?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $a?->display_course_title ?? '—' }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-sm font-bold text-emerald-700 tabular-nums">+{{ number_format((float) $p->amount, 2) }} ج.م</p>
                            <p class="text-[11px] text-slate-500">{{ optional($p->paid_at)->format('Y-m-d H:i') ?? '—' }}</p>
                        </div>
                        @if($p->payment_id)
                            <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 px-2 py-1 rounded-lg">مرتبط بمدفوعة</span>
                        @endif
                        @if($a)
                            <a href="{{ route('admin.installments.agreements.show', $a) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-700" title="فتح الاتفاقية">
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-12 text-center text-sm text-slate-500">لا توجد أقساط مسددة مسجلة بعد.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-route text-violet-600"></i>
                مسار العمل الموصى به
            </h3>
        </div>
        <ol class="list-decimal list-inside space-y-2 text-sm text-slate-600 leading-relaxed p-4 mr-2">
            <li>إنشاء أو مراجعة <strong>خطط التقسيط</strong> (مدة، دفعة مقدمة، تكرار الأقساط).</li>
            <li>ربط الطالب بالكورس عبر <strong>حجز يدوي + تقسيط</strong> أو اتفاقية من تسجيل أونلاين موجود.</li>
            <li>متابعة الجدول من صفحة الاتفاقية وتسجيل السداد — يظهر في <strong>الفواتير / المدفوعات / المعاملات</strong>.</li>
            <li>استخدم <strong>شجرة الحسابات</strong> لفهم موضع التقسيط في هيكل الأصول والإيرادات.</li>
        </ol>
    </section>
</div>
@endsection
