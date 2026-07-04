

<?php $__env->startSection('title', 'صفحات Facebook & Instagram'); ?>
<?php $__env->startSection('header', 'إدارة الصفحات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.meta-social._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.meta-social._page-header', [
        'title' => 'صفحات Meta المربوطة',
        'subtitle' => 'Facebook Page + Instagram Business المرتبط',
        'icon' => 'fab fa-facebook',
        'actions' => '
            <form method="post" action="' . route('admin.meta-social.pages.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $smBtnPrimary . '"><i class="fas fa-sync"></i> مزامنة من Meta</button>
            </form>
            <a href="' . route('admin.meta-social.oauth.redirect') . '" class="' . $smBtnMeta . '"><i class="fab fa-facebook"></i> إعادة الربط</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($smSectionClass); ?> overflow-x-auto">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="font-bold text-slate-900">قائمة الصفحات</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-right p-3 font-bold text-slate-600">الصفحة</th>
                    <th class="text-right p-3 font-bold text-slate-600">Instagram</th>
                    <th class="text-right p-3 font-bold text-slate-600">الحالة</th>
                    <th class="text-right p-3 font-bold text-slate-600">Webhook</th>
                    <th class="text-right p-3 font-bold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <?php if($page->picture_url): ?>
                                    <img src="<?php echo e($page->picture_url); ?>" class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center"><i class="fab fa-facebook text-sky-500"></i></div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-bold text-slate-900"><?php echo e($page->page_name); ?></p>
                                    <p class="text-xs text-slate-500"><?php echo e($page->category ?? '—'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">
                            <?php if($page->instagram_username): ?>
                                <span class="inline-flex items-center gap-1 text-pink-700 font-semibold"><i class="fab fa-instagram text-xs"></i> {{ $page->instagram_username }}</span>
                            <?php else: ?>
                                <span class="text-slate-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php if($page->is_active): ?>
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">نشطة</span>
                            <?php else: ?>
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold">موقوفة</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-xs text-slate-500">
                            <?php echo e($page->webhook_subscribed_at?->diffForHumans() ?? '—'); ?>

                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1.5">
                                <?php if($page->is_active): ?>
                                    <form method="post" action="<?php echo e(route('admin.meta-social.pages.deactivate', $page)); ?>"><?php echo csrf_field(); ?>
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-50 font-semibold">إيقاف</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="<?php echo e(route('admin.meta-social.pages.activate', $page)); ?>"><?php echo csrf_field(); ?>
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">تفعيل</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="<?php echo e(route('admin.meta-social.pages.sync-conversations', $page)); ?>"><?php echo csrf_field(); ?>
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-sky-200 text-sky-700 hover:bg-sky-50 font-semibold">مزامنة محادثات</button>
                                </form>
                                <a href="<?php echo e(route('admin.meta-social.inbox.index', ['page' => $page->id])); ?>" class="text-xs px-2.5 py-1.5 rounded-lg bg-sky-600 text-white inline-flex items-center font-semibold">Inbox</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-10 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fab fa-facebook text-xl text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-500">لا توجد صفحات — اربط Meta ثم اضغط «مزامنة من Meta»</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($pages->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-100"><?php echo e($pages->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/meta-social/pages/index.blade.php ENDPATH**/ ?>