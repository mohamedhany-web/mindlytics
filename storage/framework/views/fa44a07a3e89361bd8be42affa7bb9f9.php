

<?php $__env->startSection('title', 'تقديماتي'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-black text-slate-900">تقديماتي</h1>
        <a href="<?php echo e(route('community.contributor.datasets.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md">
            <i class="fas fa-plus"></i>
            <span>تقديم مجموعة بيانات جديدة</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if($datasets->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>لا توجد تقديمات حتى الآن.</p>
                <a href="<?php echo e(route('community.contributor.datasets.create')); ?>" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700">تقديم مجموعة بيانات</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">العنوان</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">التاريخ</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">الحالة</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-medium text-slate-900"><?php echo e($d->title); ?></td>
                            <td class="py-3 px-4 text-slate-600 text-sm"><?php echo e($d->created_at->format('Y-m-d')); ?></td>
                            <td class="py-3 px-4">
                                <?php if($d->status === 'pending'): ?>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد المراجعة</span>
                                <?php elseif($d->status === 'approved'): ?>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">معتمدة</span>
                                <?php else: ?>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">مرفوضة</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if($d->status === 'approved' && $d->is_active): ?>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo e(urlencode(url(route('community.data.show', $d)))); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#0A66C2] text-white text-sm font-bold hover:bg-[#004182] transition-colors" title="مشاركة تجربتك على LinkedIn — ساهمت في مجتمع البيانات والذكاء الاصطناعي">
                                        <i class="fab fa-linkedin text-base"></i>
                                        <span>مشاركة على LinkedIn</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200">
                <?php echo e($datasets->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/contributor/datasets/index.blade.php ENDPATH**/ ?>