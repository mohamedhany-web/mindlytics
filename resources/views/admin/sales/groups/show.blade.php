@extends('layouts.admin')

@section('title', $group->name)
@section('header', 'مجموعة: '.$group->name)

@section('content')
@php
    $selectedMemberIds = collect(old('member_ids', $group->members->pluck('id')->all() ?: [$group->assigned_to]));
@endphp
<div class="space-y-4">
    @if(session('success'))<div class="text-sm text-emerald-700">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="text-sm text-rose-700">{{ session('error') }}</div>@endif

    <div class="bg-white border border-emerald-200/60 rounded-xl p-4 flex flex-wrap items-center justify-between gap-3 shadow-sm">
        <div>
            <p class="font-bold text-slate-900 text-sm"><i class="fab fa-whatsapp ml-1 text-emerald-600"></i> مجموعة واتساب (Meta Cloud)</p>
            <p class="text-xs text-slate-500 mt-1">أنشئ مجموعة وأرسل دعوات لعملاء هذه المجموعة.</p>
        </div>
        <a href="{{ route('admin.sales.whatsapp-groups.create', ['crm_group' => $group->id]) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold">إنشاء مجموعة واتساب</a>
    </div>

    @include('admin.sales.groups._whatsapp_bulk', [
        'group' => $group,
        'leadsWithPhone' => $leadsWithPhone ?? collect(),
        'formAction' => route('admin.sales.groups.whatsapp.store', $group),
        'latestBatch' => $latestBatch ?? null,
        'latestBatchUrl' => isset($latestBatch) ? route('admin.whatsapp.batches.show', $latestBatch) : null,
    ])

    @if($group->members->isNotEmpty() || $group->assigned_to)
        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 flex flex-wrap items-center gap-2">
            <span class="text-sm font-bold text-sky-900"><i class="fas fa-chart-pie ml-1"></i> تقارير أداء الموظفين في هذه المجموعة:</span>
            @foreach(($group->members->isNotEmpty() ? $group->members : collect([$group->assignee])) as $member)
                @if($member)
                    <a href="{{ route('admin.sales.reports.employee', ['user_id' => $member->id, 'group_id' => $group->id, 'lead_scope' => 'in_groups']) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-sky-300 text-sm font-semibold text-sky-800 hover:bg-sky-100">
                        {{ $member->name }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('admin.sales.groups.update', $group) }}" class="bg-white border rounded-xl p-5 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">الاسم</label>
            <input type="text" name="name" value="{{ old('name', $group->name) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">موظفو المبيعات في المجموعة</label>
            <div class="max-h-48 overflow-y-auto border rounded-lg p-3 space-y-2">
                @foreach($reps as $rep)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="member_ids[]" value="{{ $rep->id }}" class="rounded"
                            @checked($selectedMemberIds->contains($rep->id))>
                        <span>{{ $rep->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('member_ids')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-slate-500 mt-1">كل موظف يرى عملاءه المسندين إليه داخل هذه المجموعة فقط.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <input type="text" name="description" value="{{ old('description', $group->description) }}" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">اختر العملاء (من محافظ الموظفين المحددين)</label>
            <div class="max-h-80 overflow-y-auto border rounded-lg p-3 space-y-1 text-sm">
                @foreach($availableLeads as $lead)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded"
                            @checked(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id)>
                        <span>{{ $lead->name }}</span>
                        <span class="text-slate-400 text-xs">{{ $lead->phone }}</span>
                        @if($lead->assignee)
                            <span class="text-[10px] text-sky-700">({{ $lead->assignee->name }})</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">حفظ</button>
    </form>

    @if($group->leads->isNotEmpty())
        <div class="bg-white border rounded-xl p-5">
            <h3 class="font-bold text-sm mb-2">عملاء المجموعة حالياً ({{ $group->leads->count() }})</h3>
            <ul class="text-sm space-y-1 max-h-48 overflow-y-auto">
                @foreach($group->leads as $lead)
                    <li class="flex justify-between gap-2">
                        <span>{{ $lead->name }}</span>
                        <span class="text-slate-500 text-xs">{{ $lead->assignee->name ?? '—' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('admin.sales.groups.destroy', $group) }}" onsubmit="return confirm('حذف المجموعة؟')">
        @csrf @method('DELETE')
        <button type="submit" class="text-rose-700 text-sm">حذف المجموعة</button>
    </form>
</div>
@endsection
