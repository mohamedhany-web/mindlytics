@php
    $crm = $crmPayload ?? null;
    $agentsList = $agents ?? [];
@endphp
<aside class="wa-inbox-col sm-crm-sidebar border-s border-slate-200 bg-white overflow-hidden flex flex-col min-h-0"
       x-show="conversationId"
       x-cloak>
    <div class="px-3 py-2.5 border-b border-slate-200 bg-slate-50 shrink-0">
        <p class="text-xs font-black text-slate-800 flex items-center gap-1.5">
            <i class="fas fa-address-card text-sky-600"></i>
            بيانات العميل · CRM
        </p>
        <p class="text-[10px] text-slate-500 mt-0.5">الاسم والتعيين والربط بعملاء المبيعات</p>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto p-3 space-y-3 text-sm">
        @if(! ($crmReady ?? false))
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                شغّل على السيرفر:
                <code class="block mt-1 bg-white px-2 py-1 rounded">php artisan migrate --force</code>
            </div>
        @elseif(! $crm)
            <p class="text-xs text-slate-500">اختر محادثة لعرض التفاصيل.</p>
        @else
            <div class="flex items-center gap-3">
                @if(!empty($crm['participant_profile_pic']))
                    <img src="{{ $crm['participant_profile_pic'] }}" alt="" class="w-12 h-12 rounded-full object-cover border border-slate-200">
                @else
                    <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-black">
                        {{ mb_substr($crm['display_name'] ?? '?', 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 truncate" x-text="crm?.display_name || @js($crm['display_name'])"></p>
                    <p class="text-[11px] text-slate-500 truncate">
                        {{ $crm['platform_label'] }} · {{ $crm['page_name'] }}
                    </p>
                </div>
            </div>

            <dl class="space-y-1.5 text-[11px] bg-slate-50 rounded-xl p-2.5 border border-slate-100">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Username</dt>
                    <dd class="font-semibold text-slate-800 truncate">{{ $crm['participant_username'] ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Meta ID</dt>
                    <dd class="font-mono text-[10px] text-slate-700 truncate" title="{{ $crm['participant_id'] }}">{{ $crm['participant_id'] }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">الموظف</dt>
                    <dd class="font-semibold text-slate-800" x-text="crm?.assignee_name || @js($crm['assignee_name'] ?: 'غير معيّن')"></dd>
                </div>
            </dl>

            <form class="space-y-2" @submit.prevent="saveContact()">
                <label class="block text-[11px] font-bold text-slate-600">الاسم الظاهر</label>
                <input type="text" x-model="contactName" class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-2">

                <label class="block text-[11px] font-bold text-slate-600">الهاتف (للـ CRM)</label>
                <input type="text" x-model="contactPhone" placeholder="01xxxxxxxxx" class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-2" dir="ltr">

                <label class="block text-[11px] font-bold text-slate-600">البريد</label>
                <input type="email" x-model="contactEmail" class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-2" dir="ltr">

                <label class="block text-[11px] font-bold text-slate-600">ملاحظات داخلية</label>
                <textarea x-model="contactNotes" rows="2" class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-2"></textarea>

                <button type="submit" class="w-full text-xs font-bold py-2 rounded-lg bg-slate-800 text-white hover:bg-slate-900" :disabled="crmSaving">
                    حفظ البيانات
                </button>
            </form>

            <div class="space-y-2 border-t border-slate-100 pt-3">
                <label class="block text-[11px] font-bold text-slate-600">تعيين موظف مبيعات</label>
                <select x-model="assigneeId" class="w-full text-xs rounded-lg border border-slate-200 px-2.5 py-2">
                    <option value="">— اختر —</option>
                    @foreach($agentsList as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
                <button type="button" @click="assignAgent()" class="w-full text-xs font-bold py-2 rounded-lg bg-sky-600 text-white hover:bg-sky-700" :disabled="crmSaving || !assigneeId">
                    تعيين
                </button>
            </div>

            <div class="space-y-2 border-t border-slate-100 pt-3">
                <p class="text-[11px] font-bold text-slate-700">عميل المبيعات (Lead)</p>
                <template x-if="crm?.sales_lead_id">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-2.5 text-xs space-y-1">
                        <p class="font-bold text-emerald-900" x-text="crm.sales_lead_name"></p>
                        <p class="text-emerald-800" x-text="crm.sales_lead_stage_label"></p>
                        <a :href="crm.sales_lead_url" class="inline-flex items-center gap-1 font-bold text-emerald-700 underline" target="_blank">
                            فتح في CRM
                        </a>
                    </div>
                </template>
                <template x-if="!crm?.sales_lead_id">
                    <div class="space-y-2">
                        <p class="text-[11px] text-slate-500">لم يُربط بعميل بعد. أدخل الهاتف إن وُجد ثم أنشئ Lead.</p>
                        <button type="button" @click="createLead()" class="w-full text-xs font-bold py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700" :disabled="crmSaving">
                            إنشاء Lead في CRM
                        </button>
                        <div class="flex gap-1">
                            <input type="number" x-model="linkLeadId" placeholder="رقم Lead موجود" class="flex-1 text-xs rounded-lg border border-slate-200 px-2 py-2" dir="ltr">
                            <button type="button" @click="linkLead()" class="px-3 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50" :disabled="crmSaving || !linkLeadId">ربط</button>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="enrichProfile()" class="w-full text-[11px] font-semibold py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" :disabled="crmSaving">
                تحديث الاسم من Meta
            </button>

            <p class="text-[10px] text-rose-600" x-show="crmError" x-text="crmError"></p>
            <p class="text-[10px] text-emerald-600" x-show="crmOk" x-text="crmOk"></p>
        @endif
    </div>
</aside>
