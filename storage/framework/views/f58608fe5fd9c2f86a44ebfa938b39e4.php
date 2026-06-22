

<?php $__env->startSection('title', 'إعدادات ربط الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'settings'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'إعدادات الربط',
        'subtitle' => 'اربط Laravel على Hostinger بـ whatsapp-web.js Bridge على VPS.',
        'icon' => 'fas fa-plug',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="rounded-2xl bg-gradient-to-r from-sky-50 to-emerald-50 border-2 border-sky-200/60 p-5 text-sm text-slate-800 shadow-sm">
        <p class="font-bold flex items-center gap-2 text-sky-900">
            <i class="fas fa-lightbulb text-amber-500"></i>
            كيف يعمل الربط؟
        </p>
        <ol class="list-decimal list-inside mt-3 space-y-2 leading-relaxed text-slate-700 mr-1">
            <li>Bridge يعمل على VPS عبر PM2 (<code class="bg-white/80 px-1.5 py-0.5 rounded text-xs font-mono">wa-api.yourdomain.com</code>)</li>
            <li>Laravel يتصل بالجسر عبر HTTPS + Bearer Token</li>
            <li>امسح QR من لوحة الواتساب لربط حسابك</li>
        </ol>
    </div>

    <section class="<?php echo e($waSectionClass); ?> max-w-3xl">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-cog text-emerald-600"></i>
                إعدادات Bridge
            </h3>
        </div>
        <div class="p-5 sm:p-6">
            <form method="POST" action="<?php echo e(route('admin.whatsapp.settings.update')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">نوع الخدمة</label>
                    <select name="service_type" class="<?php echo e($waSelectClass); ?>">
                        <?php $__currentLoopData = [
                            'disabled' => 'معطّل (حفظ فقط بدون إرسال حقيقي)',
                            'wwebjs' => 'whatsapp-web.js Bridge (موصى به)',
                            'local' => 'محلي / Bridge (نفس wwebjs)',
                            'official' => 'WhatsApp Business API (Meta)',
                            'custom' => 'API مخصص',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php if(old('service_type', $settings['service_type'] ?? 'disabled') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">رابط Bridge (Node.js على VPS)</label>
                    <input type="url" name="bridge_url" value="<?php echo e(old('bridge_url', $settings['bridge_url'] ?? '')); ?>"
                           placeholder="https://wa-api.mindlytics-academy.com"
                           class="<?php echo e($waInputClass); ?> dir-ltr text-right font-mono text-sm">
                    <?php $__errorArgs = ['bridge_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">توكن الأمان (API_TOKEN)</label>
                    <input type="password" name="bridge_token" value="<?php echo e(old('bridge_token', $settings['bridge_token'] ?? '')); ?>"
                           autocomplete="new-password" placeholder="نفس API_TOKEN في .env على VPS"
                           class="<?php echo e($waInputClass); ?> dir-ltr text-right font-mono text-sm">
                    <?php $__errorArgs = ['bridge_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    <button type="submit" class="<?php echo e($waBtnPrimary); ?>">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                    <a href="<?php echo e(route('admin.whatsapp.index')); ?>" class="<?php echo e($waBtnSecondary); ?>">العودة للوحة</a>
                </div>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\settings.blade.php ENDPATH**/ ?>