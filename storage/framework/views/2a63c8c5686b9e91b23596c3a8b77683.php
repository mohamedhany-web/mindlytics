
<?php $__env->startSection('title', 'المدربون - Mindlytics'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">مدربونا</h1>
        <p class="text-slate-600">تعرف على فريق المدربين والخبراء.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="group rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg overflow-hidden">
            <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative flex items-center justify-center">
                <?php if($p->photo_path): ?>
                    <img src="<?php echo e($p->photo_url); ?>" alt="<?php echo e($p->user->name); ?>" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 w-full h-full flex items-center justify-center text-slate-400 bg-slate-100"><i class="fas fa-user text-6xl"></i></div>
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-user text-6xl"></i></div>
                <?php endif; ?>
            </div>
            <div class="p-5">
                <h2 class="text-lg font-bold text-slate-900"><?php echo e($p->user->name); ?></h2>
                <p class="text-sm text-slate-600 mt-1"><?php echo e($p->headline ?? 'مدرب'); ?></p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-12 text-slate-500">لا يوجد مدربون معروضون حالياً.</div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructors/index.blade.php ENDPATH**/ ?>