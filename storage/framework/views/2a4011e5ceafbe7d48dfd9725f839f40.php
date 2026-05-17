

<?php $__env->startSection('title', 'نماذجي'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-black text-slate-900">نماذجي (Model Zoo)</h1>
        <a href="<?php echo e(route('community.contributor.models.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shadow-md">
            <i class="fas fa-plus"></i>
            <span>إضافة نموذج جديد</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <?php if($models->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-brain text-3xl"></i>
                </div>
                <p>لا توجد نماذج مرفوعة حتى الآن.</p>
                <p class="text-sm mt-1">ارفع نموذجك المدرب مع شرح المنهجية وربطه بمجموعة بيانات (إن وجدت). التخزين على Cloudflare.</p>
                <a href="<?php echo e(route('community.contributor.models.create')); ?>" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700">إضافة نموذج</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">العنوان</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">الداتاسيت</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">التاريخ</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-medium text-slate-900"><?php echo e($m->title); ?></td>
                            <td class="py-3 px-4 text-slate-600 text-sm">
                                <?php if($m->dataset): ?>
                                    <?php echo e(Str::limit($m->dataset->title, 25)); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-sm"><?php echo e($m->created_at->format('Y-m-d')); ?></td>
                            <td class="py-3 px-4">
                                <?php if($m->status === \App\Models\CommunityModel::STATUS_PENDING): ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد المراجعة</span>
                                <?php elseif($m->status === \App\Models\CommunityModel::STATUS_APPROVED): ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">معتمد</span>
                                    <a href="<?php echo e(route('community.models.show', $m)); ?>" class="mr-2 text-cyan-600 text-xs font-bold hover:underline">عرض</a>
                                <?php else: ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">مرفوض</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200">
                <?php echo e($models->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/contributor/models/index.blade.php ENDPATH**/ ?>