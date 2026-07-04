

<?php $__env->startSection('title', 'إعدادات Meta Social'); ?>
<?php $__env->startSection('header', 'ربط Meta — Facebook & Instagram'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isConnected = (bool) ($connectionMeta['can_use'] ?? false);
    $savedEnabled = (bool) old('enabled', $config['enabled'] ?? false);
    $savedAppId = old('app_id', $config['app_id'] ?? '');
    $savedApiUrl = old('api_url', $config['api_url'] ?? '');
    $savedWebhookToken = old('webhook_verify_token', $config['webhook_verify_token'] ?? '');
    $savedScopes = old('oauth_scopes', $config['oauth_scopes'] ?? '');
    $hasSecret = (bool) ($config['has_app_secret'] ?? false);
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.meta-social._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.meta-social._page-header', [
        'title' => 'إعدادات Meta Graph API',
        'subtitle' => 'Facebook Login + Messenger + Instagram — نفس تطبيق Meta Developers',
        'icon' => 'fas fa-plug',
        'actions' => '
            <button type="button" id="btn-test-connection" class="' . $smBtnSecondary . '"><i class="fas fa-vial"></i> اختبار الربط</button>
            <a href="' . route('admin.meta-social.index') . '" class="' . $smBtnSecondary . '"><i class="fas fa-tachometer-alt"></i> لوحة السوشيال</a>
        ',
        'statCards' => [
            ['label' => 'حالة الربط', 'value' => $isConnected ? 'متصل' : 'غير متصل', 'icon' => $isConnected ? 'fas fa-check-circle' : 'fas fa-exclamation-circle', 'bg' => $isConnected ? 'bg-emerald-100' : 'bg-amber-100', 'text' => $isConnected ? 'text-emerald-600' : 'text-amber-600', 'description' => $connectionMeta['label'] ?? '—'],
            ['label' => 'السوشيال ميديا', 'value' => $savedEnabled ? 'مفعّل' : 'معطّل', 'icon' => 'fas fa-power-off', 'bg' => $savedEnabled ? 'bg-sky-100' : 'bg-slate-100', 'text' => $savedEnabled ? 'text-sky-600' : 'text-slate-600', 'description' => $savedEnabled ? 'جاهز بعد OAuth' : 'فعّل من النموذج'],
            ['label' => 'App ID', 'value' => $savedAppId !== '' ? $savedAppId : '—', 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => $hasSecret ? 'App Secret محفوظ' : 'App Secret مطلوب'],
            ['label' => 'Graph API', 'value' => $config['graph_version'] ?? 'v21.0', 'icon' => 'fas fa-code', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'Webhook + OAuth'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="test-result" class="hidden rounded-xl border px-4 py-3 text-sm"></div>

    
    <section class="<?php echo e($smSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-database text-sky-600"></i>
                البيانات المحفوظة حالياً
            </h3>
            <p class="text-xs text-slate-500 mt-1">تُحدَّث تلقائياً بعد كل حفظ ناجح</p>
        </div>
        <div class="p-5 sm:p-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">حالة OAuth</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($connectionMeta['label'] ?? '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">حساب Meta</p>
                    <p class="font-bold text-slate-900 mt-1"><?php echo e($connectionMeta['meta_user_name'] ?? '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">App ID</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedAppId !== '' ? $savedAppId : '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">Webhook Verify Token</p>
                    <p class="font-mono text-xs text-slate-800 break-all mt-1"><?php echo e($savedWebhookToken !== '' ? '••••••••' : '—'); ?></p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-3 mt-3 text-sm">
                <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
                    <p class="text-xs text-sky-700 font-semibold">Callback URL (Webhook)</p>
                    <code class="block text-xs dir-ltr break-all mt-1 text-sky-900"><?php echo e($config['webhook_url'] ?? ''); ?></code>
                </div>
                <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
                    <p class="text-xs text-sky-700 font-semibold">OAuth Redirect URI</p>
                    <code class="block text-xs dir-ltr break-all mt-1 text-sky-900"><?php echo e($config['oauth_redirect_url'] ?? ''); ?></code>
                </div>
            </div>

            <?php if($webhookStatus['last_received_at'] ?? null): ?>
                <p class="mt-4 text-xs text-emerald-700 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> آخر Webhook: <?php echo e($webhookStatus['last_received_at']); ?>

                </p>
            <?php endif; ?>

            <?php if($isConnected || $hasSecret || $savedAppId !== ''): ?>
                <form method="post" action="<?php echo e(route('admin.meta-social.oauth.disconnect')); ?>" class="mt-4"
                      onsubmit="return confirm('قطع ربط حساب Meta؟');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                        <i class="fas fa-unlink"></i>
                        قطع ربط الحساب
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <div class="grid lg:grid-cols-2 gap-4 sm:gap-6">
        
        <section class="<?php echo e($smSectionClass); ?> lg:col-span-1">
            <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-key text-sky-600"></i>
                    إعدادات Meta Graph API
                </h3>
                <p class="text-xs text-slate-500 mt-1">تُحفظ في المنصة (مشفّرة) — لا حاجة لتعديل ملف <code class="bg-slate-100 px-1 rounded">.env</code></p>
            </div>
            <div class="p-5 sm:p-6">
                <form method="post" action="<?php echo e(route('admin.meta-social.settings.update')); ?>" class="space-y-5" id="sm-settings-form">
                    <?php echo csrf_field(); ?>

                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="enabled" value="1" <?php if($savedEnabled): echo 'checked'; endif; ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-4 h-4">
                        تفعيل قسم السوشيال ميديا
                    </label>

                    <div>
                        <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-[11px] font-black">1</span>
                            بيانات التطبيق (Meta App)
                        </p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="<?php echo e($smLabelClass); ?>">Meta App ID *</label>
                                <input type="text" name="app_id" value="<?php echo e($savedAppId); ?>" required
                                       class="<?php echo e($smInputClass); ?> dir-ltr font-mono text-sm" placeholder="123456789012345">
                            </div>
                            <div>
                                <label class="<?php echo e($smLabelClass); ?>">
                                    Meta App Secret <?php echo e($hasSecret ? '' : '*'); ?>

                                    <?php if($hasSecret): ?>
                                        <span class="inline-flex items-center gap-1 text-emerald-700 text-[11px] font-bold mr-1"><i class="fas fa-lock"></i> محفوظ</span>
                                    <?php endif; ?>
                                </label>
                                <input type="password" name="app_secret" value=""
                                       autocomplete="new-password" class="<?php echo e($smInputClass); ?> dir-ltr font-mono text-sm"
                                       placeholder="<?php echo e($hasSecret ? 'محفوظ — اتركه فارغاً للإبقاء' : 'App Secret من Meta Developers'); ?>"
                                       <?php if(!$hasSecret): ?> required <?php endif; ?>>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="<?php echo e($smLabelClass); ?>">Graph API URL *</label>
                                <input type="url" name="api_url" value="<?php echo e($savedApiUrl); ?>" required
                                       class="<?php echo e($smInputClass); ?> dir-ltr font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5">
                        <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center text-[11px] font-black">2</span>
                            Webhook & OAuth
                        </p>
                        <div class="space-y-4">
                            <div>
                                <label class="<?php echo e($smLabelClass); ?>">Webhook Verify Token *</label>
                                <input type="text" name="webhook_verify_token" value="<?php echo e($savedWebhookToken); ?>" required
                                       class="<?php echo e($smInputClass); ?> dir-ltr font-mono text-sm">
                            </div>
                            <div>
                                <label class="<?php echo e($smLabelClass); ?>">OAuth Scopes (مفصولة بفاصلة)</label>
                                <textarea name="oauth_scopes" rows="4" class="<?php echo e($smTextareaClass); ?> dir-ltr font-mono text-xs"><?php echo e($savedScopes); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="<?php echo e($smBtnPrimary); ?> w-full sm:w-auto">
                        <i class="fas fa-save"></i> حفظ الإعدادات
                    </button>
                </form>
            </div>
        </section>

        
        <div class="space-y-4 sm:space-y-6">
            <section class="<?php echo e($smSectionClass); ?>">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fab fa-facebook text-[#0866FF]"></i>
                        ربط حساب Meta
                    </h3>
                </div>
                <div class="p-5 sm:p-6 space-y-4">
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs text-slate-500 font-semibold">الحالة</p>
                            <p class="font-bold text-slate-900 mt-0.5"><?php echo e($connectionMeta['label'] ?? '—'); ?></p>
                        </div>
                        <?php if($connectionMeta['meta_user_name'] ?? null): ?>
                            <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
                                <p class="text-xs text-sky-700 font-semibold">الحساب</p>
                                <p class="font-bold text-sky-900 mt-0.5"><?php echo e($connectionMeta['meta_user_name']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <?php if($hasSecret): ?>
                            <a href="<?php echo e(route('admin.meta-social.oauth.redirect')); ?>" class="<?php echo e($smBtnMeta); ?>">
                                <i class="fab fa-facebook"></i> تسجيل الدخول عبر Meta
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">احفظ App ID و Secret أولاً</span>
                        <?php endif; ?>
                        <form method="post" action="<?php echo e(route('admin.meta-social.settings.webhook-sync')); ?>" class="inline"><?php echo csrf_field(); ?>
                            <button type="submit" class="<?php echo e($smBtnSecondary); ?> text-sm"><i class="fas fa-sync"></i> مزامنة Webhook</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="<?php echo e($smSectionClass); ?>">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-book text-sky-600"></i>
                        خطوات Meta Developers
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                    <ol class="list-decimal list-inside space-y-2 text-sm text-slate-700">
                        <li>أضف Product: <strong>Facebook Login</strong> + <strong>Messenger</strong> + <strong>Instagram</strong></li>
                        <li>Valid OAuth Redirect URI:
                            <code class="block dir-ltr text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 mt-1 break-all"><?php echo e($config['oauth_redirect_url'] ?? ''); ?></code>
                        </li>
                        <li>Webhooks → Object: <strong>Page</strong> → Callback:
                            <code class="block dir-ltr text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 mt-1 break-all"><?php echo e($config['webhook_url'] ?? ''); ?></code>
                        </li>
                        <li>Subscribe: <code>messages</code>, <code>messaging_postbacks</code>, <code>message_reads</code></li>
                        <li>App Review: <code>pages_messaging</code>, <code>instagram_manage_messages</code> (للإنتاج)</li>
                    </ol>
                </div>
            </section>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('btn-test-connection')?.addEventListener('click', async function () {
    const box = document.getElementById('test-result');
    if (!box) return;
    box.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800', 'border-amber-200', 'bg-amber-50', 'text-amber-800');
    box.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-700');
    box.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الاختبار...';
    try {
        const res = await fetch(<?php echo json_encode(route('admin.meta-social.settings.test'), 15, 512) ?>, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const ok = data.can_use || data.can_send;
        box.classList.remove('border-slate-200', 'bg-slate-50', 'text-slate-700');
        box.classList.add(ok ? 'border-emerald-200' : 'bg-amber-50', ok ? 'bg-emerald-50' : 'border-amber-200', ok ? 'text-emerald-800' : 'text-amber-800');
        box.innerHTML = '<strong>' + (data.label || (ok ? 'متصل' : 'غير متصل')) + '</strong>' + (data.meta_user_name ? ' — ' + data.meta_user_name : '') + (data.last_error ? '<br><span class="text-xs">' + data.last_error + '</span>' : '');
    } catch (e) {
        box.classList.remove('border-slate-200', 'bg-slate-50', 'text-slate-700');
        box.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
        box.innerHTML = 'فشل الاتصال بالخادم';
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/meta-social/settings.blade.php ENDPATH**/ ?>