<form method="post" action="{{ $r('store') }}" class="sales-panel p-5 md:p-6 space-y-6">
    @csrf

    <div>
        <p class="wa-section-title"><i class="fas fa-info-circle text-slate-400 ml-1"></i> بيانات المجموعة</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="120" class="px-3 py-2.5" placeholder="مثال: عملاء حملة يوليو">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">الوصف</label>
                <textarea name="description" rows="2" class="px-3 py-2.5" placeholder="وصف مختصر يظهر للمدعوين">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ربط بمجموعة CRM</label>
                <select name="sales_lead_group_id" class="px-3 py-2.5">
                    <option value="">— بدون —</option>
                    @foreach($crmGroups as $g)
                        <option value="{{ $g->id }}" @selected((int)old('sales_lead_group_id', $prefillCrmGroupId) === (int)$g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">موافقة الانضمام</label>
                <select name="join_approval_mode" class="px-3 py-2.5">
                    <option value="auto_approve" @selected(old('join_approval_mode', 'auto_approve') === 'auto_approve')>انضمام تلقائي</option>
                    <option value="approval_required" @selected(old('join_approval_mode') === 'approval_required')>يتطلب موافقة</option>
                </select>
            </div>
        </div>
    </div>

    <div>
        <p class="wa-section-title"><i class="fas fa-paper-plane text-slate-400 ml-1"></i> قالب الدعوة (Meta)</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">قالب Group Invite</label>
                <select name="invite_template_name" class="px-3 py-2.5" id="invite-template">
                    <option value="">— لاحقاً من صفحة المجموعة —</option>
                    @foreach($inviteTemplates as $tpl)
                        <option value="{{ $tpl['name'] }}" data-lang="{{ $tpl['language'] }}" @selected(old('invite_template_name') === $tpl['name'])>{{ $tpl['label'] ?? $tpl['name'] }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">من مكتبة Meta: utility · group_invite_link</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">لغة القالب</label>
                <input type="text" name="invite_template_language" id="invite-template-lang" value="{{ old('invite_template_language', 'en') }}" class="px-3 py-2.5 dir-ltr">
            </div>
        </div>
    </div>

    <div>
        <p class="wa-section-title"><i class="fas fa-user-plus text-slate-400 ml-1"></i> مدعوون (اختياري)</p>
        @if($prefillParticipants->isNotEmpty())
            <p class="text-xs text-emerald-700 mb-2"><i class="fas fa-check-circle ml-1"></i> {{ $prefillParticipants->count() }} عميل من مجموعة CRM</p>
            @foreach($prefillParticipants as $p)
                <input type="hidden" name="phones[]" value="{{ $p['phone'] }}">
                <input type="hidden" name="lead_ids[]" value="{{ $p['sales_lead_id'] }}">
            @endforeach
            <ul class="text-sm border border-slate-200 rounded-lg divide-y max-h-40 overflow-y-auto mb-3 bg-slate-50/50">
                @foreach($prefillParticipants as $p)
                    <li class="px-3 py-2 flex justify-between gap-2">
                        <span class="font-medium text-slate-800">{{ $p['display_name'] }}</span>
                        <span dir="ltr" class="text-slate-500 text-xs">{{ $p['phone'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        <div id="phones-wrap" class="space-y-2">
            @for($i = 0; $i < 3; $i++)
                <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="px-3 py-2.5 dir-ltr" value="{{ old('phones.'.$i) }}">
            @endfor
        </div>
        <button type="button" onclick="addWaPhone()" class="mt-2 text-xs text-sky-700 font-semibold hover:underline">+ إضافة رقم</button>
    </div>

    <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100">
        <button type="submit" class="btn-wa-primary">
            <i class="fab fa-whatsapp"></i> إنشاء المجموعة
        </button>
        <a href="{{ $r('index') }}" class="btn-wa-secondary">إلغاء</a>
    </div>
</form>

@push('scripts')
<script>
function addWaPhone() {
    const wrap = document.getElementById('phones-wrap');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'phones[]';
    input.placeholder = '2010xxxxxxxx';
    input.className = 'px-3 py-2.5 dir-ltr';
    wrap.appendChild(input);
}
document.getElementById('invite-template')?.addEventListener('change', function () {
    const lang = this.selectedOptions[0]?.dataset?.lang;
    if (lang) document.getElementById('invite-template-lang').value = lang;
});
</script>
@endpush
