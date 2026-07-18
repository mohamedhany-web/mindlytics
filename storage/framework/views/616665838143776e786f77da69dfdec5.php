

<?php $__env->startSection('title', 'تفاصيل المشاركة'); ?>
<?php $__env->startSection('header', 'تفاصيل المشاركة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
    <a href="<?php echo e(route('instructor.learn-discussions.index')); ?>" class="text-sm font-semibold text-teal-700 hover:underline">← رجوع للقائمة</a>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <article class="bg-white border border-slate-200 rounded-xl p-5 space-y-3">
        <div class="flex flex-wrap gap-2 items-center text-xs text-slate-500">
            <span class="font-bold px-2 py-0.5 rounded-full <?php echo e($discussion->kind === 'qa' ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800'); ?>">
                <?php echo e($discussion->kind === 'qa' ? 'سؤال للطالب' : 'نقاش'); ?>

            </span>
            <span><?php echo e($discussion->course?->title); ?></span>
            <span>·</span>
            <span><?php echo e($contextTitle); ?></span>
        </div>
        <h1 class="text-base font-bold text-slate-900"><?php echo e($discussion->user?->name); ?></h1>
        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo e($discussion->body); ?></p>
        <time class="text-xs text-slate-400"><?php echo e($discussion->created_at?->format('Y/m/d H:i')); ?></time>
    </article>

    <section class="space-y-3">
        <h2 class="text-sm font-bold text-slate-800">الردود (<?php echo e($discussion->replies->count()); ?>)</h2>
        <?php $__empty_1 = true; $__currentLoopData = $discussion->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="rounded-xl border p-4 <?php echo e($reply->isInstructorAuthor() ? 'border-teal-200 bg-teal-50/40' : 'border-slate-200 bg-white'); ?>">
                <div class="flex items-center gap-2 text-xs mb-1">
                    <strong class="text-slate-900 text-sm"><?php echo e($reply->user?->name); ?></strong>
                    <?php if($reply->isInstructorAuthor()): ?>
                        <span class="text-teal-700 font-bold">مدرب</span>
                    <?php endif; ?>
                    <span class="text-slate-400 ms-auto"><?php echo e($reply->created_at?->diffForHumans()); ?></span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($reply->body); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500">لا توجد ردود بعد.</p>
        <?php endif; ?>
    </section>

    <form method="post" action="<?php echo e(route('instructor.learn-discussions.reply', $discussion)); ?>" class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
        <?php echo csrf_field(); ?>
        <label class="block text-sm font-bold text-slate-800">ردّك للطالب</label>
        <textarea name="body" rows="4" required minlength="2" maxlength="5000"
                  class="w-full rounded-lg border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500"
                  placeholder="اكتب ردك هنا…"><?php echo e(old('body')); ?></textarea>
        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-xs text-red-600"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <button type="submit" class="rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5">إرسال الرد</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\learn-discussions\show.blade.php ENDPATH**/ ?>