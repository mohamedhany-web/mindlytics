<?php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $waConfigured = \App\Support\WhatsAppCloudSettings::isAppConfigured();
    $waConnectionMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waConnectionMeta['can_send'] ?? false);
    $templates = $approvedWhatsAppTemplates ?? collect();
    $welcomeTpl = $welcomeTemplate ?? null;
    $batches = $workshopWhatsAppBatches ?? collect();
    $defaultTplKey = $welcomeTpl?->isSendable()
        ? $welcomeTpl->name.'|'.$welcomeTpl->language
        : ($templates->first() ? $templates->first()->name.'|'.$templates->first()->language : '');
    $messagePlaceholder = "مرحباً @{{name}}،\n\nشكراً لتسجيلك في ورشة «@{{workshop}}» (@{{attendance}}).";
    $workshopGroupInviteCode = $workshopGroupInviteCode ?? '';
    $whatsappTemplatesSendMeta = $whatsappTemplatesSendMeta ?? [];
    $openInviteModal = session('show_group_invite_modal', false);
?>

<section class="rounded-2xl border border-emerald-200 bg-white shadow-sm overflow-hidden" x-data="workshopWaSend({
    templatesMeta: <?php echo \Illuminate\Support\Js::from($whatsappTemplatesSendMeta)->toHtml() ?>,
    savedInviteCode: <?php echo \Illuminate\Support\Js::from($workshopGroupInviteCode)->toHtml() ?>,
    openOnLoad: <?php echo \Illuminate\Support\Js::from((bool) $openInviteModal)->toHtml() ?>,
})">
    <div class="px-5 py-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50/80 to-white flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب للمسجلين
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">اختر المستلمين والقالب ثم اضغط إرسال — تُتابع الدفعة من سجل الإرسال.</p>
        </div>
        <div class="flex flex-wrap gap-2 text-[11px]">
            <span class="px-2.5 py-1 rounded-full bg-white border border-slate-200 font-semibold"><?php echo e($phoneCountAll); ?> رقم</span>
            <?php if($waCanSend): ?>
                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Meta متصل</span>
            <?php else: ?>
                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">غير متصل</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <?php if(!$waConfigured || !$waCanSend): ?>
            <p class="text-sm text-rose-800 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3">
                أكمل <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-bold underline">ربط Meta WhatsApp</a> قبل الإرسال.
            </p>
        <?php elseif($phoneCountAll === 0): ?>
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                لا يوجد مسجلون بأرقام هواتف في هذه الورشة.
            </p>
        <?php else: ?>
            
            <div>
                <p class="text-xs font-bold text-slate-800 mb-2">المستلمون</p>
                <div class="flex flex-wrap gap-3 text-sm" id="wa-scope-radios">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="wa_scope_ui" value="all" checked class="text-emerald-600"> كل المسجلين (<?php echo e($phoneCountAll); ?>)
                    </label>
                    <?php if($phoneCountOnline > 0): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="wa_scope_ui" value="online" class="text-emerald-600"> أونلاين (<?php echo e($phoneCountOnline); ?>)
                        </label>
                    <?php endif; ?>
                    <?php if($phoneCountOffline > 0): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="wa_scope_ui" value="offline" class="text-emerald-600"> حضوري (<?php echo e($phoneCountOffline); ?>)
                        </label>
                    <?php endif; ?>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="wa_scope_ui" value="phone" class="text-emerald-600"> رقم محدد
                    </label>
                </div>
                <input type="text" id="wa-phone-input" placeholder="2010xxxxxxx" class="mt-2 w-full max-w-xs rounded-lg border border-slate-200 px-3 py-2 text-sm hidden">
            </div>

            
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="mode='template'"
                        :class="mode === 'template' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors">
                    <i class="fas fa-file-alt ml-1"></i> قالب Meta
                </button>
                <button type="button" @click="mode='text'"
                        :class="mode === 'text' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors">
                    <i class="fas fa-comment ml-1"></i> رسالة نصية
                </button>
            </div>

            
            <form x-show="mode === 'template'" x-cloak method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.send', $workshop)); ?>"
                  id="workshop-wa-template-form" class="space-y-3 rounded-xl border border-violet-100 bg-violet-50/40 p-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="scope" id="tpl-scope" value="all">
                <input type="hidden" name="phone" id="tpl-phone" value="">

                <?php if($templates->isEmpty()): ?>
                    <p class="text-sm text-amber-800">لا توجد قوالب معتمدة.
                        <a href="<?php echo e(route('admin.whatsapp.templates.create', ['workshop_id' => $workshop->id])); ?>" class="font-bold underline">أنشئ قالب ترحيب للورشة</a>
                        أو من <a href="<?php echo e(route('admin.whatsapp.templates.index')); ?>" class="font-bold underline">قسم القوالب</a>.
                    </p>
                <?php else: ?>
                    <label class="block text-xs font-bold text-slate-800">القالب</label>
                    <select name="template_name" id="wa-template-name" required
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm bg-white">
                        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $key = $tpl->name.'|'.$tpl->language; ?>
                            <option value="<?php echo e($tpl->name); ?>" data-lang="<?php echo e($tpl->language); ?>"
                                    <?php if($key === $defaultTplKey): echo 'selected'; endif; ?>>
                                <?php echo e($tpl->name); ?> · <?php echo e($tpl->language); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="hidden" name="template_language" id="wa-template-lang"
                           value="<?php echo e($templates->firstWhere(fn($t) => ($t->name.'|'.$t->language) === $defaultTplKey)?->language ?? $templates->first()?->language); ?>">
                    <input type="hidden" name="group_invite_code" id="group-invite-code-input" value="">

                    <p class="text-[11px] text-slate-600">
                        المتغيرات تُملأ تلقائياً: {{1}} الاسم، {{2}} الورشة، {{5}} الحضور.
                        <span class="text-violet-800 font-semibold">{{3}} = كود دعوة الجروب</span> (ليس الرابط الكامل).
                    </p>
                    <p x-show="selectedTemplateNeedsInvite() && !inviteCode.trim()" x-cloak class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        لم يُحفظ رابط الجروب في الورشة — سيُطلب منك كود الدعوة عند الإرسال.
                        <a href="<?php echo e(route('admin.workshops.edit', $workshop)); ?>" class="font-bold underline">أو أضفه في تعديل الورشة</a>.
                    </p>
                    <p x-show="inviteCode.trim()" x-cloak class="text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2" dir="ltr">
                        كود الجروب: <span class="font-mono font-bold" x-text="inviteCode"></span>
                        <button type="button" @click="openInviteModal()" class="mr-2 text-violet-700 font-bold underline" dir="rtl">تعديل</button>
                    </p>

                    <button type="button" @click="submitTemplateForm()" <?php if(!$waCanSend): echo 'disabled'; endif; ?>
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold">
                        <i class="fas fa-paper-plane"></i>
                        إرسال القالب للمسجلين
                    </button>
                <?php endif; ?>
            </form>

            
            <form x-show="mode === 'text'" x-cloak method="POST" action="<?php echo e(route('admin.workshops.whatsapp-bulk', $workshop)); ?>"
                  id="workshop-wa-bulk-form" class="space-y-3 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="scope" id="text-scope" value="all">
                <input type="hidden" name="phone" id="text-phone" value="">
                <textarea name="message" rows="4" required maxlength="4096" placeholder="<?php echo e($messagePlaceholder); ?>"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"><?php echo e(old('message')); ?></textarea>
                <p class="text-[10px] text-slate-500">
                    متغيرات: <code class="bg-white px-1 rounded">{{name}}</code>
                    <code class="bg-white px-1 rounded">{{workshop}}</code>
                    <code class="bg-white px-1 rounded">{{phone}}</code>
                    <code class="bg-white px-1 rounded">{{attendance}}</code>
                </p>
                <button type="submit" <?php if(!$waCanSend): echo 'disabled'; endif; ?>
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الرسالة للمسجلين
                </button>
            </form>
        <?php endif; ?>

        
        <?php if($batches->isNotEmpty()): ?>
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-700 mb-2"><i class="fas fa-history text-slate-500 ml-1"></i> آخر الإرسالات</p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $batches->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isTemplate = ($batch->meta['send_mode'] ?? '') === 'template';
                            $tplName = $batch->meta['template_name'] ?? null;
                        ?>
                        <a href="<?php echo e(route('admin.whatsapp.batches.show', $batch)); ?>"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 hover:border-emerald-300 text-xs font-semibold text-slate-800">
                            <?php if($isTemplate): ?>
                                <i class="fas fa-file-alt text-violet-600"></i>
                                <?php echo e(Str::limit($tplName ?: 'قالب', 20)); ?>

                            <?php else: ?>
                                <i class="fas fa-comment text-emerald-600"></i>
                                نصية
                            <?php endif; ?>
                            <span class="text-slate-500 tabular-nums"><?php echo e($batch->sent_count); ?>/<?php echo e($batch->total_count); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.whatsapp.batches.index')); ?>" class="text-xs text-emerald-700 font-bold self-center hover:underline">كل الدفعات</a>
                </div>
            </div>
        <?php endif; ?>

        
        <details class="rounded-xl border border-slate-200" <?php if($welcomeTpl && !$welcomeTpl->isSendable()): ?> open <?php endif; ?>>
            <summary class="px-4 py-3 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50">
                <i class="fas fa-cog text-slate-400 ml-1"></i> إعداد قالب ترحيب الورشة · إيميل
            </summary>
            <div class="px-4 pb-4 pt-1 space-y-4 border-t border-slate-100">
                <?php if($welcomeTpl): ?>
                    <div class="text-xs text-slate-600 space-y-1">
                        <p>قالب الورشة: <code class="bg-slate-100 px-1 rounded"><?php echo e($welcomeTpl->name); ?></code>
                            — <span class="font-bold"><?php echo e($welcomeTpl->statusLabel()); ?></span></p>
                        <?php if($welcomeTpl->rejection_reason): ?>
                            <p class="text-rose-700"><?php echo e($welcomeTpl->rejection_reason); ?></p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.sync', $workshop)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-xs font-bold text-violet-700 hover:underline">مزامنة من Meta</button>
                    </form>
                <?php endif; ?>

                <?php if(!$welcomeTpl || !$welcomeTpl->isSendable()): ?>
                    <div class="rounded-lg border border-violet-100 bg-violet-50/50 p-3 space-y-2">
                        <p class="text-xs text-slate-700">أنشئ أو عدّل قالب الترحيب بنفس نموذج Meta الرسمي (Header، Body، Footer، أزرار، متغيرات).</p>
                        <a href="<?php echo e(route('admin.whatsapp.templates.create', ['workshop_id' => $workshop->id])); ?>"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-violet-600 text-white text-xs font-bold hover:bg-violet-700">
                            <i class="fas fa-plus-circle"></i>
                            <?php echo e($welcomeTpl ? 'تعديل قالب الورشة' : 'إنشاء قالب ترحيب'); ?>

                        </a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.workshops.send-acceptance', $workshop)); ?>" class="flex flex-wrap items-end gap-2 pt-2 border-t border-slate-100">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-bold text-slate-700 block mb-1">إيميل القبول (<?php echo e($emailPendingCount ?? 0); ?> متبقي)</label>
                        <select name="scope" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                            <option value="all">كل المسجلين</option>
                            <option value="email">بريد محدد</option>
                        </select>
                    </div>
                    <input type="email" name="email" placeholder="email@..." class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold">إرسال</button>
                </form>
            </div>
        </details>
    </div>

    
    <div x-show="showInviteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
         @keydown.escape.window="closeInviteModal()">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-xl border border-slate-200 p-5 space-y-4" @click.outside="closeInviteModal()">
            <div>
                <h4 class="text-base font-black text-slate-900">كود دعوة جروب واتساب</h4>
                <p class="text-xs text-slate-600 mt-1">
                    القالب يحتاج المتغير <code dir="ltr" class="bg-slate-100 px-1 rounded">{{3}}</code> —
                    الصق <strong>كود الدعوة فقط</strong> من رابط الجروب.
                </p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">كود الدعوة</label>
                <input type="text" x-model="inviteCodeDraft" dir="ltr"
                       placeholder="Ld0j8PUAprmCnDi65uUqTC"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                       @keydown.enter.prevent="confirmInviteModal()">
                <p class="text-[10px] text-slate-500 mt-1">
                    من رابط مثل <code dir="ltr">https://chat.whatsapp.com/<strong>هذا_الكود</strong></code>
                </p>
            </div>
            <div class="flex flex-wrap gap-2 justify-end">
                <button type="button" @click="closeInviteModal()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    إلغاء
                </button>
                <button type="button" @click="confirmInviteModal()"
                        class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">
                    متابعة الإرسال
                </button>
            </div>
        </div>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
