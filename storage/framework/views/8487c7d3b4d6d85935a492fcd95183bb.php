<?php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waConfigured = \App\Support\WhatsAppCloudSettings::isAppConfigured();
    $waConnectionMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waConnectionMeta['can_send'] ?? false);
    $tpl = $welcomeTemplate ?? null;
    $tplApproved = $tpl?->isSendable() ?? false;
    $defaultBody = $defaultWelcomeBody ?? app(\App\Services\WorkshopWhatsAppTemplateService::class)->defaultWelcomeBody();
    $displayBody = str_replace(['{{1}}', '{{2}}'], ['{{name}}', '{{workshop_name}}'], $defaultBody);
    $batches = $workshopWhatsAppBatches ?? collect();
    $tplBodyDisplay = $tpl?->body_text
        ? str_replace(['{{1}}', '{{2}}'], ['{{name}}', '{{workshop_name}}'], $tpl->body_text)
        : '';
    $textareaBody = old('body_text');
    if ($textareaBody === null) {
        $textareaBody = $tplBodyDisplay !== '' ? $tplBodyDisplay : $displayBody;
    }
?>

<section class="rounded-xl border border-violet-200 bg-gradient-to-br from-violet-50/80 to-white p-4 sm:p-5 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-file-alt text-violet-600"></i>
                قالب ترحيب Meta — انضمام للورشة
            </h3>
            <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                أنشئ قالباً معتمداً من Meta وارسله لجميع المسجلين. المتغيرات:
                <code class="bg-white px-1 rounded text-[10px]">{{name}}</code>
                (اسم المسجّل) و
                <code class="bg-white px-1 rounded text-[10px]">{{workshop_name}}</code>
                (اسم الورشة).
            </p>
        </div>
        <?php if($tpl): ?>
            <span class="text-[10px] px-2.5 py-1 rounded-full font-bold
                <?php if($tplApproved): ?> bg-emerald-100 text-emerald-800
                <?php elseif($tpl->status === 'pending'): ?> bg-amber-100 text-amber-800
                <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                <?php echo e($tpl->statusLabel()); ?>

            </span>
        <?php endif; ?>
    </div>

    <?php if($tpl): ?>
        <div class="rounded-lg border border-violet-100 bg-white/80 px-3 py-2 text-xs text-slate-700 space-y-1">
            <p><strong>اسم القالب في Meta:</strong> <code class="bg-slate-100 px-1 rounded"><?php echo e($tpl->name); ?></code></p>
            <p><strong>اللغة:</strong> <?php echo e($tpl->language); ?></p>
            <?php if($tpl->body_text): ?>
                <p class="text-slate-600 whitespace-pre-line mt-2 border-t border-slate-100 pt-2"><?php echo e($tplBodyDisplay); ?></p>
            <?php endif; ?>
            <?php if($tpl->rejection_reason): ?>
                <p class="text-rose-700 bg-rose-50 rounded px-2 py-1 mt-1"><strong>سبب الرفض:</strong> <?php echo e($tpl->rejection_reason); ?></p>
            <?php endif; ?>
        </div>
        <form method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.sync', $workshop)); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-xs font-bold text-violet-700 hover:underline">
                <i class="fas fa-sync ml-1"></i> مزامنة الحالة من Meta
            </button>
        </form>
    <?php endif; ?>

    <?php if(!$waConfigured || !$waCanSend): ?>
        <p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
            أكمل <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-bold underline">ربط Meta WhatsApp</a> أولاً.
        </p>
    <?php elseif($phoneCountAll === 0): ?>
        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            لا يوجد مسجلون بأرقام هواتف.
        </p>
    <?php else: ?>
        
        <?php if(! $tplApproved): ?>
            <form method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.create', $workshop)); ?>" class="space-y-3 border-t border-violet-100 pt-4">
                <?php echo csrf_field(); ?>
                <label class="block text-xs font-bold text-slate-800">نص رسالة الترحيب (يُحوَّل تلقائياً لصيغة Meta {{1}} و {{2}})</label>
                <textarea name="body_text" rows="6" maxlength="1024"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs leading-relaxed"><?php echo e($textareaBody); ?></textarea>
                <button type="submit" <?php if(!$waCanSend): echo 'disabled'; endif; ?>
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <?php echo e($tpl ? 'إعادة إرسال القالب لـ Meta' : 'إنشاء القالب وإرساله لـ Meta'); ?>

                </button>
                <p class="text-[10px] text-slate-500">بعد موافقة Meta (عادة خلال دقائق إلى ساعات) اضغط «مزامنة الحالة» ثم «إرسال للجميع».</p>
            </form>
        <?php endif; ?>

        
        <?php if($tplApproved): ?>
            <div class="border-t border-violet-100 pt-4 space-y-3">
                <div class="text-[11px] text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
                    <span>اليوم: <?php echo e($pacing['day']); ?>/<?php echo e($pacing['max_day']); ?></span>
                    <?php if($remainingToday !== null): ?>
                        <span>متبقي اليوم: <?php echo e($remainingToday); ?></span>
                    <?php endif; ?>
                </div>
                <form method="POST" action="<?php echo e(route('admin.workshops.whatsapp-template.send', $workshop)); ?>"
                      id="workshop-wa-template-form" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <div class="flex flex-wrap gap-3 text-xs">
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="scope" value="all" checked class="text-violet-600"> كل الأرقام (<?php echo e($phoneCountAll); ?>)
                        </label>
                        <?php if($phoneCountOnline > 0): ?>
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="scope" value="online" class="text-violet-600"> أونلاين (<?php echo e($phoneCountOnline); ?>)
                            </label>
                        <?php endif; ?>
                        <?php if($phoneCountOffline > 0): ?>
                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="scope" value="offline" class="text-violet-600"> حضوري (<?php echo e($phoneCountOffline); ?>)
                            </label>
                        <?php endif; ?>
                        <label class="inline-flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="scope" value="phone" class="text-violet-600"> رقم محدد
                        </label>
                    </div>
                    <input type="text" name="phone" placeholder="2010xxxxxxx" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                    <button type="submit" <?php if(!$waCanSend): echo 'disabled'; endif; ?>
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white rounded-lg text-sm font-bold">
                        <i class="fas fa-paper-plane"></i>
                        إرسال قالب الترحيب لجميع المسجلين
                    </button>
                    <p class="text-[10px] text-slate-500">
                        يُسجَّل في «دفعات الواتساب» مع اسم القالب <code class="bg-slate-100 px-1 rounded"><?php echo e($tpl->name); ?></code> وعدد المستلمين.
                    </p>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if($batches->isNotEmpty()): ?>
        <div class="border-t border-violet-100 pt-3 space-y-2">
            <p class="text-xs font-bold text-slate-800"><i class="fas fa-history text-violet-600 ml-1"></i> سجل الإرسال</p>
            <div class="space-y-1.5 max-h-40 overflow-y-auto">
                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isTemplate = ($batch->meta['send_mode'] ?? '') === 'template';
                        $tplName = $batch->meta['template_name'] ?? null;
                    ?>
                    <a href="<?php echo e(route('admin.whatsapp.batches.show', $batch)); ?>"
                       class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 rounded-lg bg-white border border-slate-100 hover:border-violet-200 text-xs transition-colors">
                        <span class="font-semibold text-slate-800">
                            <?php if($isTemplate): ?>
                                <i class="fas fa-file-alt text-violet-600 ml-1"></i>
                                قالب <?php echo e($tplName ?: 'ترحيب'); ?>

                            <?php else: ?>
                                <i class="fas fa-comment text-emerald-600 ml-1"></i>
                                رسالة نصية
                            <?php endif; ?>
                        </span>
                        <span class="text-slate-500 tabular-nums"><?php echo e($batch->sent_count); ?>/<?php echo e($batch->total_count); ?> · <?php echo e($batch->statusLabel()); ?></span>
                        <span class="text-[10px] text-slate-400 w-full"><?php echo e($batch->created_at?->format('Y-m-d H:i')); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a href="<?php echo e(route('admin.whatsapp.batches.index')); ?>" class="text-[10px] text-violet-700 font-bold hover:underline">كل دفعات الواتساب ←</a>
        </div>
    <?php endif; ?>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const form = document.getElementById('workshop-wa-template-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const scope = form.querySelector('input[name="scope"]:checked')?.value || 'all';
        let count = <?php echo e((int) $phoneCountAll); ?>;
        if (scope === 'online') count = <?php echo e((int) $phoneCountOnline); ?>;
        else if (scope === 'offline') count = <?php echo e((int) $phoneCountOffline); ?>;
        else if (scope === 'phone') count = 1;
        if (!confirm('إرسال قالب الترحيب Meta إلى ' + count + ' مسجّل؟\n\nستظهر الدفعة في سجل الإدارة.')) {
            e.preventDefault();
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\_whatsapp_template.blade.php ENDPATH**/ ?>