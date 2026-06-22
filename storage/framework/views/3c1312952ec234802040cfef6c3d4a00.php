

<?php $__env->startSection('title', 'لوحة الواتساب - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $connectionStatus = $status['status'] ?? 'unknown';
    $statusMeta = [
        'ready' => ['label' => 'متصل وجاهز', 'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fas fa-check-circle', 'iconColor' => 'text-emerald-500', 'ring' => 'ring-emerald-500/20'],
        'qr' => ['label' => 'بانتظار مسح QR', 'badge' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fas fa-qrcode', 'iconColor' => 'text-amber-500', 'ring' => 'ring-amber-500/20'],
        'authenticated' => ['label' => 'تمت المصادقة', 'badge' => 'bg-sky-100 text-sky-800 border-sky-200', 'icon' => 'fas fa-shield-alt', 'iconColor' => 'text-sky-500', 'ring' => 'ring-sky-500/20'],
        'disconnected' => ['label' => 'غير متصل', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fas fa-unlink', 'iconColor' => 'text-slate-400', 'ring' => 'ring-slate-300/30'],
        'error' => ['label' => 'خطأ', 'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fas fa-times-circle', 'iconColor' => 'text-rose-500', 'ring' => 'ring-rose-500/20'],
        'auth_failure' => ['label' => 'فشل المصادقة', 'badge' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fas fa-ban', 'iconColor' => 'text-rose-500', 'ring' => 'ring-rose-500/20'],
    ];
    $meta = $statusMeta[$connectionStatus] ?? ['label' => $connectionStatus, 'badge' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fas fa-question-circle', 'iconColor' => 'text-slate-400', 'ring' => 'ring-slate-300/30'];
    $bridgeConfigured = !empty($settings['bridge_url']) && !empty($settings['bridge_token']);
    $tokenPreview = !empty($settings['bridge_token']) ? substr($settings['bridge_token'], 0, 8) . '••••' : '—';
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.whatsapp._nav', ['active' => 'dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'لوحة الواتساب',
        'subtitle' => 'إدارة الاتصال، مسح QR، وإرسال الرسائل عبر whatsapp-web.js Bridge على VPS.',
        'icon' => 'fab fa-whatsapp',
        'actions' => '
            <a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-paper-plane"></i> إرسال رسالة</a>
            <a href="' . route('admin.whatsapp.settings') . '" class="' . $waBtnSecondary . '"><i class="fas fa-cog"></i> الإعدادات</a>
        ',
        'statCards' => [
            ['label' => 'إجمالي الرسائل', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-comments', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'كل الرسائل المسجلة'],
            ['label' => 'مرسلة اليوم', 'value' => number_format($stats['sent_today'] ?? 0), 'icon' => 'fas fa-paper-plane', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم الإرسال بنجاح اليوم'],
            ['label' => 'رسائل فاشلة', 'value' => number_format($stats['failed'] ?? 0), 'icon' => 'fas fa-exclamation-triangle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تحتاج مراجعة'],
            ['label' => 'حالة الجسر', 'value' => $meta['label'], 'icon' => $meta['icon'], 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => $bridgeConfigured ? 'الإعدادات محفوظة' : 'أكمل إعدادات الربط'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <i class="fas fa-server text-emerald-600"></i>
            <h3 class="text-lg font-bold text-slate-900">معلومات الربط</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <p class="text-xs font-semibold text-slate-500 mb-1">نوع الخدمة</p>
                <p class="text-sm font-bold text-slate-900"><?php echo e($settings['service_type'] ?? 'disabled'); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 md:col-span-1">
                <p class="text-xs font-semibold text-slate-500 mb-1">رابط Bridge</p>
                <p class="text-sm font-mono text-slate-800 truncate" title="<?php echo e($settings['bridge_url'] ?? ''); ?>">
                    <?php echo e($settings['bridge_url'] ?: 'غير مضبوط'); ?>

                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <p class="text-xs font-semibold text-slate-500 mb-1">توكن API</p>
                <p class="text-sm font-mono text-slate-800"><?php echo e($tokenPreview); ?></p>
            </div>
        </div>
        <?php if (! ($bridgeConfigured)): ?>
            <div class="mx-5 mb-5 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900 flex items-start gap-3">
                <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                <p>أكمل <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="font-semibold underline">إعدادات الربط</a> برابط VPS والتوken قبل مسح QR.</p>
            </div>
        <?php endif; ?>
        <?php if(!empty($pacingStats)): ?>
            <div class="mx-5 mb-5 rounded-xl bg-sky-50 border border-sky-200 px-4 py-3 text-sm text-sky-900">
                <p class="font-semibold flex items-center gap-2"><i class="fas fa-shield-alt text-sky-600"></i> وضع الإرسال الآمن (Human-like)</p>
                <p class="mt-1 text-sky-800/90">
                    اليوم: <strong><?php echo e($pacingStats['day']); ?>/<?php echo e($pacingStats['max_day']); ?></strong> رسالة ·
                    هذه الساعة: <strong><?php echo e($pacingStats['hour']); ?>/<?php echo e($pacingStats['max_hour']); ?></strong> ·
                    تأخير 5–14 ث · استراحة كل 20 رسالة · ساعات 9–21
                </p>
            </div>
        <?php endif; ?>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">
        
        <section class="<?php echo e($waSectionClass); ?>">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-signal text-emerald-600"></i>
                    حالة الاتصال
                </h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo e($meta['badge']); ?>">
                    <i class="<?php echo e($meta['icon']); ?>"></i>
                    <?php echo e($meta['label']); ?>

                </span>
            </div>
            <div class="p-5 space-y-5">
                <?php if($bridgeError): ?>
                    <div class="rounded-xl bg-rose-50 border-2 border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-start gap-3">
                        <i class="fas fa-plug text-rose-500 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">تعذّر الاتصال بالجسر</p>
                            <p class="mt-1 text-rose-700/90"><?php echo e($bridgeError); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-br from-slate-50 to-white border border-slate-200 ring-4 <?php echo e($meta['ring']); ?>">
                    <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="<?php echo e($meta['icon']); ?> text-3xl <?php echo e($meta['iconColor']); ?>"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <?php if(!empty($status['phone'])): ?>
                            <p class="text-lg font-black text-slate-900 dir-ltr text-right">+<?php echo e($status['phone']); ?></p>
                            <?php if(!empty($status['pushname'])): ?>
                                <p class="text-sm text-slate-600"><?php echo e($status['pushname']); ?></p>
                            <?php endif; ?>
                            <?php if(!empty($status['platform'])): ?>
                                <p class="text-xs text-slate-500 mt-1"><i class="fas fa-mobile-alt ml-1"></i><?php echo e($status['platform']); ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-sm font-semibold text-slate-700">لم يتم ربط رقم واتساب بعد</p>
                            <p class="text-xs text-slate-500 mt-1">امسح QR من البطاقة المجاورة أو من تطبيق واتساب</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!empty($status['last_error'])): ?>
                    <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-800">
                        <span class="font-semibold">آخر خطأ:</span> <?php echo e($status['last_error']); ?>

                    </div>
                <?php endif; ?>

                <div class="flex flex-wrap gap-2 pt-1">
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.start')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="<?php echo e($waBtnPrimary); ?>">
                            <i class="fas fa-sync-alt"></i>
                            بدء / إعادة الاتصال
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.whatsapp.logout')); ?>" onsubmit="return confirm('قطع اتصال الواتساب؟ ستحتاج مسح QR من جديد.');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="<?php echo e($waBtnSecondary); ?>">
                            <i class="fas fa-sign-out-alt"></i>
                            قطع الاتصال
                        </button>
                    </form>
                </div>

                <ol class="text-xs text-slate-500 space-y-1.5 border-t border-slate-100 pt-4">
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">1.</span> تأكد أن Bridge يعمل على VPS (PM2 online)</li>
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">2.</span> اضغط «بدء الاتصال» ثم امسح QR</li>
                    <li class="flex items-start gap-2"><span class="font-bold text-emerald-600">3.</span> عند ظهور «متصل وجاهز» — جرّب إرسال رسالة</li>
                </ol>
            </div>
        </section>

        
        <section class="<?php echo e($waSectionClass); ?>" x-data="whatsappQr()" x-init="init()">
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-qrcode text-emerald-600"></i>
                    رمز QR للربط
                </h3>
                <p class="text-sm text-slate-500 mt-1">واتساب → الأجهزة المرتبطة → ربط جهاز</p>
            </div>
            <div class="p-5">
                <div class="relative flex flex-col items-center justify-center min-h-[280px] rounded-2xl border-2 border-dashed border-emerald-200/80 bg-gradient-to-br from-emerald-50/50 via-white to-sky-50/30 p-6">
                    <template x-if="connected">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 flex items-center justify-center mb-4 ring-4 ring-emerald-500/20">
                                <i class="fab fa-whatsapp text-4xl text-emerald-600"></i>
                            </div>
                            <p class="text-lg font-black text-emerald-800">الواتساب متصل</p>
                            <p class="text-sm text-emerald-600/80 mt-1">يمكنك إرسال الرسائل الآن</p>
                        </div>
                    </template>
                    <template x-if="!connected && qrImage">
                        <div class="text-center">
                            <img :src="qrImage" alt="WhatsApp QR" class="max-w-[240px] rounded-xl shadow-lg border-4 border-white mx-auto">
                            <p class="text-xs text-slate-500 mt-4">ينتهي QR خلال دقائق — حدّث إن لم ينجح المسح</p>
                        </div>
                    </template>
                    <template x-if="!connected && !qrImage && !loading">
                        <div class="text-center max-w-xs">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                                <i class="fas fa-qrcode text-2xl text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-600 font-medium" x-text="message || 'اضغط «تحديث QR» أو «بدء الاتصال»'"></p>
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-3xl text-emerald-500 mb-3"></i>
                            <p class="text-sm text-slate-500">جاري تحميل QR...</p>
                        </div>
                    </template>
                </div>

                <button type="button" @click="refresh()" class="mt-4 w-full <?php echo e($waBtnDark); ?>">
                    <i class="fas fa-redo"></i>
                    تحديث QR / الحالة
                </button>
            </div>
        </section>
    </div>

    
    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                إجراءات سريعة
            </h3>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="<?php echo e(route('admin.whatsapp.send')); ?>" class="group rounded-xl border border-slate-200 p-4 hover:border-emerald-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-paper-plane text-emerald-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-emerald-700">إرسال رسالة</p>
                <p class="text-xs text-slate-500 mt-1">رسالة فردية فورية</p>
            </a>
            <a href="<?php echo e(route('admin.whatsapp.messages')); ?>" class="group rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-history text-sky-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-sky-700">سجل الرسائل</p>
                <p class="text-xs text-slate-500 mt-1">مراجعة المرسل والفاشل</p>
            </a>
            <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="group rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-plug text-violet-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-violet-700">إعدادات الربط</p>
                <p class="text-xs text-slate-500 mt-1">رابط VPS والتوken</p>
            </a>
            <a href="<?php echo e(route('admin.messages.index')); ?>" class="group rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:shadow-md transition-all bg-white">
                <i class="fas fa-envelope text-amber-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 group-hover:text-amber-700">الرسائل العامة</p>
                <p class="text-xs text-slate-500 mt-1">تقارير وقوالب النظام</p>
            </a>
        </div>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function whatsappQr() {
    return {
        qrImage: null,
        connected: false,
        loading: false,
        message: '',
        pollTimer: null,
        init() {
            this.refresh();
            this.pollTimer = setInterval(() => this.refresh(true), 8000);
        },
        async refresh(silent = false) {
            if (!silent) this.loading = true;
            try {
                const statusRes = await fetch(<?php echo json_encode(route('admin.whatsapp.status'), 15, 512) ?>);
                const statusJson = await statusRes.json();
                const data = statusJson.data || {};
                this.connected = data.status === 'ready';
                if (this.connected) {
                    this.qrImage = null;
                    this.message = '';
                    return;
                }
                const qrRes = await fetch(<?php echo json_encode(route('admin.whatsapp.qr'), 15, 512) ?>);
                const qrJson = await qrRes.json();
                if (qrJson.success && qrJson.data?.qr_image) {
                    this.qrImage = qrJson.data.qr_image;
                    this.message = '';
                } else {
                    this.message = qrJson.error || qrJson.data?.error || statusJson.error || 'QR غير جاهز بعد — انتظر ثوانٍ';
                }
            } catch (e) {
                this.message = 'تعذّر الاتصال بالجسر — تحقق من الإعدادات';
            } finally {
                if (!silent) this.loading = false;
            }
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\whatsapp\index.blade.php ENDPATH**/ ?>