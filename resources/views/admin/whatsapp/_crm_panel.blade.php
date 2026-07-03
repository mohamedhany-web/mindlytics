@if($crmReady ?? false)
<aside class="hidden xl:flex xl:col-span-3 flex-col border-r border-slate-200 bg-white h-full min-h-0"
       x-show="conversationId && activeConversation"
       x-cloak>
    <div class="p-3 border-b border-slate-200 bg-slate-50 shrink-0">
        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
            <i class="fas fa-id-card text-emerald-600"></i> CRM
        </h4>
        <p class="text-[10px] text-slate-500 mt-0.5">ملاحظات داخلية — العميل لا يراها</p>
    </div>

    <div class="flex-1 overflow-y-auto p-3 space-y-4 text-sm wa-conv-scroll" x-show="activeConversation?.crm">

        {{-- الحالة والقسم --}}
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-600">الحالة</label>
            <select x-model="crmStatus" @change="updateCrmStatus()"
                    class="w-full rounded-lg border-slate-200 text-sm py-2">
                @foreach($crmStatuses as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-600">الموظف المسؤول</label>
            <select x-model="crmAssignee" @change="transferConversation()"
                    class="w-full rounded-lg border-slate-200 text-sm py-2">
                <option value="">غير معيّن</option>
                @foreach($crmAgents as $agent)
                    <option value="{{ $agent['id'] }}">{{ $agent['name'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-2" x-show="transferReasonOpen">
            <label class="text-xs font-bold text-slate-600">سبب النقل (اختياري)</label>
            <input type="text" x-model="transferReason" @keydown.enter="confirmTransfer()"
                   class="w-full rounded-lg border-slate-200 text-sm py-2" placeholder="مثال: يحتاج دعم فني">
            <button type="button" @click="confirmTransfer()" class="text-xs text-emerald-700 font-bold">تأكيد النقل</button>
        </div>

        {{-- الوسوم --}}
        <div>
            <label class="text-xs font-bold text-slate-600 block mb-2">الوسوم</label>
            <div class="flex flex-wrap gap-1.5">
                @php
                    $tagColorClasses = [
                        'amber' => 'bg-amber-100 border-amber-300 text-amber-800',
                        'sky' => 'bg-sky-100 border-sky-300 text-sky-800',
                        'emerald' => 'bg-emerald-100 border-emerald-300 text-emerald-800',
                        'slate' => 'bg-slate-100 border-slate-300 text-slate-800',
                        'violet' => 'bg-violet-100 border-violet-300 text-violet-800',
                        'rose' => 'bg-rose-100 border-rose-300 text-rose-800',
                    ];
                @endphp
                @foreach($crmTags as $tag)
                    @php $activeTagClass = $tagColorClasses[$tag->color] ?? $tagColorClasses['slate']; @endphp
                    <button type="button"
                            @click="toggleTag({{ $tag->id }})"
                            class="text-[10px] px-2 py-1 rounded-full border font-semibold transition-colors tag-btn-{{ $tag->id }}"
                            data-active-class="{{ $activeTagClass }}"
                            :class="hasTag({{ $tag->id }}) ? $el.dataset.activeClass : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- بيانات العميل --}}
        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/80 space-y-2" x-show="activeConversation?.crm?.contact">
            <p class="text-xs font-bold text-slate-700">بيانات العميل</p>
            <template x-if="activeConversation?.crm?.sales_lead_url">
                <a :href="activeConversation.crm.sales_lead_url" class="text-xs text-sky-700 font-bold hover:underline block">
                    <i class="fas fa-external-link-alt ml-1"></i> فتح في CRM المبيعات
                </a>
            </template>
            <p class="text-xs text-slate-600" x-show="activeConversation?.crm?.contact?.email">
                <i class="fas fa-envelope ml-1 text-slate-400"></i>
                <span x-text="activeConversation?.crm?.contact?.email"></span>
            </p>
            <p class="text-xs text-slate-600" x-show="activeConversation?.crm?.contact?.company">
                <i class="fas fa-building ml-1 text-slate-400"></i>
                <span x-text="activeConversation?.crm?.contact?.company"></span>
            </p>
            <p class="text-xs text-slate-600" x-show="activeConversation?.crm?.contact?.lifetime_value">
                قيمة متوقعة: <span x-text="activeConversation?.crm?.contact?.lifetime_value"></span>
            </p>
        </div>

        {{-- ملاحظات داخلية --}}
        <div>
            <label class="text-xs font-bold text-slate-600 block mb-2">ملاحظات داخلية</label>
            <textarea x-model="noteBody" rows="2" placeholder="ملاحظة للفريق فقط..."
                      class="w-full rounded-lg border-slate-200 text-xs resize-none"></textarea>
            <button type="button" @click="addNote()" :disabled="!noteBody.trim() || crmSaving"
                    class="mt-1.5 text-xs px-3 py-1.5 rounded-lg bg-slate-800 text-white font-bold disabled:opacity-40">
                إضافة ملاحظة
            </button>
            <div class="mt-3 space-y-2 max-h-40 overflow-y-auto">
                <template x-for="note in crmNotes" :key="'n-' + note.id">
                    <div class="rounded-lg bg-amber-50 border border-amber-100 p-2 text-xs">
                        <p class="text-slate-800 whitespace-pre-wrap" x-text="note.body"></p>
                        <p class="text-[10px] text-slate-500 mt-1">
                            <span x-text="note.author"></span> · <span x-text="note.created_at_human"></span>
                        </p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Timeline --}}
        <div>
            <label class="text-xs font-bold text-slate-600 block mb-2">الخط الزمني</label>
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                <template x-for="ev in crmTimeline" :key="'ev-' + ev.id">
                    <div class="flex gap-2 text-xs">
                        <div class="w-1.5 shrink-0 rounded-full bg-emerald-400 mt-1.5"></div>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800" x-text="ev.title"></p>
                            <p class="text-slate-600 truncate" x-text="ev.description"></p>
                            <p class="text-[10px] text-slate-400" x-text="(ev.performed_by ? ev.performed_by + ' · ' : '') + ev.created_at_human"></p>
                        </div>
                    </div>
                </template>
                <p x-show="crmTimeline.length === 0" class="text-xs text-slate-400">لا أحداث بعد</p>
            </div>
        </div>
    </div>

    <p x-show="crmError" class="text-xs text-rose-600 px-3 py-2 border-t" x-text="crmError"></p>
</aside>
@endif
