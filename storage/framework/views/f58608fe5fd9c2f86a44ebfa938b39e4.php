<?php $__env->startSection('title', 'ربط WhatsApp Business — Meta Cloud API'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
    $savedEnabled = (bool) old('enable_service', $config['enabled'] ?? false);
    $savedAppId = old('app_id', $config['app_id'] ?? '');
    $savedPhoneId = old('phone_number_id', $config['phone_number_id'] ?? '');
    $savedWaba = old('business_account_id', $config['business_account_id'] ?? '');
    $savedApiUrl = old('api_url', $config['api_url'] ?? '');
    $savedWebhookToken = old('webhook_verify_token', $config['webhook_verify_token'] ?? '');
    $displayPhone = $connectionMeta['display_phone'] ?? $config['display_phone_number'] ?? '—';
    $displayName = $connectionMeta['display_name'] ?? $config['verified_display_name'] ?? '—';
    $hasSecret = (bool) ($config['has_app_secret'] ?? false);
    $hasToken = (bool) ($config['has_access_token'] ?? false);
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'settings'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'ربط WhatsApp Business (Meta)',
        'subtitle' => 'إعداد يدوي عبر Meta Cloud API — بدون Bridge أو QR.',
        'icon' => 'fab fa-whatsapp',
        'actions' => '
            <button type="button" id="btn-test-connection" class="' . $waBtnSecondary . '"><i class="fas fa-vial"></i> اختبار الربط</button>
            <a href="' . route('admin.whatsapp.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-tachometer-alt"></i> لوحة الواتساب</a>
        ',
        'statCards' => [
            ['label' => 'حالة الربط', 'value' => $isConnected ? 'متصل' : 'غير متصل', 'icon' => $isConnected ? 'fas fa-check-circle' : 'fas fa-exclamation-circle', 'bg' => $isConnected ? 'bg-emerald-100' : 'bg-amber-100', 'text' => $isConnected ? 'text-emerald-600' : 'text-amber-600', 'description' => $connectionMeta['label'] ?? '—'],
            ['label' => 'إرسال الواتساب', 'value' => $savedEnabled ? 'مفعّل' : 'معطّل', 'icon' => 'fas fa-power-off', 'bg' => $savedEnabled ? 'bg-sky-100' : 'bg-slate-100', 'text' => $savedEnabled ? 'text-sky-600' : 'text-slate-600', 'description' => $savedEnabled ? 'جاهز بعد اكتمال Token' : 'فعّل من النموذج'],
            ['label' => 'App ID', 'value' => $savedAppId !== '' ? $savedAppId : '—', 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => $hasSecret ? 'App Secret محفوظ' : 'App Secret مطلوب'],
            ['label' => 'Phone Number ID', 'value' => $savedPhoneId !== '' ? $savedPhoneId : '—', 'icon' => 'fas fa-phone', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => $hasToken ? 'Access Token محفوظ' : 'Access Token مطلوب'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="test-result" class="hidden rounded-xl border px-4 py-3 text-sm"></div>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-database text-emerald-600"></i>
                البيانات المحفوظة حالياً
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحدَّث تلقائياً بعد كل حفظ ناجح</p>
        </div>
        <div class="p-5 sm:p-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">رقم الواتساب</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($displayPhone); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">الاسم المعتمد</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($displayName); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">Phone Number ID</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedPhoneId !== '' ? $savedPhoneId : '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500">WABA ID</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedWaba !== '' ? $savedWaba : '—'); ?></p>
                </div>
            </div>

            <?php if(!empty($connectionMeta['last_error']) && !$isConnected): ?>
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    <?php echo e($connectionMeta['last_error']); ?>

                </div>
            <?php endif; ?>

            <?php if($hasSecret || $hasToken || $savedAppId !== '' || $savedPhoneId !== ''): ?>
                <form method="POST" action="<?php echo e(route('admin.whatsapp.disconnect')); ?>" class="mt-4"
                      onsubmit="return confirm('مسح بيانات الربط وتعطيل الإرسال؟');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                        <i class="fas fa-trash-alt"></i>
                        مسح بيانات الربط
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-key text-emerald-600"></i>
                إعدادات Meta Cloud API
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحفظ في المنصة (مشفّرة) — لا حاجة لتعديل ملف <code class="bg-slate-100 px-1 rounded">.env</code></p>
        </div>
        <div class="p-5 sm:p-6">
            <form method="POST" action="<?php echo e(route('admin.whatsapp.settings.update')); ?>" class="space-y-5" id="wa-settings-form">
                <?php echo csrf_field(); ?>

                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-black">1</span>
                        بيانات التطبيق (Meta App)
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Meta App ID *</label>
                            <input type="text" name="app_id" value="<?php echo e($savedAppId); ?>" required
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="123456789012345">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">
                                Meta App Secret <?php echo e($hasSecret ? '' : '*'); ?>

                                <?php if($hasSecret): ?>
                                    <span class="inline-flex items-center gap-1 text-emerald-700 text-[11px] font-bold mr-1"><i class="fas fa-lock"></i> محفوظ</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" name="app_secret" value=""
                                   autocomplete="new-password" class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm"
                                   placeholder="<?php echo e($hasSecret ? 'محفوظ — اتركه فارغاً للإبقاء' : 'App Secret من Meta Developers'); ?>"
                                   <?php if(!$hasSecret): ?> required <?php endif; ?>>
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?php echo e($waLabelClass); ?>">Graph API URL *</label>
                            <input type="url" name="api_url" value="<?php echo e($savedApiUrl); ?>" required
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-[11px] font-black">2</span>
                        بيانات الإرسال (WhatsApp Business)
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">
                                Access Token
                                <?php if($hasToken): ?>
                                    <span class="inline-flex items-center gap-1 text-emerald-700 text-[11px] font-bold mr-1"><i class="fas fa-lock"></i> محفوظ</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" name="access_token" value=""
                                   autocomplete="new-password" class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm"
                                   placeholder="<?php echo e($hasToken ? 'محفوظ — اتركه فارغاً للإبقاء' : 'System User Token من Meta'); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Phone Number ID</label>
                            <input type="text" name="phone_number_id" value="<?php echo e($savedPhoneId); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="من WhatsApp → API Setup">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">WhatsApp Business Account ID (WABA)</label>
                            <input type="text" name="business_account_id" value="<?php echo e($savedWaba); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="اختياري — للـ Webhook">
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer px-4 py-2.5 rounded-xl border-2 <?php echo e($savedEnabled ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white'); ?> w-full">
                                <input type="checkbox" name="enable_service" value="1" <?php if($savedEnabled): echo 'checked'; endif; ?> class="rounded text-emerald-600">
                                تفعيل إرسال الواتساب
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-[11px] font-black">3</span>
                        Webhook (اختياري — لتتبع التسليم)
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Webhook Verify Token</label>
                            <input type="text" name="webhook_verify_token" value="<?php echo e($savedWebhookToken); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm" placeholder="سلسلة عشوائية للتحقق">
                        </div>
                        <div>
                            <label class="<?php echo e($waLabelClass); ?>">Webhook URL (انسخه إلى Meta)</label>
                            <input type="text" readonly value="<?php echo e($config['webhook_url']); ?>"
                                   class="<?php echo e($waInputClass); ?> dir-ltr font-mono text-sm bg-slate-50" onclick="this.select()">
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-sky-50 border border-sky-200 p-4 text-xs text-sky-900 leading-relaxed space-y-2">
                    <p class="font-bold">صلاحيات Meta المطلوبة:</p>
                    <p><code>whatsapp_business_management</code> · <code>whatsapp_business_messaging</code> · <code>business_management</code></p>
                    <p>بعد الحفظ: أضف Webhook URL في Meta Developers → WhatsApp → Configuration، واستخدم نفس Verify Token.</p>
                </div>

                <button type="submit" class="<?php echo e($waBtnPrimary); ?>">
                    <i class="fas fa-save"></i>
                    حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-paper-plane text-emerald-600"></i>
                اختبار إرسال رسالة
            </h3>
        </div>
        <div class="p-5 sm:p-6 space-y-3">
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">رقم الاختبار</label>
                    <input type="text" id="test-phone" placeholder="2010xxxxxxx" class="<?php echo e($waInputClass); ?> text-sm dir-ltr">
                </div>
                <div>
                    <label class="<?php echo e($waLabelClass); ?>">نص الرسالة (اختياري)</label>
                    <input type="text" id="test-message" placeholder="رسالة الاختبار" class="<?php echo e($waInputClass); ?> text-sm">
                </div>
            </div>
            <p class="text-[11px] text-slate-500">اترك الرقم فارغاً للتحقق من Token فقط. لإرسال حقيقي: يجب أن يكون المستلم ضمن نافذة 24 ساعة أو Template معتمد.</p>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const csrf = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const testUrl = <?php echo json_encode(route('admin.whatsapp.test-connection'), 15, 512) ?>;

    document.getElementById('btn-test-connection')?.addEventListener('click', async function () {
        const box = document.getElementById('test-result');
        if (!box) return;

        box.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800');
        box.textContent = 'جاري الاختبار…';

        const form = document.getElementById('wa-settings-form');
        const fd = new FormData(form);
        fd.append('test_phone', document.getElementById('test-phone')?.value || '');
        fd.append('test_message', document.getElementById('test-message')?.value || '');

        try {
            const res = await fetch(testUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json();
            if (data.success) {
                box.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
                box.textContent = data.message || 'نجح الاختبار — يمكنك تحديث الصفحة لرؤية البيانات المحدّثة.';
            } else {
                box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
                box.textContent = data.error || 'فشل الاختبار';
            }
        } catch (e) {
            box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
            box.textContent = 'خطأ في الاتصال بالسيرفر';
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\settings.blade.php ENDPATH**/ ?>