function workshopWaSend(config) {
    return {
        mode: 'template',
        showInviteModal: false,
        inviteCode: config.savedInviteCode || '',
        inviteCodeDraft: config.savedInviteCode || '',
        templatesMeta: config.templatesMeta || [],
        pendingSubmit: false,

        init() {
            const hidden = document.getElementById('group-invite-code-input');
            if (hidden && (this.inviteCode || '').trim() !== '') {
                hidden.value = this.inviteCode.trim();
            }
            if (config.openOnLoad) {
                this.openInviteModal();
            }
        },

        selectedTemplateMeta() {
            const nameEl = document.getElementById('wa-template-name');
            const langEl = document.getElementById('wa-template-lang');
            if (!nameEl || !langEl) return null;
            return this.templatesMeta.find(t => t.name === nameEl.value && t.language === langEl.value) || null;
        },

        selectedTemplateNeedsInvite() {
            const meta = this.selectedTemplateMeta();
            return !!(meta && meta.needs_invite_code);
        },

        openInviteModal() {
            this.inviteCodeDraft = this.inviteCode || '';
            this.showInviteModal = true;
        },

        closeInviteModal() {
            this.showInviteModal = false;
            this.pendingSubmit = false;
        },

        confirmInviteModal() {
            const code = (this.inviteCodeDraft || '').trim();
            if (!code) {
                alert('أدخل كود دعوة الجروب.');
                return;
            }
            this.inviteCode = code;
            const hidden = document.getElementById('group-invite-code-input');
            if (hidden) hidden.value = code;
            this.showInviteModal = false;
            if (this.pendingSubmit) {
                this.pendingSubmit = false;
                this.submitTemplateForm(true);
            }
        },

        submitTemplateForm(skipInviteCheck = false) {
            const form = document.getElementById('workshop-wa-template-form');
            if (!form) return;

            if (typeof window.workshopWaSyncScope === 'function') window.workshopWaSyncScope();

            const scope = document.getElementById('tpl-scope')?.value || 'all';
            const needsInvite = this.selectedTemplateNeedsInvite();
            const hasCode = (this.inviteCode || '').trim() !== '';

            if (needsInvite && !hasCode && !skipInviteCheck) {
                this.pendingSubmit = true;
                this.openInviteModal();
                return;
            }

            const hidden = document.getElementById('group-invite-code-input');
            if (hidden) hidden.value = hasCode ? this.inviteCode.trim() : '';

            if (!confirm('إرسال القالب إلى ' + window.workshopWaConfirmCount(scope) + ' مسجّل؟')) {
                return;
            }

            form.submit();
        },
    };
}

