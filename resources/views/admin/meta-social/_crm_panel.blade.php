@php
    $crm = $crmPayload ?? null;
    $agentsList = $agents ?? [];
@endphp
<aside class="sm-crm-sidebar flex flex-col min-h-0 overflow-hidden w-full h-full bg-white"
       x-show="conversationId"
       x-cloak>
    <div class="px-4 py-3 border-b border-[#e4e6eb] shrink-0 flex items-center justify-between">
        <div>
            <p class="text-sm font-black text-[#1c2b33]">Details</p>
            <p class="text-[10px] text-[#65676b] mt-0.5">أقوى من Business Suite · مربوط بـ CRM</p>
        </div>
        <button type="button" class="xl:hidden bs-icon-btn" @click="showDetails = false"><i class="fas fa-times"></i></button>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-4 text-sm">
        @if(! ($crmReady ?? false))
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                شغّل على السيرفر:
                <code class="block mt-1 bg-white px-2 py-1 rounded">php artisan migrate --force</code>
            </div>
        @elseif(! $crm)
            <p class="text-xs text-[#65676b]">اختر محادثة لعرض التفاصيل.</p>
        @else
            <div class="flex flex-col items-center text-center gap-2 pb-2">
                @if(!empty($crm['participant_profile_pic']))
                    <img src="{{ $crm['participant_profile_pic'] }}" alt="" class="w-16 h-16 rounded-full object-cover border border-[#e4e6eb]">
                @else
                    <div class="w-16 h-16 rounded-full bg-[#e7f3ff] text-[#0084FF] flex items-center justify-center font-black text-xl">
                        {{ mb_substr($crm['display_name'] ?? '?', 0, 1) }}
                    </div>
                @endif
                <div>
                    <p class="font-black text-[#1c2b33]" x-text="crm?.display_name || @js($crm['display_name'])"></p>
                    <p class="text-[11px] text-[#65676b]">
                        {{ $crm['platform_label'] }} · {{ $crm['page_name'] }}
                    </p>
                </div>
                <div class="flex gap-1.5 flex-wrap justify-center">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#e7f3ff] text-[#0084FF]" x-text="crm?.status === 'closed' ? 'Done' : 'Open'"></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#f0f2f5] text-[#65676b]" x-text="crm?.assignee_name || 'Unassigned'"></span>
                </div>
            </div>

            <section class="space-y-2">
                <h3 class="text-[11px] font-black uppercase tracking-wide text-[#65676b]">About</h3>
                <dl class="space-y-2 text-[11px] rounded-xl bg-[#f0f2f5] p-3">
                    <div class="flex justify-between gap-2">
                        <dt class="text-[#65676b]">Username</dt>
                        <dd class="font-semibold text-[#1c2b33] truncate">{{ $crm['participant_username'] ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-[#65676b]">Meta ID</dt>
                        <dd class="font-mono text-[10px] text-[#1c2b33] truncate" title="{{ $crm['participant_id'] }}">{{ $crm['participant_id'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-[#65676b]">Platform</dt>
                        <dd class="font-semibold">{{ $crm['platform_label'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-[#65676b]">Page</dt>
                        <dd class="font-semibold truncate">{{ $crm['page_name'] ?: '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="space-y-2">
                <h3 class="text-[11px] font-black uppercase tracking-wide text-[#65676b]">Contact</h3>
                <div class="rounded-xl border border-[#e7f3ff] bg-[#f5faff] p-2.5 text-[10px] text-[#1c2b33] leading-relaxed">
                    Meta <strong>مش بتدي رقم التليفون أو الإيميل</strong> من Messenger/Instagram حتى لو الحساب موثّق.
                    النظام بيحفظ الرقم تلقائياً لو العميل كتبه في الرسالة، أو لما يضغط «مشاركة الرقم» على Messenger.
                </div>
                <form class="space-y-2" @submit.prevent="saveContact()">
                    <input type="text" x-model="contactName" placeholder="الاسم" class="w-full text-xs rounded-xl border border-[#e4e6eb] px-3 py-2 bg-white">
                    <input type="text" x-model="contactPhone" placeholder="الهاتف (يتملأ تلقائياً)" class="w-full text-xs rounded-xl border border-[#e4e6eb] px-3 py-2 bg-white" dir="ltr">
                    <input type="email" x-model="contactEmail" placeholder="البريد (يتملأ تلقائياً)" class="w-full text-xs rounded-xl border border-[#e4e6eb] px-3 py-2 bg-white" dir="ltr">
                    <textarea x-model="contactNotes" rows="2" placeholder="ملاحظات داخلية" class="w-full text-xs rounded-xl border border-[#e4e6eb] px-3 py-2 bg-white"></textarea>
                    <button type="submit" class="w-full text-xs font-bold py-2.5 rounded-xl bg-[#1c2b33] text-white hover:bg-black" :disabled="crmSaving">
                        حفظ التفاصيل
                    </button>
                </form>
                <template x-if="(crm?.platform || @js($crm['platform'] ?? '')) === 'messenger'">
                    <button type="button" @click="requestPhone()" class="w-full text-xs font-bold py-2.5 rounded-xl bg-[#0084FF] text-white hover:opacity-90" :disabled="crmSaving">
                        <i class="fas fa-mobile-alt ml-1"></i> طلب رقم الهاتف من العميل
                    </button>
                </template>
            </section>

            <section class="space-y-2 border-t border-[#e4e6eb] pt-3">
                <h3 class="text-[11px] font-black uppercase tracking-wide text-[#65676b]">Assign</h3>
                <select x-model="assigneeId" class="w-full text-xs rounded-xl border border-[#e4e6eb] px-3 py-2">
                    <option value="">— Unassigned —</option>
                    @foreach($agentsList as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
                <button type="button" @click="assignAgent()" class="w-full text-xs font-bold py-2.5 rounded-xl bg-[#0084FF] text-white hover:opacity-90" :disabled="crmSaving || !assigneeId">
                    تعيين موظف مبيعات
                </button>
            </section>

            <section class="space-y-2 border-t border-[#e4e6eb] pt-3">
                <h3 class="text-[11px] font-black uppercase tracking-wide text-[#65676b]">CRM Lead</h3>
                <template x-if="crm?.sales_lead_id">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs space-y-1">
                        <p class="font-black text-emerald-900" x-text="crm.sales_lead_name"></p>
                        <p class="text-emerald-800" x-text="crm.sales_lead_stage_label"></p>
                        <a :href="crm.sales_lead_url" class="inline-flex items-center gap-1 font-bold text-emerald-700 underline" target="_blank">
                            فتح في CRM المبيعات
                        </a>
                    </div>
                </template>
                <template x-if="!crm?.sales_lead_id">
                    <div class="space-y-2">
                        <p class="text-[11px] text-[#65676b]">حوّل المحادثة لعميل محتمل في نظام المبيعات.</p>
                        <button type="button" @click="createLead()" class="w-full text-xs font-bold py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700" :disabled="crmSaving">
                            إنشاء Lead
                        </button>
                        <div class="flex gap-1">
                            <input type="number" x-model="linkLeadId" placeholder="رقم Lead" class="flex-1 text-xs rounded-xl border border-[#e4e6eb] px-3 py-2" dir="ltr">
                            <button type="button" @click="linkLead()" class="px-3 text-xs font-bold rounded-xl border border-[#e4e6eb] bg-white" :disabled="crmSaving || !linkLeadId">ربط</button>
                        </div>
                    </div>
                </template>
            </section>

            <button type="button" @click="enrichProfile()" class="w-full text-[11px] font-semibold py-2 rounded-xl border border-[#e4e6eb] text-[#65676b] hover:bg-[#f0f2f5]" :disabled="crmSaving">
                تحديث الملف من Meta
            </button>

            <p class="text-[10px] text-rose-600" x-show="crmError" x-text="crmError"></p>
            <p class="text-[10px] text-emerald-600" x-show="crmOk" x-text="crmOk"></p>
        @endif
    </div>
</aside>
