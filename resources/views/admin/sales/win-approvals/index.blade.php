@extends('layouts.admin')

@section('title', 'موافقة صفقات Win')
@section('header', 'موافقة صفقات الفوز')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">موافقة صفقات Win</h2>
                    <p class="text-xs text-slate-600">عندما يغيّر موظف السيلز حالة العميل إلى «فوز» — يظهر هنا حتى تعتمد الكوميشن.</p>
                </div>
            </div>
            <a href="{{ route('admin.sales.commissions.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                <i class="fas fa-coins text-amber-600"></i> ملخص الكوميشن
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-800">بانتظار الموافقة</p>
                <p class="text-2xl font-black text-amber-900 tabular-nums">{{ $stats['pending'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold text-slate-600">إجمالي القيمة</p>
                <p class="text-2xl font-black text-slate-900 tabular-nums">{{ number_format($stats['pending_value'], 2) }} <span class="text-sm font-bold">ج.م</span></p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-800">كوميشن مقدّر</p>
                <p class="text-2xl font-black text-emerald-900 tabular-nums">{{ number_format($stats['pending_commission_est'], 2) }} <span class="text-sm font-bold">ج.م</span></p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <form method="get" class="flex flex-wrap gap-3 items-end">
                <div class="min-w-[180px] flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم، هاتف، شركة..." class="{{ $inputClass }}">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">موظف السيلز</label>
                    <select name="assigned_to" class="{{ $inputClass }}">
                        <option value="">الكل</option>
                        @foreach($reps as $rep)
                            <option value="{{ $rep->id }}" @selected(request('assigned_to') == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-right p-3 font-bold text-slate-600">العميل</th>
                        <th class="text-right p-3 font-bold text-slate-600">موظف السيلز</th>
                        <th class="text-right p-3 font-bold text-slate-600">القيمة</th>
                        <th class="text-right p-3 font-bold text-slate-600">كوميشن مقدّر</th>
                        <th class="text-right p-3 font-bold text-slate-600">تاريخ الفوز</th>
                        <th class="text-right p-3 font-bold text-slate-600 min-w-[280px]">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leads as $lead)
                        @php
                            $defaultCommission = \App\Services\SalesWinCommissionService::defaultCommissionForLead($lead);
                            $rep = $lead->assignee;
                        @endphp
                        <tr class="hover:bg-slate-50/50 align-top">
                            <td class="p-3">
                                <a href="{{ route('admin.sales.leads.show', $lead) }}" class="font-bold text-sky-700 hover:underline">{{ $lead->name }}</a>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $lead->phone ?? '—' }} · {{ $lead->company ?? '—' }}</p>
                            </td>
                            <td class="p-3">
                                <p class="font-semibold text-slate-900">{{ $rep?->name ?? '—' }}</p>
                                @if($rep)
                                    <p class="text-[10px] text-slate-500">{{ $rep->salesCommissionLabel() }}</p>
                                @endif
                            </td>
                            <td class="p-3 font-bold tabular-nums">{{ number_format((float) ($lead->expected_value ?? 0), 2) }} ج.م</td>
                            <td class="p-3 font-bold text-emerald-700 tabular-nums">{{ number_format($defaultCommission, 2) }} ج.م</td>
                            <td class="p-3 text-xs text-slate-600">{{ $lead->closed_at?->format('Y-m-d H:i') ?? $lead->updated_at?->format('Y-m-d H:i') }}</td>
                            <td class="p-3">
                                <form method="post" action="{{ route('admin.sales.win-approvals.approve', $lead) }}" class="space-y-2 mb-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50/40">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-600">مبلغ الكوميشن</label>
                                            <input type="number" step="0.01" min="0" name="commission_amount" value="{{ number_format($defaultCommission, 2, '.', '') }}" class="{{ $inputClass }} !py-1.5 text-xs">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="text-[10px] font-bold text-slate-600">ملاحظات (اختياري)</label>
                                            <input type="text" name="commission_notes" class="{{ $inputClass }} !py-1.5 text-xs" placeholder="ملاحظة للاعتماد">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">
                                        <i class="fas fa-check"></i> اعتماد وصرف الكوميشن
                                    </button>
                                </form>
                                <details class="text-xs">
                                    <summary class="cursor-pointer text-rose-700 font-semibold">رفض الطلب</summary>
                                    <form method="post" action="{{ route('admin.sales.win-approvals.reject', $lead) }}" class="mt-2 space-y-2 p-2 rounded-lg border border-rose-200 bg-rose-50/50">
                                        @csrf
                                        <textarea name="rejection_reason" rows="2" required class="{{ $inputClass }} !text-xs" placeholder="سبب الرفض للموظف"></textarea>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold" onclick="return confirm('رفض الفوز وإعادة الصفقة لـ «عرض سعر»؟')">رفض</button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-500">
                                <i class="fas fa-inbox text-3xl text-slate-300 mb-3"></i>
                                <p class="font-semibold">لا توجد صفقات Win بانتظار الموافقة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $leads->links() }}</div>
        @endif
    </section>
</div>
@endsection
