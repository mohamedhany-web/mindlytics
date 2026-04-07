@extends('layouts.admin')

@section('title', 'شجرة الحسابات')
@section('header', 'شجرة الحسابات')

@section('content')
@php
    $roots = $chart['roots'] ?? [];
    $currency = $chart['currency'] ?? 'EGP';
@endphp
<div class="w-full max-w-7xl mx-auto space-y-8 pb-12">
    <section class="rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-sky-950 text-white shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-indigo-200/90 mb-2">المحاسبة — الهيكل المرجعي</p>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">شجرة الحسابات</h1>
                    <p class="mt-3 text-sm text-white/75 leading-relaxed max-w-3xl">
                        خريطة تفاعلية تربط مفاهيم الأصول والخصوم وحقوق الملكية والإيرادات والمصروفات بصفحات النظام الفعلية (فواتير، مدفوعات، تقسيط، حجوزات، مدربين).
                        العملة المرجعية: <strong class="text-white">{{ $currency }}</strong>.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('admin.accounting.hub') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2.5 text-sm font-semibold border border-white/20 hover:bg-white/20">
                        <i class="fas fa-th-large"></i>
                        مركز المحاسبة
                    </a>
                    @if(Route::has('admin.accounting.installments'))
                    <a href="{{ route('admin.accounting.installments') }}" class="inline-flex items-center gap-2 rounded-2xl bg-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-violet-400">
                        <i class="fas fa-percentage"></i>
                        لوحة التقسيط
                    </a>
                    @endif
                    <a href="{{ route('admin.accounting.reports') }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-emerald-400">
                        <i class="fas fa-file-excel"></i>
                        تقارير وتصدير
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-white/10 border-t border-white/10">
            <div class="bg-slate-900/70 px-4 py-3 text-center">
                <p class="text-[10px] text-white/55 font-semibold">أصول</p>
                <p class="text-lg font-black text-amber-200">1 — 11 — 12</p>
            </div>
            <div class="bg-slate-900/70 px-4 py-3 text-center">
                <p class="text-[10px] text-white/55 font-semibold">خصوم</p>
                <p class="text-lg font-black text-rose-200">2 — 21</p>
            </div>
            <div class="bg-slate-900/70 px-4 py-3 text-center">
                <p class="text-[10px] text-white/55 font-semibold">إيرادات</p>
                <p class="text-lg font-black text-emerald-200">4 — 41 — 42</p>
            </div>
            <div class="bg-slate-900/70 px-4 py-3 text-center">
                <p class="text-[10px] text-white/55 font-semibold">مصروفات</p>
                <p class="text-lg font-black text-orange-200">5 — 51</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-l from-slate-50 to-white flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900">الهيكل التفصيلي</h2>
                <p class="text-xs text-slate-500 mt-0.5">اضغط <i class="fas fa-chevron-down text-[10px]"></i> لطي أو فتح الفروع. الروابط تفتح الصفحة المعنية في لوحة التحكم.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">{{ count($roots) }} جذور رئيسية</span>
        </div>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 bg-gradient-to-b from-slate-50/50 to-white">
            @foreach($roots as $root)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex flex-wrap items-center gap-3 px-4 py-3.5 border-b border-slate-100 bg-gradient-to-l from-indigo-50/60 to-white">
                        <span class="font-mono text-sm font-black text-indigo-700 bg-indigo-100 px-2.5 py-1 rounded-lg">{{ $root['code'] ?? '' }}</span>
                        <span class="text-base font-black text-slate-900">{{ $root['name'] ?? '' }}</span>
                    </div>
                    @if(!empty($root['description']))
                        <p class="px-4 py-2.5 text-xs text-slate-600 border-b border-slate-50 bg-slate-50/30">{{ $root['description'] }}</p>
                    @endif
                    <div class="px-3 py-4 sm:px-5">
                        @include('admin.accounting.partials.chart-node', ['nodes' => $root['children'] ?? [], 'depth' => 0])
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <p class="text-center text-xs text-slate-500 px-4">
        الشجرة للعرض والتوافق الداخلي؛ لا تُحدّث قيود اليومية تلقائياً. للأرقام التفصيلية استخدم
        <a href="{{ route('admin.accounting.reports') }}" class="font-semibold text-sky-600 hover:underline">التقارير المحاسبية</a>
        والتصدير إلى Excel.
    </p>
</div>
@endsection
