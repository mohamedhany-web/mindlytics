<?php
    $phoneCountAll = $whatsappPhoneCountAll ?? 0;
    $phoneCountOnline = $whatsappPhoneCountOnline ?? 0;
    $phoneCountOffline = $whatsappPhoneCountOffline ?? 0;
    $waConfigured = \App\Support\WhatsAppBridgeSettings::usesBridge();
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waTemplateVars = ['{{name}}', '{{phone}}', '{{workshop}}', '{{attendance}}', '{{location}}'];
    $messagePlaceholder = "مرحباً {{name}}،\n\nشكراً لتسجيلك في ورشة «{{workshop}}» ({{attendance}}).\n\n...";
?>

<section class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50/80 to-white p-4 sm:p-5 space-y-4 h-full">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب لكل المسجلين
            </h3>
            <p class="text-xs text-slate-600 mt-1">
                يُرسل عبر Bridge مع تأخير آمن بين الرسائل، إعادة محاولة تلقائية، ومتابعة حية لكل رقم.
            </p>
        </div>
        <?php if($waConfigured): ?>
            <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Bridge مفعّل</span>
        <?php else: ?>
            <span class="text-[10px] px-2 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">Bridge غير مفعّل</span>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-2 text-[11px]">
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong><?php echo e($phoneCountAll); ?></strong> رقم (الكل)
        </span>
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong><?php echo e($phoneCountOnline); ?></strong> أونلاين
        </span>
        <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
            <strong><?php echo e($phoneCountOffline); ?></strong> حضوري
        </span>
    </div>

    <?php if(!$waConfigured): ?>
        <p class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
            الإرسال التلقائي غير متاح — فعّل Bridge من
            <a href="<?php echo e(route('admin.whatsapp.index')); ?>" class="font-bold underline">قسم الواتساب</a>
            أو استخدم «فتح روابط يدوياً» بالأسفل.
        </p>
    <?php elseif($phoneCountAll === 0): ?>
        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            لا يوجد مسجلون بأرقام هواتف في هذه الورشة.
        </p>
    <?php else: ?>
        <div class="text-[11px] text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
            <span>اليوم: <?php echo e($pacing['day']); ?>/<?php echo e($pacing['max_day']); ?></span>
            <span>هذه الساعة: <?php echo e($pacing['hour']); ?>/<?php echo e($pacing['max_hour']); ?></span>
            <?php if($remainingToday !== null): ?>
                <span>متبقي اليوم: <?php echo e($remainingToday); ?></span>
            <?php endif; ?>
        </div>

        <form method="post" action="<?php echo e(route('admin.workshops.whatsapp-bulk', $workshop)); ?>"
              id="workshop-wa-bulk-form"
              class="space-y-3">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap gap-3 text-xs">
                <label class="inline-flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="scope" value="all" checked class="text-emerald-600"> كل الأرقام (<?php echo e($phoneCountAll); ?>)
                </label>
                <?php if($phoneCountOnline > 0): ?>
                    <label class="inline-flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="scope" value="online" class="text-emerald-600"> أونلاين فقط (<?php echo e($phoneCountOnline); ?>)
                    </label>
                <?php endif; ?>
                <?php if($phoneCountOffline > 0): ?>
                    <label class="inline-flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="scope" value="offline" class="text-emerald-600"> حضوري فقط (<?php echo e($phoneCountOffline); ?>)
                    </label>
                <?php endif; ?>
                <label class="inline-flex items-center gap-1 cursor-pointer">
                    <input type="radio" name="scope" value="phone" class="text-emerald-600"> رقم محدد
                </label>
            </div>
            <input type="text" name="phone" placeholder="2010xxxxxxx (عند اختيار رقم محدد)"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
            <div>
                <textarea name="message" rows="4" required maxlength="4096"
                          placeholder="<?php echo e($messagePlaceholder); ?>"
                          class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs"><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-[10px] text-slate-500 mt-1">
                    متغيرات:
                    <?php $__currentLoopData = $waTemplateVars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <code class="bg-slate-100 px-1 rounded"><?php echo e($var); ?></code>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </p>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">
                <i class="fas fa-paper-plane"></i>
                بدء الإرسال الآمن لكل الأرقام
            </button>
            <p class="text-[10px] text-slate-500 leading-relaxed">
                تأخير 5–14 ثانية بين الرسائل، استراحة كل 20 رسالة، حتى 3 محاولات لكل رقم، ومتابعة من صفحة الدفعة مع إعادة إرسال الفاشل فقط.
            </p>
        </form>
    <?php endif; ?>

    <?php if(!empty($latestWhatsAppBatch)): ?>
        <div class="pt-3 border-t border-emerald-200/60 text-xs">
            <span class="text-slate-600">آخر دفعة:</span>
            <a href="<?php echo e(route('admin.whatsapp.batches.show', $latestWhatsAppBatch)); ?>" class="text-emerald-700 font-bold hover:underline">
                #<?php echo e($latestWhatsAppBatch->id); ?> — <?php echo e($latestWhatsAppBatch->statusLabel()); ?>

                (<?php echo e($latestWhatsAppBatch->sent_count); ?>/<?php echo e($latestWhatsAppBatch->total_count); ?>)
            </a>
        </div>
    <?php endif; ?>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const form = document.getElementById('workshop-wa-bulk-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const scope = form.querySelector('input[name="scope"]:checked')?.value || 'all';
        let count = <?php echo e((int) $phoneCountAll); ?>;
        if (scope === 'online') count = <?php echo e((int) $phoneCountOnline); ?>;
        else if (scope === 'offline') count = <?php echo e((int) $phoneCountOffline); ?>;
        else if (scope === 'phone') count = 1;

        const msg = 'بدء إرسال ' + count + ' رسالة واتساب في الخلفية؟\n\n'
            + 'سيتم الإرسال تدريجياً لتقليل خطر الحظر.\n'
            + 'يمكنك متابعة التقدّم وإعادة إرسال الفاشل من صفحة الدفعة.';

        if (!confirm(msg)) {
            e.preventDefault();
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\_whatsapp_bulk.blade.php ENDPATH**/ ?>