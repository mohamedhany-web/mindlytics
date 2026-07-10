@extends('layouts.employee')

@section('title', 'تحويل Leads')
@section('header', 'تحويل Leads بين الفريق')

@section('content')
@php
    $stageLabels = \App\Models\SalesLead::STAGES;
@endphp
<div class="space-y-5 max-w-5xl">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex items-start gap-2">
            <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="rounded-2xl border border-amber-200 bg-gradient-to-l from-amber-50 via-white to-orange-50/40 px-5 py-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-exchange-alt text-sm"></i>
                    </span>
                    تحويل Leads الجماعي
                </h1>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed max-w-xl">
                    انقل كل العملاء المسندين من موظف إلى آخر داخل فريق
                    <strong>{{ $team->name }}</strong>
                    — مفيد عند الإجازة أو إعادة توزيع الحمل.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.sales-manager.leads.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-users"></i> عملاء الفريق
                </a>
                <a href="{{ route('employee.sales-manager.follow-ups.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-clipboard-list"></i> رقابة المتابعات
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {{-- Form --}}
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80">
                <h2 class="text-sm font-bold text-slate-900">خطوات التحويل</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">اختر المصدر والمستلم ثم أكّد العملية</p>
            </div>

            <form method="POST" action="{{ route('employee.sales-manager.transfer.store') }}" class="p-5 space-y-5"
                  onsubmit="return confirm('تأكيد تحويل كل العملاء من الموظف المصدر إلى المستلم؟ لا يمكن التراجع بسهولة.');">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-900 text-white text-[10px] ml-1">1</span>
                            من موظف (المصدر)
                        </label>
                        <select name="from_user_id" required
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400"
                                onchange="window.location='{{ route('employee.sales-manager.transfer.index') }}?from_user_id='+this.value+(document.querySelector('[name=to_user_id]').value ? '&to_user_id='+document.querySelector('[name=to_user_id]').value : '')">
                            <option value="">— اختر الموظف —</option>
                            @foreach($members as $m)
                                <option value="{{ $m->user_id }}" @selected((int) $fromId === (int) $m->user_id)>
                                    {{ $m->user->name }} ({{ (int) ($memberLeadCounts[$m->user_id] ?? 0) }} عميل)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-900 text-white text-[10px] ml-1">2</span>
                            إلى موظف (المستلم)
                        </label>
                        <select name="to_user_id" required
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            <option value="">— اختر الموظف —</option>
                            @foreach($members as $m)
                                @if((int) $m->user_id !== (int) $fromId)
                                    <option value="{{ $m->user_id }}" @selected((int) $toId === (int) $m->user_id)>
                                        {{ $m->user->name }} ({{ (int) ($memberLeadCounts[$m->user_id] ?? 0) }} عميل)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Visual flow --}}
                @if($fromRep || $toRep)
                    <div class="flex flex-wrap items-center justify-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-4">
                        <div class="min-w-[8rem] text-center rounded-xl bg-white border border-slate-200 px-3 py-3">
                            <p class="text-[10px] text-slate-500 font-semibold mb-1">المصدر</p>
                            <p class="text-sm font-bold text-slate-900">{{ $fromRep->name ?? '—' }}</p>
                            <p class="text-[11px] text-amber-700 font-bold mt-1 tabular-nums">{{ $stats['leads_total'] ?? 0 }} عميل</p>
                        </div>
                        <div class="text-amber-500">
                            <i class="fas fa-long-arrow-alt-left text-xl"></i>
                        </div>
                        <div class="min-w-[8rem] text-center rounded-xl bg-white border border-emerald-200 px-3 py-3">
                            <p class="text-[10px] text-slate-500 font-semibold mb-1">المستلم</p>
                            <p class="text-sm font-bold text-slate-900">{{ $toRep->name ?? 'اختر مستلماً' }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">سيستلم الكل</p>
                        </div>
                    </div>
                @endif

                <div class="rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-xs text-amber-950 leading-relaxed">
                    <p class="font-bold mb-1 flex items-center gap-1.5"><i class="fas fa-exclamation-triangle text-amber-600"></i> تنبيه مهم</p>
                    <ul class="list-disc pe-4 space-y-1 text-amber-900/90">
                        <li>سيتم نقل <strong>كل</strong> العملاء المسندين للموظف المصدر دفعة واحدة.</li>
                        <li>المحادثات والواتساب المرتبطة تبقى حسب إعدادات الإسناد الحالية.</li>
                        <li>راجع الإحصائيات على اليسار قبل التنفيذ.</li>
                    </ul>
                </div>

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer hover:border-slate-300">
                    <input type="checkbox" name="confirm" value="1" required
                           class="mt-0.5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm text-slate-700 leading-relaxed">
                        أؤكد تحويل جميع العملاء من الموظف المصدر إلى المستلم، وأتحمل مسؤولية إعادة التوزيع.
                    </span>
                </label>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-sm disabled:opacity-50"
                            @disabled(! $fromId)>
                        <i class="fas fa-exchange-alt"></i>
                        تنفيذ التحويل
                    </button>
                    <a href="{{ route('employee.sales-manager.transfer.index') }}"
                       class="text-xs font-semibold text-slate-500 hover:text-slate-800">إعادة تعيين</a>
                </div>
            </form>
        </div>

        {{-- Side panel --}}
        <div class="lg:col-span-2 space-y-4">
            @if($stats && $fromRep)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/80">
                        <h3 class="text-sm font-bold text-slate-900">إحصائيات {{ $fromRep->name }}</h3>
                    </div>
                    <div class="p-4 grid grid-cols-3 gap-2">
                        <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-3 text-center">
                            <p class="text-[10px] text-slate-500 font-semibold">الإجمالي</p>
                            <p class="text-xl font-black text-slate-900 tabular-nums">{{ $stats['leads_total'] }}</p>
                        </div>
                        <div class="rounded-xl bg-teal-50 border border-teal-100 px-3 py-3 text-center">
                            <p class="text-[10px] text-teal-700 font-semibold">مفتوحة</p>
                            <p class="text-xl font-black text-teal-800 tabular-nums">{{ $stats['leads_open'] }}</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 border border-rose-100 px-3 py-3 text-center">
                            <p class="text-[10px] text-rose-700 font-semibold">متأخرة</p>
                            <p class="text-xl font-black text-rose-800 tabular-nums">{{ $stats['overdue'] }}</p>
                        </div>
                    </div>
                    @if(! empty($stats['leads_by_stage']))
                        <div class="px-4 pb-4">
                            <p class="text-[11px] font-bold text-slate-600 mb-2">حسب المرحلة</p>
                            <div class="space-y-1.5">
                                @foreach($stats['leads_by_stage'] as $stage => $count)
                                    <div class="flex items-center justify-between text-xs rounded-lg bg-slate-50 px-3 py-2">
                                        <span class="text-slate-700">{{ $stageLabels[$stage] ?? $stage }}</span>
                                        <span class="font-bold tabular-nums text-slate-900">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 px-5 py-10 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="fas fa-user-friends text-lg"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-800">اختر موظفاً مصدراً</p>
                    <p class="text-xs text-slate-500 mt-1">ستظهر هنا أعداد العملاء ومراحلهم قبل التحويل</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">أعضاء الفريق</h3>
                </div>
                <ul class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                    @forelse($members as $m)
                        <li>
                            <a href="{{ route('employee.sales-manager.transfer.index', ['from_user_id' => $m->user_id]) }}"
                               class="flex items-center justify-between gap-2 px-4 py-3 hover:bg-slate-50 transition-colors
                               {{ (int) $fromId === (int) $m->user_id ? 'bg-amber-50/80' : '' }}">
                                <span class="text-sm font-semibold text-slate-800">{{ $m->user->name }}</span>
                                <span class="text-[11px] font-bold tabular-nums px-2 py-0.5 rounded-full
                                    {{ (int) ($memberLeadCounts[$m->user_id] ?? 0) > 0 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ (int) ($memberLeadCounts[$m->user_id] ?? 0) }}
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-sm text-slate-500">لا يوجد أعضاء في الفريق</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
