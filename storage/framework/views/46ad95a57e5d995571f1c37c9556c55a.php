

<?php $__env->startSection('title', 'واجباتي'); ?>
<?php $__env->startSection('header', 'واجباتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 mb-5">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-slate-800">الواجبات المتاحة</h2>
            <span class="text-sm text-slate-500"><?php echo e($assignments->count()); ?> واجب</span>
        </div>
    </div>

    <?php if($assignments->count()): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $sub = $submissions->get($assignment->id); ?>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5 h-full flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-lg font-bold text-slate-800 line-clamp-2"><?php echo e($assignment->title); ?></h3>
                        <?php if($sub): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 shrink-0">تم التسليم</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700 shrink-0">بانتظار التسليم</span>
                        <?php endif; ?>
                    </div>

                    <?php if($assignment->description): ?>
                        <p class="text-sm text-slate-600 mb-3 line-clamp-3"><?php echo e($assignment->description); ?></p>
                    <?php endif; ?>

                    <div class="space-y-1.5 text-sm text-slate-500 mb-4">
                        <div><i class="fas fa-book text-sky-500 ml-1"></i> <span class="font-semibold text-slate-700">الكورس:</span> <?php echo e($assignment->course->title ?? '—'); ?></div>
                        <?php if($assignment->lesson): ?>
                            <div><i class="fas fa-list-alt text-emerald-500 ml-1"></i> <span class="font-semibold text-slate-700">الدرس:</span> <?php echo e($assignment->lesson->title); ?></div>
                        <?php endif; ?>
                        <?php if($assignment->due_date): ?>
                            <div><i class="fas fa-calendar text-slate-400 ml-1"></i> <span class="font-semibold text-slate-700">آخر موعد:</span> <?php echo e($assignment->due_date->format('Y-m-d H:i')); ?></div>
                        <?php endif; ?>
                        <div><i class="fas fa-star text-amber-500 ml-1"></i> <span class="font-semibold text-slate-700">الدرجة:</span> <?php echo e($assignment->max_score); ?></div>
                    </div>

                    <div class="mt-auto">
                        <a href="<?php echo e(route('student.assignments.show', $assignment)); ?>" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-sm font-semibold">
                            <i class="fas fa-eye"></i>
                            عرض الواجب
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tasks text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد واجبات متاحة حالياً</h3>
            <p class="text-sm text-slate-500">ستظهر هنا الواجبات عند نشرها من المدرب</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\assignments\index.blade.php ENDPATH**/ ?>