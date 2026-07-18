<?php if($crmReady ?? false): ?>
<aside class="wa-crm-sidebar wa-inbox-col hidden xl:flex flex-col min-h-0 overflow-hidden border-e border-slate-200 bg-white"
       x-show="conversationId && activeConversation"
       x-cloak>
    <div class="p-3 border-b border-slate-200 bg-slate-50 shrink-0">
        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
            <i class="fas fa-id-card text-emerald-600"></i> CRM
        </h4>
        <p class="text-[10px] text-slate-500 mt-0.5">ملاحظات داخلية — العميل لا يراها</p>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto p-3 space-y-4 text-sm wa-conv-scroll" x-show="activeConversation?.crm" style="-webkit-overflow-scrolling: touch;">

        
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-600">حالة المحادثة</label>
            <select x-model="crmStatus" @change="updateCrmStatus()"
                    class="w-full rounded-lg border-slate-200 text-sm py-2">
                <?php $__currentLoopData = $crmStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="space-y-2" x-show="activeConversation?.crm?.sales_lead_id">
            <label class="text-xs font-bold text-slate-600">مرحلة Pipeline</label>
            <select x-model="crmLeadStage" @change="updateLeadStage()"
                    class="w-full rounded-lg border-slate-200 text-sm py-2">
                <option value="">— غير مرتبط —</option>
                <?php $__currentLoopData = $pipelineStages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-[10px] text-slate-500" x-show="activeConversation?.crm?.sales_lead_stage_label">
                الحالية: <span x-text="activeConversation?.crm?.sales_lead_stage_label"></span>
            </p>
        </div>

        
        <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-3 space-y-2" x-show="activeConversation?.crm?.sales_lead_id" x-cloak>
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-bold text-teal-900 flex items-center gap-1.5">
                    <i class="fas fa-calendar-check"></i> Next Follow
                </p>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                      :class="activeConversation?.crm?.next_follow_overdue ? 'bg-rose-100 text-rose-700' : (activeConversation?.crm?.next_follow_up_label ? 'bg-white text-teal-800 border border-teal-200' : 'bg-slate-100 text-slate-500')"
                      x-text="activeConversation?.crm?.next_follow_up_label || 'بدون موعد'"></span>
            </div>
            <p class="text-[10px] text-teal-800/80" x-show="activeConversation?.crm?.next_follow_up_human"
               x-text="activeConversation?.crm?.next_follow_up_human"></p>

            <div class="grid grid-cols-3 gap-1.5">
                <button type="button" @click="setNextFollowQuick('2h')" :disabled="crmSaving"
                        class="text-[10px] font-bold px-2 py-1.5 rounded-lg bg-white border border-teal-200 text-teal-800 hover:bg-teal-100 disabled:opacity-40">
                    بعد ساعتين
                </button>
                <button type="button" @click="setNextFollowQuick('tomorrow10')" :disabled="crmSaving"
                        class="text-[10px] font-bold px-2 py-1.5 rounded-lg bg-white border border-teal-200 text-teal-800 hover:bg-teal-100 disabled:opacity-40">
                    بكرة 10ص
                </button>
                <button type="button" @click="setNextFollowQuick('tomorrow18')" :disabled="crmSaving"
                        class="text-[10px] font-bold px-2 py-1.5 rounded-lg bg-white border border-teal-200 text-teal-800 hover:bg-teal-100 disabled:opacity-40">
                    بكرة 6م
                </button>
            </div>

            <div class="flex gap-1.5">
                <input type="datetime-local" x-model="crmNextFollowAt"
                       class="flex-1 rounded-lg border-teal-200 text-xs py-1.5 bg-white">
                <button type="button" @click="saveNextFollow()" :disabled="crmSaving || !crmNextFollowAt"
                        class="shrink-0 text-xs px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold disabled:opacity-40">
                    حفظ
                </button>
            </div>
            <input type="text" x-model="crmNextFollowNote" maxlength="500"
                   placeholder="ملاحظة المتابعة (اختياري)"
                   class="w-full rounded-lg border-teal-200 text-xs py-1.5 bg-white">
        </div>

        <?php if(($inboxAudience ?? 'admin') === 'admin'): ?>
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-600">الموظف المسؤول</label>
            <select x-model="crmAssignee" @change="transferConversation()"
                    class="w-full rounded-lg border-slate-200 text-sm py-2">
                <option value="">غير معيّن</option>
                <?php $__currentLoopData = $crmAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($agent['id']); ?>"><?php echo e($agent['name']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="space-y-2" x-show="transferReasonOpen">
            <label class="text-xs font-bold text-slate-600">سبب النقل (اختياري)</label>
            <input type="text" x-model="transferReason" @keydown.enter="confirmTransfer()"
                   class="w-full rounded-lg border-slate-200 text-sm py-2" placeholder="مثال: يحتاج دعم فني">
            <button type="button" @click="confirmTransfer()" class="text-xs text-emerald-700 font-bold">تأكيد النقل</button>
        </div>
        <?php endif; ?>

        
        <div>
            <label class="text-xs font-bold text-slate-600 block mb-2">الوسوم</label>
            <div class="flex flex-wrap gap-1.5">
                <?php
                    $tagColorClasses = [
                        'amber' => 'bg-amber-100 border-amber-300 text-amber-800',
                        'sky' => 'bg-sky-100 border-sky-300 text-sky-800',
                        'emerald' => 'bg-emerald-100 border-emerald-300 text-emerald-800',
                        'slate' => 'bg-slate-100 border-slate-300 text-slate-800',
                        'violet' => 'bg-violet-100 border-violet-300 text-violet-800',
                        'rose' => 'bg-rose-100 border-rose-300 text-rose-800',
                    ];
                ?>
                <?php $__currentLoopData = $crmTags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $activeTagClass = $tagColorClasses[$tag->color] ?? $tagColorClasses['slate']; ?>
                    <button type="button"
                            @click="toggleTag(<?php echo e($tag->id); ?>)"
                            class="text-[10px] px-2 py-1 rounded-full border font-semibold transition-colors tag-btn-<?php echo e($tag->id); ?>"
                            data-active-class="<?php echo e($activeTagClass); ?>"
                            :class="hasTag(<?php echo e($tag->id); ?>) ? $el.dataset.activeClass : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                        <?php echo e($tag->name); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
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
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\_crm_panel.blade.php ENDPATH**/ ?>