(function () {
    const scopeRadios = document.querySelectorAll('input[name="wa_scope_ui"]');
    const phoneInput = document.getElementById('wa-phone-input');
    const tplScope = document.getElementById('tpl-scope');
    const tplPhone = document.getElementById('tpl-phone');
    const textScope = document.getElementById('text-scope');
    const textPhone = document.getElementById('text-phone');

    function syncScope() {
        const scope = document.querySelector('input[name="wa_scope_ui"]:checked')?.value || 'all';
        const phone = phoneInput?.value || '';
        if (phoneInput) {
            phoneInput.classList.toggle('hidden', scope !== 'phone');
        }
        [tplScope, textScope].forEach(el => { if (el) el.value = scope; });
        [tplPhone, textPhone].forEach(el => { if (el) el.value = phone; });
    }

    scopeRadios.forEach(r => r.addEventListener('change', syncScope));
    phoneInput?.addEventListener('input', syncScope);
    syncScope();

    const tplSelect = document.getElementById('wa-template-name');
    const tplLang = document.getElementById('wa-template-lang');
    if (tplSelect && tplLang) {
        tplLang.value = tplSelect.selectedOptions[0]?.dataset.lang || tplLang.value;
        tplSelect.addEventListener('change', function () {
            tplLang.value = this.selectedOptions[0]?.dataset.lang || '';
        });
    }

    function confirmCount(scope) {
        let count = <?php echo e((int) $phoneCountAll); ?>;
        if (scope === 'online') count = <?php echo e((int) $phoneCountOnline); ?>;
        else if (scope === 'offline') count = <?php echo e((int) $phoneCountOffline); ?>;
        else if (scope === 'phone') count = 1;
        return count;
    }

    window.workshopWaSyncScope = syncScope;
    window.workshopWaConfirmCount = confirmCount;

    document.getElementById('workshop-wa-template-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
    });

    document.getElementById('workshop-wa-bulk-form')?.addEventListener('submit', function (e) {
        syncScope();
        const scope = textScope?.value || 'all';
        if (!confirm('إرسال الرسالة إلى ' + confirmCount(scope) + ' مسجّل؟')) e.preventDefault();
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\_workshop_messaging.blade.php ENDPATH**/ ?>