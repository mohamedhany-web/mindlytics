@extends('layouts.admin')

@section('title', 'لوحة توزيع الاهتمام')
@section('header', 'المبيعات — توزيع الـ Leads')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">لوحة توزيع حسب الاهتمام</h2>
                <p class="text-xs text-slate-600">وزّع العملاء على المتخصصين المناسبين — يظهر من سجّل العميل ومن المعيَّن حالياً.</p>
            </div>
            <form method="get" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="text-[11px] font-semibold text-slate-600">الفلتر</label>
                    <select name="filter" class="rounded-xl border px-3 py-2 text-sm">
                        <option value="all" @selected($filter==='all')>الكل</option>
                        <option value="unassigned" @selected($filter==='unassigned')>غير معيّن</option>
                        <option value="mismatch" @selected($filter==='mismatch')>معيّن لغير متخصص</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-slate-600">الاهتمام</label>
                    <select name="interest_type_id" class="rounded-xl border px-3 py-2 text-sm">
                        <option value="">الكل</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" @selected($interestTypeId==$type->id)>{{ $type->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="rounded-xl bg-emerald-600 text-white px-4 py-2 text-sm font-semibold">تطبيق</button>
            </form>
        </div>
    </section>

    @foreach($types as $type)
        @php $typeLeads = $leads->where('interest_type_id', $type->id); @endphp
        @if($typeLeads->isEmpty() && $interestTypeId && $interestTypeId !== $type->id)
            @continue
        @endif
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color: {{ $type->color }}33; background: {{ $type->color }}12">
                <h3 class="font-black text-slate-900 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background:{{ $type->color }}"></span>
                    {{ $type->name_ar }}
                    <span class="text-xs font-semibold text-slate-500">({{ $typeLeads->count() }})</span>
                </h3>
                <p class="text-[11px] text-slate-600">متخصصون: {{ ($specialistsByType[$type->id] ?? collect())->pluck('name')->implode('، ') ?: '—' }}</p>
            </div>
            <div class="divide-y">
                @forelse($typeLeads as $lead)
                    <div class="p-4 flex flex-col lg:flex-row lg:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.sales.leads.show', $lead) }}" class="font-bold text-emerald-700 hover:underline">{{ $lead->name }}</a>
                            <p class="text-xs text-slate-600 mt-0.5">
                                سجّله: <strong>{{ $lead->creator?->name ?? '—' }}</strong>
                                · معيّن: <strong>{{ $lead->assignee?->name ?? 'غير معيّن' }}</strong>
                                · {{ $lead->created_at?->diffForHumans() }}
                            </p>
                        </div>
                        <form method="post" action="{{ route('admin.sales.distribution.assign', $lead) }}" class="flex flex-wrap gap-2 items-center">
                            @csrf
                            <select name="to_user_id" required class="rounded-xl border px-3 py-2 text-sm min-w-[160px]">
                                <option value="">— عيّن لـ —</option>
                                @foreach(($specialistsByType[$type->id] ?? collect()) as $rep)
                                    <option value="{{ $rep->id }}" @selected((int)$lead->assigned_to === (int)$rep->id)>{{ $rep->name }} ★</option>
                                @endforeach
                                @foreach($salesReps as $rep)
                                    @if(! ($specialistsByType[$type->id] ?? collect())->contains('id', $rep->id))
                                        <option value="{{ $rep->id }}" @selected((int)$lead->assigned_to === (int)$rep->id)>{{ $rep->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-xs font-semibold">تحويل</button>
                        </form>
                    </div>
                @empty
                    <p class="p-4 text-sm text-slate-500">لا عملاء في هذا الاهتمام حسب الفلتر.</p>
                @endforelse
            </div>
        </section>
    @endforeach

    @php $untagged = $leads->whereNull('interest_type_id'); @endphp
    @if($untagged->isNotEmpty())
        <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 bg-amber-50 border-b border-amber-200 font-black text-amber-950">بدون نوع اهتمام ({{ $untagged->count() }})</div>
            <div class="divide-y">
                @foreach($untagged as $lead)
                    <div class="p-4 flex flex-col lg:flex-row lg:items-center gap-3">
                        <div class="flex-1">
                            <a href="{{ route('admin.sales.leads.show', $lead) }}" class="font-bold text-emerald-700">{{ $lead->name }}</a>
                            <p class="text-xs text-slate-600">سجّله: {{ $lead->creator?->name ?? '—' }} · معيّن: {{ $lead->assignee?->name ?? '—' }}</p>
                        </div>
                        <form method="post" action="{{ route('admin.sales.distribution.assign', $lead) }}" class="flex gap-2">
                            @csrf
                            <select name="to_user_id" required class="rounded-xl border px-3 py-2 text-sm">
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-xs font-semibold">تحويل</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
