<?php
    $leadsWithPhone = $leadsWithPhone ?? ($group->leads ?? collect())->filter(fn ($l) => ! empty($l->phone));
    $phoneCount = $leadsWithPhone->count();
    $waConfigured = \App\Support\WhatsAppCloudSettings::usesOfficial();
    $waMeta = app(\App\Services\WhatsAppCloudService::class)->connectionMeta();
    $waCanSend = (bool) ($waMeta['can_send'] ?? false);
    $pacing = app(\App\Services\WhatsAppPacingService::class)->usageStats();
    $remainingToday = app(\App\Services\WhatsAppPacingService::class)->remainingDailyQuota();
    $waTemplateVars = ['{{name}}', '{{company}}', '{{phone}}'];
    $messagePlaceholder = 'مرحباً {{name}}، ...';
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
?>

<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-emerald-600"></i>
                إرسال واتساب جماعي للمجموعة
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">
                يُرسل لـ <strong><?php echo e($phoneCount); ?></strong> عميل لديه رقم — عبر الطابور مع تأخير آمن بين الرسائل.
            </p>
        </div>
        <?php if($waCanSend): ?>
            <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold border border-emerald-200">Meta متصل</span>
        <?php elseif($waConfigured): ?>
            <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold border border-amber-200"><?php echo e($waMeta['label'] ?? 'غير جاهز'); ?></span>
        <?php else: ?>
            <span class="text-xs px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-semibold border border-rose-200">Meta غير مفعّل</span>
        <?php endif; ?>
    </div>

    <div class="p-5 space-y-4">
        <?php if(! $waConfigured): ?>
            <p class="text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2">
                إرسال الواتساب غير متاح — <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-bold underline">ربط Meta WhatsApp</a>
            </p>
        <?php elseif(! $waCanSend): ?>
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                <?php echo e($waMeta['label'] ?? 'WhatsApp غير مربوط'); ?> — <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-bold underline">أكمل الربط</a>
            </p>
        <?php elseif($phoneCount === 0): ?>
            <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                لا يوجد عملاء بأرقام هواتف في هذه المجموعة.
            </p>
        <?php else: ?>
            <div class="text-xs text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
                <span>اليوم: <?php echo e($pacing['day']); ?>/<?php echo e($pacing['max_day']); ?></span>
                <span>هذه الساعة: <?php echo e($pacing['hour']); ?>/<?php echo e($pacing['max_hour']); ?></span>
                <?php if($remainingToday !== null): ?>
                    <span>متبقي اليوم: <?php echo e($remainingToday); ?></span>
                <?php endif; ?>
            </div>

            <form method="post" action="<?php echo e($formAction); ?>"
                  onsubmit="return confirm('بدء إرسال <?php echo e($phoneCount); ?> رسالة واتساب في الخلفية؟\n\nسيتم الإرسال تدريجياً لتقليل خطر الحظر.');"
                  class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نص الرسالة</label>
                    <textarea name="message" rows="5" required maxlength="4096"
                              placeholder="<?php echo e($messagePlaceholder); ?>"
                              class="<?php echo e($inputClass); ?>"><?php echo e(old('message')); ?></textarea>
                    <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="text-xs text-slate-500 mt-1">
                        متغيرات:
                        <?php $__currentLoopData = $waTemplateVars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <code class="bg-slate-100 px-1 rounded"><?php echo e($var); ?></code>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </p>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                    <i class="fas fa-paper-plane"></i>
                    إرسال لـ <?php echo e($phoneCount); ?> رقم (Queue)
                </button>
                <p class="text-[11px] text-slate-500">يُعاد المحاولة تلقائياً حتى 3 مرات عند أخطاء الاتصال المؤقتة.</p>
            </form>
        <?php endif; ?>

        <?php if(! empty($latestBatch)): ?>
            <div class="pt-3 border-t border-slate-100 text-sm">
                <span class="text-slate-600">آخر دفعة:</span>
                <a href="<?php echo e($latestBatchUrl ?? '#'); ?>" class="text-emerald-700 font-semibold hover:underline">
                    #<?php echo e($latestBatch->id); ?> — <?php echo e($latestBatch->statusLabel()); ?>

                    (<?php echo e($latestBatch->sent_count); ?>/<?php echo e($latestBatch->total_count); ?>)
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/groups/_whatsapp_bulk.blade.php ENDPATH**/ ?>