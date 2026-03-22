

<?php $__env->startSection('title', 'الاجتماعات والورش'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-people-arrows text-blue-600"></i>
                <span>الاجتماعات / الورش</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                إدارة ورش العمل وصفحات الحجز، ومتابعة الطلاب المسجلين وتحميل بياناتهم.
            </p>
        </div>
        <a href="<?php echo e(route('admin.workshops.create')); ?>"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-plus"></i>
            <span>إنشاء ورشة جديدة</span>
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">العنوان</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التاريخ</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">المقاعد</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التحكم</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $total = $ws->max_seats ?: null;
                        $registered = $ws->registrations()->count();
                        $remaining = $ws->remaining_seats;
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-500"><?php echo e($ws->id); ?></td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-semibold text-slate-900"><?php echo e($ws->title); ?></div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                رابط الحجز:
                                <a href="<?php echo e(route('public.workshops.show', $ws->slug)); ?>" target="_blank" class="text-blue-600 hover:underline">
                                    <?php echo e(route('public.workshops.show', $ws->slug)); ?>

                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            <?php if($ws->starts_at): ?>
                                <div><?php echo e($ws->starts_at->format('Y-m-d H:i')); ?></div>
                                <?php if($ws->ends_at): ?>
                                    <div class="text-xs text-slate-500">حتى <?php echo e($ws->ends_at->format('Y-m-d H:i')); ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">غير محدد</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            <?php if($total): ?>
                                <div class="font-semibold"><?php echo e($registered); ?> / <?php echo e($total); ?></div>
                                <div class="text-xs text-slate-500">
                                    متبقي: <?php echo e($remaining); ?>

                                </div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">غير محدود</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if($ws->is_active): ?>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    نشطة
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    غير نشطة
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.workshops.show', $ws)); ?>" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </a>
                                <a href="<?php echo e(route('admin.workshops.edit', $ws)); ?>" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs font-semibold">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                                <form action="<?php echo e(route('admin.workshops.destroy', $ws)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الورشة وجميع الحجوزات المرتبطة بها؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                        <i class="fas fa-trash"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 text-sm">
                            لا توجد ورش عمل حالياً. يمكنك إنشاء ورشة جديدة من الزر أعلى الصفحة.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            <?php echo e($workshops->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\index.blade.php ENDPATH**/ ?>