

<?php $__env->startSection('title', 'صفحات Facebook & Instagram'); ?>
<?php $__env->startSection('header', 'إدارة الصفحات'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.meta-social._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.meta-social._page-header', [
        'title' => 'صفحات Meta — متعددة',
        'subtitle' => 'اربط أكثر من حساب Meta وفعّل أي عدد من الصفحات (Facebook + Instagram)',
        'icon' => 'fab fa-facebook',
        'statCards' => [
            ['label' => 'حسابات Meta', 'value' => $connections->count(), 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'صفحات مزامنة', 'value' => $pages->total(), 'icon' => 'fab fa-facebook', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'صفحات نشطة', 'value' => \App\Models\MetaSocialPage::where('is_active', true)->count(), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'Inbox', 'value' => 'موحّد', 'icon' => 'fas fa-inbox', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'description' => 'كل الصفحات النشطة'],
        ],
        'actions' => '
            <a href="' . route('admin.meta-social.oauth.redirect') . '" class="' . $smBtnMeta . '"><i class="fas fa-plus"></i> ربط حساب Meta</a>
            <form method="post" action="' . route('admin.meta-social.pages.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $smBtnPrimary . '"><i class="fas fa-sync"></i> مزامنة الكل</button>
            </form>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($showPicker && $pages->isNotEmpty()): ?>
        <div class="rounded-2xl border-2 border-sky-300 bg-sky-50 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-bold text-sky-900">اختر الصفحات للتفعيل</p>
                <p class="text-sm text-sky-800 mt-0.5">يمكنك تفعيل أكثر من صفحة — Messenger و Instagram لكل صفحة.</p>
            </div>
            <form method="post" action="<?php echo e(route('admin.meta-social.pages.activate-all')); ?>" class="inline"><?php echo csrf_field(); ?>
                <button type="submit" class="<?php echo e($smBtnPrimary); ?>"><i class="fas fa-check-double"></i> تفعيل الكل</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if($connections->isNotEmpty()): ?>
    <section class="<?php echo e($smSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="font-bold text-slate-900">حسابات Meta المربوطة</h3>
        </div>
        <div class="p-4 sm:p-5 flex flex-wrap gap-3">
            <?php $__currentLoopData = $connections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 flex items-center gap-3 min-w-[220px]">
                    <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
                        <i class="fab fa-meta"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-900 truncate"><?php echo e($conn->meta_user_name ?: 'Meta User'); ?></p>
                        <p class="text-[10px] text-slate-500"><?php echo e($conn->connected_at?->diffForHumans()); ?></p>
                    </div>
                    <form method="post" action="<?php echo e(route('admin.meta-social.oauth.disconnect')); ?>" onsubmit="return confirm('قطع هذا الحساب؟')"><?php echo csrf_field(); ?>
                        <input type="hidden" name="connection_id" value="<?php echo e($conn->id); ?>">
                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold">قطع</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.meta-social.oauth.redirect')); ?>" class="rounded-xl border-2 border-dashed border-sky-300 bg-sky-50/50 px-4 py-3 flex items-center gap-2 text-sky-700 hover:bg-sky-50 font-semibold text-sm">
                <i class="fas fa-plus"></i> حساب Meta إضافي
            </a>
        </div>
    </section>
    <?php endif; ?>

    <section class="<?php echo e($smSectionClass); ?> overflow-x-auto" x-data="{ selected: [] }">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-bold text-slate-900">قائمة الصفحات</h3>
            <div class="flex flex-wrap gap-2" x-show="selected.length > 0" x-cloak>
                <form method="post" action="<?php echo e(route('admin.meta-social.pages.bulk-activate')); ?>"><?php echo csrf_field(); ?>
                    <template x-for="id in selected" :key="'a-'+id">
                        <input type="hidden" name="page_ids[]" :value="id">
                    </template>
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold">تفعيل المحدد (<span x-text="selected.length"></span>)</button>
                </form>
                <form method="post" action="<?php echo e(route('admin.meta-social.pages.bulk-deactivate')); ?>"><?php echo csrf_field(); ?>
                    <template x-for="id in selected" :key="'d-'+id">
                        <input type="hidden" name="page_ids[]" :value="id">
                    </template>
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border-2 border-slate-200 font-bold">إيقاف المحدد</button>
                </form>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-3 w-10">
                        <input type="checkbox" class="rounded border-slate-300"
                               @change="selected = $event.target.checked ? <?php echo json_encode($pages->pluck('id'), 15, 512) ?> : []">
                    </th>
                    <th class="text-right p-3 font-bold text-slate-600">الصفحة</th>
                    <th class="text-right p-3 font-bold text-slate-600">حساب Meta</th>
                    <th class="text-right p-3 font-bold text-slate-600">Instagram</th>
                    <th class="text-right p-3 font-bold text-slate-600">الحالة</th>
                    <th class="text-right p-3 font-bold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <input type="checkbox" class="rounded border-slate-300" value="<?php echo e($page->id); ?>"
                                   @change="if($event.target.checked) { if(!selected.includes(<?php echo e($page->id); ?>)) selected.push(<?php echo e($page->id); ?>) } else { selected = selected.filter(id => id !== <?php echo e($page->id); ?>) }"
                                   :checked="selected.includes(<?php echo e($page->id); ?>)">
                        </td>
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
                        <td class="p-3 text-xs text-slate-600"><?php echo e($page->connection?->meta_user_name ?? '—'); ?></td>
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
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold">غير مفعّلة</span>
                            <?php endif; ?>
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
                                <a href="<?php echo e(route('admin.meta-social.inbox.index', ['page' => $page->id])); ?>" class="text-xs px-2.5 py-1.5 rounded-lg bg-sky-600 text-white inline-flex items-center font-semibold">Inbox</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="p-10 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fab fa-facebook text-xl text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-500 mb-3">لا توجد صفحات — اربط Meta ثم مزامنة</p>
                            <a href="<?php echo e(route('admin.meta-social.oauth.redirect')); ?>" class="<?php echo e($smBtnMeta); ?> text-sm inline-flex"><i class="fab fa-facebook"></i> ربط Meta</a>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\meta-social\pages\index.blade.php ENDPATH**/ ?>