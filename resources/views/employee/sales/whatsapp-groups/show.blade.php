@extends('layouts.employee')

@section('title', $whatsappGroup->subject)
@section('header', 'مجموعة واتساب: '.$whatsappGroup->subject)

@section('content')
@include('employee.sales.whatsapp-groups._styles')

@php $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p); @endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <a href="{{ $r('index') }}" class="text-sm text-slate-600 hover:underline">← مجموعات واتساب</a>
        <div class="flex flex-wrap gap-2">
            <form method="post" action="{{ $r('sync', $whatsappGroup) }}">@csrf<button type="submit" class="px-3 py-1.5 text-xs border rounded-lg">مزامنة من Meta</button></form>
            <form method="post" action="{{ $r('refresh-invite', $whatsappGroup) }}">@csrf<button type="submit" class="px-3 py-1.5 text-xs border rounded-lg">تجديد رابط الدعوة</button></form>
        </div>
    </div>

    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <div class="grid xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 space-y-4">
            <form method="post" action="{{ $r('update', $whatsappGroup) }}" class="sales-panel p-5 space-y-4">
                @csrf @method('PUT')
                <h3 class="font-bold text-slate-900">إعدادات المجموعة على Meta</h3>
                <div>
                    <label class="block text-sm font-medium mb-1">الاسم</label>
                    <input type="text" name="subject" value="{{ old('subject', $whatsappGroup->subject) }}" required class="w-full px-3 py-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">الوصف</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2.5 border rounded-lg">{{ old('description', $whatsappGroup->description) }}</textarea>
                </div>
                <p class="text-xs text-slate-500">موافقة الانضمام: {{ $whatsappGroup->join_approval_mode === 'approval_required' ? 'يتطلب موافقة' : 'تلقائي' }}</p>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold" @disabled(!$whatsappGroup->isActive())>حفظ على Meta</button>
            </form>

            <div class="sales-panel p-5">
                <h3 class="font-bold text-slate-900 mb-3">المدعوون والمنضمون ({{ $whatsappGroup->participants->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-500 text-xs"><tr><th class="text-right py-2">الاسم</th><th class="text-right py-2">الرقم</th><th class="text-right py-2">الحالة</th><th></th></tr></thead>
                        <tbody class="divide-y">
                            @foreach($whatsappGroup->participants as $p)
                                <tr>
                                    <td class="py-2">{{ $p->display_name ?: $p->salesLead?->name ?: '—' }}</td>
                                    <td class="py-2 dir-ltr text-left">{{ $p->phone }}</td>
                                    <td class="py-2">{{ $p->statusLabel() }}</td>
                                    <td class="py-2 text-left">
                                        @if($whatsappGroup->isActive() && $p->status !== 'removed')
                                            <form method="post" action="{{ $r('participants.destroy', [$whatsappGroup, $p]) }}" onsubmit="return confirm('إزالة العضو من المجموعة؟')">@csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 text-xs">إزالة</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="xl:col-span-5 space-y-4">
            <div class="sales-panel p-4 text-sm space-y-2">
                <p><span class="text-slate-500">الحالة:</span> <strong>{{ $whatsappGroup->statusLabel() }}</strong></p>
                <p><span class="text-slate-500">المزود:</span> Meta Cloud</p>
                @if($whatsappGroup->invite_link)
                    <p class="text-slate-500">رابط الدعوة:</p>
                    <input type="text" readonly value="{{ $whatsappGroup->invite_link }}" class="w-full text-xs dir-ltr border rounded px-2 py-1" onclick="this.select()">
                @endif
                @if($whatsappGroup->wa_group_jid)
                    <p class="text-[10px] text-slate-400 dir-ltr break-all">Group ID: {{ $whatsappGroup->wa_group_jid }}</p>
                @endif
            </div>

            @if($whatsappGroup->isActive())
                <form method="post" action="{{ $r('participants.store', $whatsappGroup) }}" class="sales-panel p-4 space-y-3">
                    @csrf
                    <h3 class="font-bold text-sm">إرسال دعوة Meta للعملاء</h3>
                    <select name="invite_template_name" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        <option value="">اختر قالب Group Invite</option>
                        @foreach($inviteTemplates as $tpl)
                            <option value="{{ $tpl['name'] }}" data-lang="{{ $tpl['language'] }}" @selected($whatsappGroup->invite_template_name === $tpl['name'])>{{ $tpl['label'] ?? $tpl['name'] }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="invite_template_language" value="{{ $whatsappGroup->invite_template_language ?: 'en' }}" id="show-invite-lang">
                    <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="w-full px-3 py-2 border rounded-lg dir-ltr">
                    <input type="text" name="phones[]" placeholder="2011xxxxxxxx" class="w-full px-3 py-2 border rounded-lg dir-ltr">
                    @if($availableLeads->isNotEmpty())
                        <p class="text-xs font-semibold text-slate-600">أو من العملاء:</p>
                        <div class="max-h-40 overflow-y-auto border rounded divide-y text-sm">
                            @foreach($availableLeads->take(30) as $lead)
                                <label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer">
                                    <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded">
                                    <span class="flex-1">{{ $lead->name }}</span>
                                    <span class="text-xs text-slate-500 dir-ltr">{{ $lead->phone }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <button type="submit" class="w-full py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">إرسال الدعوات</button>
                    <p class="text-[11px] text-slate-500">Meta لا يضيف الأرقام مباشرة — يُرسل قالب دعوة ويختار العميل الانضمام.</p>
                </form>

                <form method="post" action="{{ $r('import-crm', $whatsappGroup) }}" class="sales-panel p-4 space-y-3">
                    @csrf
                    <h3 class="font-bold text-sm">دعوة عملاء من مجموعة CRM</h3>
                    @if(!$whatsappGroup->invite_template_name)
                        <p class="text-xs text-amber-700">عيّن قالب دعوة من النموذج أعلاه أولاً (يُحفظ مع المجموعة).</p>
                    @endif
                    <select name="sales_lead_group_id" class="w-full px-3 py-2 border rounded-lg" required>
                        @foreach($crmGroups as $g)
                            <option value="{{ $g->id }}" @selected((int)$whatsappGroup->sales_lead_group_id === (int)$g->id)>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full py-2 border border-emerald-600 text-emerald-700 rounded-lg text-sm font-semibold" @disabled(!$whatsappGroup->invite_template_name)>إرسال دعوات CRM</button>
                </form>

                <form method="post" action="{{ $r('leave', $whatsappGroup) }}" onsubmit="return confirm('حذف المجموعة على Meta؟')">
                    @csrf
                    <button type="submit" class="w-full py-2 text-rose-700 border border-rose-200 rounded-lg text-sm">حذف المجموعة على Meta</button>
                </form>
            @endif
        </aside>
    </div>
</div>
@endsection
