

<?php $__env->startSection('title', __('admin.community_competitions')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
        المسابقات
    </h1>
    <p class="text-slate-600 mb-8">
        شارك في مسابقات البرمجة والذكاء الاصطناعي، تنافس مع المجتمع، وارتقِ في لوحة المتصدرين.
    </p>

    <?php if($competitions->isNotEmpty()): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $competitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center mb-4">
                        <i class="fas fa-trophy text-xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mb-2"><?php echo e($competition->title); ?></h3>
                    <?php if($competition->description): ?>
                        <p class="text-slate-600 text-sm mb-4 line-clamp-3"><?php echo e(Str::limit($competition->description, 120)); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                        <?php if($competition->start_at): ?>
                            <span>بداية: <?php echo e($competition->start_at->translatedFormat('Y-m-d')); ?></span>
                        <?php endif; ?>
                        <?php if($competition->end_at): ?>
                            <span>نهاية: <?php echo e($competition->end_at->translatedFormat('Y-m-d')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($competitions->hasPages()): ?>
            <div class="mt-8"><?php echo e($competitions->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm text-center">
            <div class="w-20 h-20 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-trophy text-4xl"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-3">المسابقات قادمة قريباً</h2>
            <p class="text-slate-600 max-w-xl mx-auto mb-6">
                نعمل على إطلاق مسابقات في تحليل البيانات، التعلم الآلي، والذكاء الاصطناعي. ستظهر هنا المسابقات النشطة عند الإطلاق.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">مسابقات تنافسية</span>
                <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">جوائز وترتيب</span>
                <span class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold">لوحة متصدرين</span>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\competitions\index.blade.php ENDPATH**/ ?>