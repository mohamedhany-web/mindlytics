@extends('layouts.admin')

@section('title', 'مركز المحاسبة')
@section('header', 'مركز المحاسبة')

@section('content')
<div class="w-full space-y-8">
    <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-sky-900 text-white shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 border-b border-white/10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">مركز المحاسبة والمالية</h1>
                    <p class="mt-2 text-sm text-white/70 max-w-2xl">
                        نقطة دخول واحدة لكل ما يتعلق بالأموال في الأكاديمية: السجلات، التقارير، الحجوزات، التقسيط، المدربين، والتصدير.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(Route::has('admin.accounting.installments'))
                    <a href="{{ route('admin.accounting.installments') }}" class="inline-flex items-center gap-2 rounded-2xl bg-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-violet-400">
                        <i class="fas fa-percentage"></i>
                        لوحة التقسيط
                    </a>
                    @endif
                    <a href="{{ route('admin.accounting.chart') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/25">
                        <i class="fas fa-sitemap"></i>
                        شجرة الحسابات
                    </a>
                    <a href="{{ route('admin.accounting.reports') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-emerald-400">
                        <i class="fas fa-chart-line"></i>
                        التقارير والـ Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/10">
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">إيرادات مدفوعات الشهر</p>
                <p class="mt-2 text-xl font-black text-emerald-300">{{ number_format($snapshot['revenue_month'], 2) }} <span class="text-sm font-semibold text-white/50">ج.م</span></p>
                <p class="text-[11px] text-white/45 mt-1">{{ $snapshot['month_label'] }}</p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">فواتير معلقة</p>
                <p class="mt-2 text-xl font-black text-amber-200">{{ $snapshot['pending_invoices'] }}</p>
                <p class="text-[11px] text-white/45 mt-1">{{ number_format($snapshot['pending_invoices_amount'], 2) }} ج.م</p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">طلبات معلقة</p>
                <p class="mt-2 text-xl font-black text-sky-200">{{ $snapshot['pending_orders'] }}</p>
                <p class="text-[11px] text-white/45 mt-1">طلبات شراء</p>
            </div>
            <div class="bg-slate-900/80 p-5">
                <p class="text-xs font-semibold text-white/60">سحوبات معلقة</p>
                <p class="mt-2 text-xl font-black text-rose-200">{{ $snapshot['pending_withdrawals'] }}</p>
                <p class="text-[11px] text-white/45 mt-1">{{ number_format($snapshot['pending_withdrawals_amount'], 2) }} ج.م</p>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">اشتراكات نشطة</p>
                <p class="text-lg font-bold text-white">{{ $snapshot['active_subscriptions'] }}</p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">اتفاقيات تقسيط نشطة</p>
                <p class="text-lg font-bold text-white">{{ $snapshot['installment_agreements_active'] }}</p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">أقساط مستحقة (مجموع)</p>
                <p class="text-lg font-bold text-violet-200">{{ number_format($snapshot['installment_pending_total'], 2) }}</p>
            </div>
            <div class="bg-slate-900/60 p-4">
                <p class="text-[11px] text-white/55">مدفوعات تسجيل أوفلاين (الشهر)</p>
                <p class="text-lg font-bold text-teal-200">{{ number_format($snapshot['offline_paid_month'], 2) }}</p>
            </div>
        </div>
    </section>

    @foreach($sections as $section)
        <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                <h2 class="text-lg font-bold text-slate-900">{{ $section['title'] }}</h2>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($section['items'] as $item)
                        @continue(! Route::has($item['route']))
                        <a href="{{ route($item['route']) }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/40 transition-colors">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-sky-100 group-hover:text-sky-700">
                                <i class="fas {{ $item['icon'] }}"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block font-semibold text-slate-900 group-hover:text-sky-800">{{ $item['label'] }}</span>
                                @if(!empty($item['hint']))
                                    <span class="mt-1 block text-xs text-slate-500 leading-snug">{{ $item['hint'] }}</span>
                                @endif
                            </span>
                            <i class="fas fa-chevron-left text-slate-300 group-hover:text-sky-500 mr-auto mt-1 text-sm"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach
</div>
@endsection
