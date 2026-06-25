

<?php $__env->startSection('title', __('admin.community_datasets')); ?>
<?php $__env->startSection('header', __('admin.community_datasets')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-black text-slate-900">قائمة مجموعات البيانات</h2>
        <a href="<?php echo e(route('admin.community.datasets.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة مجموعة بيانات</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">العنوان</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الرابط / الملف</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحجم</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700 w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($dataset->title); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?php if($dataset->file_url): ?>
                                    <a href="<?php echo e($dataset->file_url); ?>" target="_blank" rel="noopener" class="text-cyan-600 hover:underline truncate block max-w-xs">رابط</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($dataset->file_size ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($dataset->is_active): ?>
                                    <span class="px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">نشط</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.community.datasets.edit', $dataset)); ?>" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('admin.community.datasets.destroy', $dataset)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف مجموعة البيانات هذه؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد مجموعات بيانات. <a href="<?php echo e(route('admin.community.datasets.create')); ?>" class="text-cyan-600 font-bold hover:underline">إضافة مجموعة بيانات</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($datasets->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200"><?php echo e($datasets->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/community/datasets/index.blade.php ENDPATH**/ ?>