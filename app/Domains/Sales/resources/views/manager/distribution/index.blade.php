@extends('layouts.employee')

@section('title', 'توزيع الاهتمام')
@section('header', 'توزيع Leads الفريق حسب الاهتمام')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900">لوحة التوزيع — {{ $team->name }}</h2>
                <p class="text-xs text-slate-600">حوّل العملاء لأعضاء الفريق المتخصصين في نفس الاهتمام.</p>
            </div>
            <form method="get" class="flex flex-wrap gap-2">
                <select name="filter" class="rounded-xl border px-3 py-2 text-sm">
                    <option value="all" @selected($filter==='all')>الكل</option>
                    <option value="mismatch" @selected($filter==='mismatch')>غير مطابق للتخصص</option>
                </select>
                <select name="interest_type_id" class="rounded-xl border px-3 py-2 text-sm">
                    <option value="">كل الاهتمامات</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" @selected($interestTypeId==$type->id)>{{ $type->name_ar }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-teal-600 text-white px-4 py-2 text-sm font-semibold">تطبيق</button>
            </form>
        </div>
    </section>

    @foreach($types as $type)
        @php $typeLeads = $leads->where('interest_type_id', $type->id); @endphp
        <section class="rounded-2xl bg-white border shadow overflow-hidden">
            <div class="px-4 py-3 border-b font-black flex justify-between" style="background:{{ $type->color }}12">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full" style="background:{{ $type->color }}"></span>{{ $type->name_ar }} ({{ $typeLeads->count() }})</span>
                <span class="text-[11px] font-semibold text-slate-600">{{ ($specialistsByType[$type->id] ?? collect())->pluck('name')->implode('، ') ?: 'لا متخصصين' }}</span>
            </div>
            <div class="divide-y">
                @forelse($typeLeads as $lead)
                    <div class="p-4 flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex-1">
                            <a href="{{ route('employee.sales-manager.leads.show', $lead) }}" class="font-bold text-teal-700">{{ $lead->name }}</a>
                            <p class="text-xs text-slate-600">سجّله: {{ $lead->creator?->name ?? '—' }} · معيّن: {{ $lead->assignee?->name ?? '—' }}</p>
                        </div>
                        <form method="post" action="{{ route('employee.sales-manager.distribution.assign', $lead) }}" class="flex gap-2">
                            @csrf
                            <select name="to_user_id" required class="rounded-xl border px-3 py-2 text-sm">
                                @foreach(($specialistsByType[$type->id] ?? collect())->isNotEmpty() ? $specialistsByType[$type->id] : $salesReps as $rep)
                                    <option value="{{ $rep->id }}" @selected((int)$lead->assigned_to===(int)$rep->id)>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-xs font-semibold">تحويل</button>
                        </form>
                    </div>
                @empty
                    <p class="p-4 text-sm text-slate-500">لا عملاء.</p>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
@endsection
