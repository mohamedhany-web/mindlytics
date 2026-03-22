

<?php $__env->startSection('title', 'تقديمات المجتمع - مراجعة ونشر'); ?>
<?php $__env->startSection('header', 'تقديمات المساهمين (مراجعة والموافقة)'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-800">مجموعات البيانات المعلقة (من المساهمين)</h2>
            <p class="text-sm text-slate-600 mt-1">مراجعة البيانات والموافقة عليها للنشر أو الرفض.</p>
        </div>
        <div class="p-6">
            <?php if($pendingDatasets->isEmpty()): ?>
                <div class="text-center py-12 text-slate-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>لا توجد تقديمات معلقة حالياً.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $pendingDatasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900"><?php echo e($dataset->title); ?></h3>
                                <?php if($dataset->description): ?>
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2"><?php echo e(Str::limit($dataset->description, 120)); ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-500 mt-2">
                                    من: <?php echo e($dataset->creator->name ?? '—'); ?> (<?php echo e($dataset->creator->email ?? '—'); ?>) — <?php echo e($dataset->created_at->diffForHumans()); ?>

                                </p>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <a href="<?php echo e(route('admin.community.submissions.dataset.show', $dataset)); ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-cyan-600 hover:text-cyan-700">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض البيانات</span>
                                    </a>
                                    <?php if($dataset->file_path): ?>
                                        <a href="<?php echo e(route('admin.community.submissions.dataset.download', $dataset)); ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-800">
                                            <i class="fas fa-download"></i>
                                            <span>تحميل الملف</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($dataset->file_url): ?>
                                        <a href="<?php echo e($dataset->file_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>رابط التحميل</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="<?php echo e(route('admin.community.submissions.dataset.approve', $dataset)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check ml-1"></i> موافقة ونشر
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.community.submissions.dataset.reject', $dataset)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times ml-1"></i> رفض
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\community\submissions.blade.php ENDPATH**/ ?>