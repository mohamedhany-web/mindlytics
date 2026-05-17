

<?php $__env->startSection('title', __('admin.community_competitions')); ?>
<?php $__env->startSection('header', __('admin.community_competitions')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-black text-slate-900">قائمة المسابقات</h2>
        <a href="<?php echo e(route('admin.community.competitions.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة مسابقة</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">العنوان</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">البداية</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">النهاية</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700 w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $competitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($competition->title); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($competition->start_at?->translatedFormat('Y-m-d') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($competition->end_at?->translatedFormat('Y-m-d') ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($competition->is_active): ?>
                                    <span class="px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">نشط</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.community.competitions.edit', $competition)); ?>" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('admin.community.competitions.destroy', $competition)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذه المسابقة؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد مسابقات. <a href="<?php echo e(route('admin.community.competitions.create')); ?>" class="text-cyan-600 font-bold hover:underline">إضافة مسابقة</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($competitions->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-200"><?php echo e($competitions->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/community/competitions/index.blade.php ENDPATH**/ ?>