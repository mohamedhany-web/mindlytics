

<?php $__env->startSection('title', 'نقاش وأسئلة الطلاب'); ?>
<?php $__env->startSection('header', 'نقاش وأسئلة الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">نقاش وأسئلة الطلاب</h1>
            <p class="text-sm text-slate-500 mt-1">اطّلع على ما يكتبه طلابك في صفحة التعلّم وردّ عليهم.</p>
        </div>
        <?php if(($unreadQa ?? 0) > 0): ?>
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1.5 text-xs font-bold">
                <?php echo e($unreadQa); ?> سؤال بانتظار ردك
            </span>
        <?php endif; ?>
    </div>

    <form method="get" class="flex flex-wrap gap-2 items-center bg-white border border-slate-200 rounded-xl p-3">
        <select name="kind" class="rounded-lg border-slate-200 text-sm">
            <option value="">الكل</option>
            <option value="qa" <?php if(($kind ?? '') === 'qa'): echo 'selected'; endif; ?>>أسئلة وأجوبة</option>
            <option value="discussion" <?php if(($kind ?? '') === 'discussion'): echo 'selected'; endif; ?>>نقاش</option>
        </select>
        <select name="course_id" class="rounded-lg border-slate-200 text-sm min-w-[180px]">
            <option value="">كل المقررات</option>
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php if((string) ($courseFilter ?? '') === (string) $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2">تصفية</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100">
        <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('instructor.learn-discussions.show', $thread)); ?>"
               class="block p-4 hover:bg-slate-50 transition-colors">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full <?php echo e($thread->kind === 'qa' ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800'); ?>">
                        <?php echo e($thread->kind === 'qa' ? 'سؤال' : 'نقاش'); ?>

                    </span>
                    <span class="text-xs text-slate-500"><?php echo e($thread->course?->title); ?></span>
                    <span class="text-xs text-slate-400 ms-auto"><?php echo e($thread->created_at?->diffForHumans()); ?></span>
                </div>
                <p class="text-sm font-semibold text-slate-900 line-clamp-2"><?php echo e($thread->body); ?></p>
                <p class="text-xs text-slate-500 mt-1">
                    <?php echo e($thread->user?->name); ?>

                    · <?php echo e($thread->replies_count); ?> رد
                </p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-10 text-center text-slate-500 text-sm">لا توجد مشاركات بعد من الطلاب.</div>
        <?php endif; ?>
    </div>

    <div class="flex justify-center"><?php echo e($threads->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\learn-discussions\index.blade.php ENDPATH**/ ?>