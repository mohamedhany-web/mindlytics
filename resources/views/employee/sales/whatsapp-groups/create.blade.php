@extends('layouts.employee')

@section('title', 'مجموعة واتساب جديدة')
@section('header', 'مجموعة واتساب جديدة')

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p); @endphp

<div class="space-y-4 max-w-3xl">
    <a href="{{ $r('index') }}" class="text-sm text-slate-600 hover:underline">← مجموعات واتساب</a>

    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <div class="sales-panel p-4 text-xs text-slate-600 border-sky-200 bg-sky-50/50">
        <p class="font-bold text-sky-900 mb-1">كيف تعمل المجموعات على Meta Cloud</p>
        <p>1) تُنشأ المجموعة عبر API. 2) تُرسل دعوة لكل عميل عبر قالب <strong>Group Invite</strong> معتمد. 3) العميل يختار الانضمام — لا يمكن إضافته يدوياً.</p>
    </div>

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
                <select name="sales_lead_group_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg">
                    <option value="">— بدون —</option>
                    @foreach($crmGroups as $g)
                        <option value="{{ $g->id }}" @selected((int)old('sales_lead_group_id', $prefillCrmGroupId) === (int)$g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">موافقة الانضمام</label>
                <select name="join_approval_mode" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg">
                    <option value="auto_approve" @selected(old('join_approval_mode', 'auto_approve') === 'auto_approve')>انضمام تلقائي</option>
                    <option value="approval_required" @selected(old('join_approval_mode') === 'approval_required')>يتطلب موافقة</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">قالب دعوة المجموعة</label>
                <select name="invite_template_name" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg" id="invite-template">
                    <option value="">— لاحقاً من صفحة المجموعة —</option>
                    @foreach($inviteTemplates as $tpl)
                        <option value="{{ $tpl['name'] }}" data-lang="{{ $tpl['language'] }}" @selected(old('invite_template_name') === $tpl['name'])>{{ $tpl['label'] ?? $tpl['name'] }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">أضف قالب Group Invite من مكتبة Meta (utility · group_invite_link).</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">لغة القالب</label>
                <input type="text" name="invite_template_language" id="invite-template-lang" value="{{ old('invite_template_language', 'en') }}" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg dir-ltr">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-2">عملاء لإرسال الدعوة لهم (اختياري)</label>
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

        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold" @disabled(!($cloud['connected'] ?? false))>
            إنشاء على Meta Cloud
        </button>
        @if(!($cloud['connected'] ?? false))
            <p class="text-xs text-amber-700">أكمل ربط Meta Cloud من إعدادات الواتساب أولاً.</p>
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
document.getElementById('invite-template')?.addEventListener('change', function () {
    const lang = this.selectedOptions[0]?.dataset?.lang;
    if (lang) document.getElementById('invite-template-lang').value = lang;
});
</script>
@endsection
