

<?php $__env->startSection('title', 'تقديمات النماذج — مراجعة ونشر'); ?>
<?php $__env->startSection('header', 'تقديمات مكتبة النماذج (Model Zoo)'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-amber-50/80">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-brain text-amber-600"></i>
                النماذج المعلقة (من المساهمين)
            </h2>
            <p class="text-sm text-slate-600 mt-1">مراجعة النماذج والموافقة عليها للنشر في مكتبة النماذج أو الرفض.</p>
        </div>
        <div class="p-6">
            <?php if($pendingModels->isEmpty()): ?>
                <div class="text-center py-12 text-slate-500">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-3xl"></i>
                    </div>
                    <p>لا توجد تقديمات نماذج معلقة حالياً.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $pendingModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900"><?php echo e($m->title); ?></h3>
                                <?php if($m->description): ?>
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2"><?php echo e(Str::limit($m->description, 120)); ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-500 mt-2">
                                    من: <?php echo e($m->creator->name ?? '—'); ?> (<?php echo e($m->creator->email ?? '—'); ?>) — <?php echo e($m->created_at->diffForHumans()); ?>

                                </p>
                                <?php if($m->dataset): ?>
                                    <p class="text-xs text-cyan-600 mt-1"><i class="fas fa-database ml-1"></i> مرتبط بداتاسيت: <?php echo e(Str::limit($m->dataset->title, 40)); ?></p>
                                <?php endif; ?>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <a href="<?php echo e(route('admin.community.submissions.model.show', $m)); ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-700">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض النموذج والمنهجية</span>
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="<?php echo e(route('admin.community.submissions.model.approve', $m)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check ml-1"></i> موافقة ونشر
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.community.submissions.model.reject', $m)); ?>" method="POST" class="inline" onsubmit="return confirm('رفض هذا النموذج؟');">
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

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('admin.community.submissions.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200">
            <i class="fas fa-database"></i>
            <span>تقديمات البيانات</span>
        </a>
        <a href="<?php echo e(route('admin.community.dashboard')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-100 text-cyan-800 font-bold hover:bg-cyan-200">
            <i class="fas fa-home"></i>
            <span>لوحة المجتمع</span>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/community/submissions-models.blade.php ENDPATH**/ ?>