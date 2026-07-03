@extends('layouts.admin')

@section('title', 'مجموعة واتساب جديدة')
@section('header', 'مجموعة واتساب جديدة')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p); @endphp

<div class="p-4 md:p-6 space-y-4 max-w-3xl">
    <a href="{{ $r('index') }}" class="text-sm text-slate-600 hover:underline">← مجموعات واتساب</a>

    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <form method="post" action="{{ $r('store') }}" class="sales-panel p-5 space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة على واتساب *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="120" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">وصف المجموعة</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ربط بمجموعة عملاء (CRM)</label>
                <select name="sales_lead_group_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg" id="crm-group-select">
                    <option value="">— بدون —</option>
                    @foreach($crmGroups as $g)
                        <option value="{{ $g->id }}" @selected((int)old('sales_lead_group_id', $prefillCrmGroupId) === (int)$g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="announce_only" value="1" class="rounded" @checked(old('announce_only'))>
                <span>الرسائل للمشرفين فقط (Announce)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="restrict_info" value="1" class="rounded" @checked(old('restrict_info'))>
                <span>تعديل معلومات المجموعة للمشرفين فقط</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-2">الأعضاء (رقم واحد على الأقل) *</label>
            @if($prefillParticipants->isNotEmpty())
                <p class="text-xs text-emerald-700 mb-2">تم تحميل {{ $prefillParticipants->count() }} عميل من مجموعة CRM</p>
                @foreach($prefillParticipants as $p)
                    <input type="hidden" name="phones[]" value="{{ $p['phone'] }}">
                    <input type="hidden" name="lead_ids[]" value="{{ $p['sales_lead_id'] }}">
                @endforeach
                <ul class="text-sm border border-slate-200 rounded-lg divide-y max-h-48 overflow-y-auto mb-3">
                    @foreach($prefillParticipants as $p)
                        <li class="px-3 py-2 flex justify-between"><span>{{ $p['display_name'] }}</span><span dir="ltr" class="text-slate-500">{{ $p['phone'] }}</span></li>
                    @endforeach
                </ul>
            @endif
            <div id="phones-wrap" class="space-y-2">
                @for($i = 0; $i < 3; $i++)
                    <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="w-full px-3 py-2 border border-slate-200 rounded-lg dir-ltr" value="{{ old('phones.'.$i) }}">
                @endfor
            </div>
            <button type="button" onclick="addPhone()" class="mt-2 text-xs text-emerald-700 font-semibold">+ رقم آخر</button>
        </div>

        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold" @disabled(!($bridge['connected'] ?? false))>
            إنشاء على واتساب
        </button>
        @if(!($bridge['connected'] ?? false))
            <p class="text-xs text-amber-700">يجب أن تكون جلسة الجسر متصلة أولاً.</p>
        @endif
    </form>
</div>

<script>
function addPhone() {
    const wrap = document.getElementById('phones-wrap');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'phones[]';
    input.placeholder = '2010xxxxxxxx';
    input.className = 'w-full px-3 py-2 border border-slate-200 rounded-lg dir-ltr';
    wrap.appendChild(input);
}
</script>
@endsection
