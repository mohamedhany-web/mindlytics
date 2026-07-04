

<?php $__env->startSection('title', 'لوحة السوشيال ميديا'); ?>
<?php $__env->startSection('header', 'إدارة السوشيال ميديا'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $connected = (bool) ($connectionMeta['can_use'] ?? false);
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.meta-social._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.meta-social._page-header', [
        'title' => 'Meta Business Suite',
        'subtitle' => 'Facebook Pages · Messenger · Instagram — ربط رسمي عبر Graph API',
        'icon' => 'fab fa-meta',
        'actions' => '
            <a href="' . route('admin.meta-social.inbox.index') . '" class="' . $smBtnPrimary . '"><i class="fas fa-inbox"></i> المحادثات</a>
            <a href="' . route('admin.meta-social.pages.index') . '" class="' . $smBtnSecondary . '"><i class="fab fa-facebook"></i> الصفحات</a>
            <a href="' . route('admin.meta-social.settings') . '" class="' . $smBtnSecondary . '"><i class="fas fa-plug"></i> إعدادات الربط</a>
        ',
        'statCards' => [
            ['label' => 'صفحات نشطة', 'value' => number_format($stats['pages'] ?? 0), 'icon' => 'fab fa-facebook', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'محادثات', 'value' => number_format($stats['conversations'] ?? 0), 'icon' => 'fas fa-comments', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'رسائل اليوم', 'value' => number_format($stats['messages_today'] ?? 0), 'icon' => 'fas fa-paper-plane', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'غير مقروء', 'value' => number_format($stats['unread'] ?? 0), 'icon' => 'fas fa-envelope', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(! $tablesReady): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 flex flex-wrap items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-database text-amber-600 text-xl mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-amber-900">تشغيل الترحيل مطلوب</h3>
                    <p class="text-sm text-amber-800 mt-1">نفّذ الأمر التالي على السيرفر لتفعيل جداول السوشيال ميديا:</p>
                    <code class="inline-block mt-2 bg-white px-3 py-1.5 rounded-lg text-xs font-mono border border-amber-200">php artisan migrate --force</code>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(! $connected): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 flex flex-wrap items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fab fa-meta text-amber-700 text-2xl mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-amber-900">Meta Business غير مربوط بعد</h3>
                    <p class="text-sm text-amber-800 mt-1">أدخل App ID و App Secret ثم سجّل الدخول عبر Facebook لربط صفحاتك وInstagram Business.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.meta-social.settings')); ?>" class="<?php echo e($smBtnMeta); ?>"><i class="fab fa-facebook"></i> بدء الربط</a>
        </div>
    <?php else: ?>
        <section class="<?php echo e($smSectionClass); ?>">
            <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between gap-3">
                <h3 class="font-bold text-slate-900">حالة الربط</h3>
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold"><?php echo e($connectionMeta['label'] ?? 'متصل'); ?></span>
            </div>
            <div class="p-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
                    <p class="text-xs text-sky-700 font-semibold">حساب Meta</p>
                    <p class="font-bold text-sky-900 mt-0.5"><?php echo e($connectionMeta['meta_user_name'] ?? $connection?->meta_user_name ?? '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">Meta User ID</p>
                    <p class="font-mono text-xs break-all mt-0.5"><?php echo e($connection?->meta_user_id ?? '—'); ?></p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">تاريخ الربط</p>
                    <p class="font-bold mt-0.5"><?php echo e($connection?->connected_at?->format('Y-m-d H:i') ?? '—'); ?></p>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="<?php echo e($smSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-bold text-slate-900">الصفحات المربوطة</h3>
            <?php if($connected): ?>
                <a href="<?php echo e(route('admin.meta-social.pages.index')); ?>" class="text-xs font-bold text-sky-700 hover:text-sky-900">عرض الكل ←</a>
            <?php endif; ?>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 sm:p-5 flex flex-wrap items-center gap-4 hover:bg-slate-50/50 transition-colors">
                    <?php if($page->picture_url): ?>
                        <img src="<?php echo e($page->picture_url); ?>" alt="" class="w-12 h-12 rounded-full object-cover border-2 border-slate-200 shadow-sm">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-100 to-blue-100 flex items-center justify-center border border-sky-200">
                            <i class="fab fa-facebook text-sky-500"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900"><?php echo e($page->page_name); ?></p>
                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e($page->platformLabel()); ?></p>
                        <?php if($page->instagram_username): ?>
                            <p class="text-xs text-pink-600 font-semibold mt-0.5"><i class="fab fa-instagram"></i> {{ $page->instagram_username }}</p>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($page->is_active): ?>
                            <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">نشطة</span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.meta-social.inbox.index', ['page' => $page->id])); ?>" class="<?php echo e($smBtnSecondary); ?> text-xs !py-2 !px-3">
                            <i class="fas fa-inbox"></i> المحادثات
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 sm:p-10 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-facebook text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-sm text-slate-600">لا توجد صفحات مربوطة بعد</p>
                    <?php if($connected): ?>
                        <a href="<?php echo e(route('admin.meta-social.pages.index')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-sky-700 hover:text-sky-900">
                            <i class="fas fa-sync"></i> مزامنة من Meta
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.meta-social.settings')); ?>" class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-sky-700 hover:text-sky-900">
                            <i class="fas fa-plug"></i> إعداد الربط أولاً
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="<?php echo e($smSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-900">اختصارات سريعة</h3>
        </div>
        <div class="p-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="<?php echo e(route('admin.meta-social.inbox.index')); ?>" class="rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/50 transition-all group">
                <i class="fas fa-inbox text-sky-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 text-sm group-hover:text-sky-800">Inbox الموحّد</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Messenger + Instagram</p>
            </a>
            <a href="<?php echo e(route('admin.meta-social.pages.index')); ?>" class="rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/50 transition-all group">
                <i class="fab fa-facebook text-[#0866FF] text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 text-sm group-hover:text-sky-800">إدارة الصفحات</p>
                <p class="text-[11px] text-slate-500 mt-0.5">تفعيل ومزامنة</p>
            </a>
            <a href="<?php echo e(route('admin.meta-social.settings')); ?>" class="rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/50 transition-all group">
                <i class="fas fa-cog text-slate-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 text-sm group-hover:text-sky-800">إعدادات Meta</p>
                <p class="text-[11px] text-slate-500 mt-0.5">App ID · Webhook</p>
            </a>
            <a href="<?php echo e(route('admin.meta-social.oauth.redirect')); ?>" class="rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/50 transition-all group">
                <i class="fab fa-meta text-violet-600 text-lg"></i>
                <p class="font-bold text-slate-900 mt-2 text-sm group-hover:text-sky-800">إعادة الربط</p>
                <p class="text-[11px] text-slate-500 mt-0.5">OAuth عبر Facebook</p>
            </a>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/meta-social/index.blade.php ENDPATH**/ ?>