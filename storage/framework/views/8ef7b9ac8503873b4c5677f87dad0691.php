<?php $__env->startSection('title', 'بنوك الأسئلة'); ?>
<?php $__env->startSection('header', 'بنوك الأسئلة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">بنوك الأسئلة</h1>
                <p class="text-sm text-slate-500 mt-0.5">إدارة بنوك الأسئلة والأسئلة</p>
            </div>
            <a href="<?php echo e(route('instructor.question-banks.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span>إنشاء بنك أسئلة</span>
            </a>
        </div>
    </div>

    <?php if($questionBanks->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $questionBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <h3 class="text-lg font-bold text-slate-800 flex-1"><?php echo e($bank->title); ?></h3>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($bank->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>">
                            <?php echo e($bank->is_active ? 'نشط' : 'معطل'); ?>

                        </span>
                    </div>
                    <?php if($bank->description): ?>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2"><?php echo e($bank->description); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
                        <span class="font-semibold text-slate-700"><?php echo e($bank->questions_count); ?></span> سؤال
                        <?php if($bank->difficulty): ?>
                            <span>• <?php echo e($bank->difficulty === 'easy' ? 'سهل' : ($bank->difficulty === 'medium' ? 'متوسط' : 'صعب')); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('instructor.question-banks.show', $bank)); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <a href="<?php echo e(route('instructor.question-banks.edit', $bank)); ?>" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm"><?php echo e($questionBanks->links()); ?></div>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد بنوك أسئلة</h3>
            <p class="text-sm text-slate-500 mb-4 max-w-sm mx-auto">ابدأ بإنشاء بنك أسئلة لإضافة الأسئلة</p>
            <a href="<?php echo e(route('instructor.question-banks.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> إنشاء بنك أسئلة
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/question-banks/index.blade.php ENDPATH**/ ?>