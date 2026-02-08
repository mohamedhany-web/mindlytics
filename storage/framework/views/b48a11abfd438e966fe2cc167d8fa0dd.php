<?php $__env->startSection('title', $activity->title . ' - نشاط أوفلاين'); ?>
<?php $__env->startSection('header', $activity->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.activities.index', $offlineCourse)); ?>" class="hover:text-amber-600">الواجبات والاختبارات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($activity->title); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-800"><?php echo e($activity->title); ?></h1>
            <a href="<?php echo e(route('instructor.offline-courses.activities.edit', [$offlineCourse, $activity])); ?>" class="px-4 py-2 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">تعديل</a>
        </div>
        <?php if($activity->description): ?>
            <p class="text-slate-600 mt-2"><?php echo e($activity->description); ?></p>
        <?php endif; ?>
        <p class="text-sm text-slate-500 mt-2"><?php echo e($activity->type); ?> | آخر موعد: <?php echo e($activity->due_date ? $activity->due_date->format('Y-m-d') : '—'); ?> | الدرجة العظمى: <?php echo e($activity->max_score); ?></p>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800">تقديمات الطلاب (<?php echo e($activity->submissions->count()); ?>)</h2>
        </div>
        <?php if($activity->submissions->isEmpty()): ?>
            <div class="p-12 text-center text-slate-500">لا توجد تقديمات بعد.</div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $activity->submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="font-semibold text-slate-800"><?php echo e($sub->student->name ?? 'طالب'); ?></span>
                            <span class="text-xs px-2 py-1 rounded
                                <?php if($sub->status === 'graded'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($sub->status === 'submitted'): ?> bg-amber-100 text-amber-700
                                <?php else: ?> bg-slate-100 text-slate-600 <?php endif; ?>">
                                <?php if($sub->status === 'graded'): ?> مصحح (<?php echo e($sub->score); ?>/<?php echo e($activity->max_score); ?>)
                                <?php elseif($sub->status === 'submitted'): ?> مقدم
                                <?php else: ?> قيد الانتظار <?php endif; ?>
                            </span>
                        </div>
                        <?php if($sub->submitted_at): ?>
                            <p class="text-sm text-slate-600">تاريخ التقديم: <?php echo e($sub->submitted_at->format('Y-m-d H:i')); ?></p>
                        <?php endif; ?>
                        <?php if($sub->submission_content): ?>
                            <p class="text-sm text-slate-700 mt-2 whitespace-pre-line"><?php echo e(Str::limit($sub->submission_content, 300)); ?></p>
                        <?php endif; ?>
                        <?php if($sub->attachments && count($sub->attachments)): ?>
                            <p class="text-xs text-slate-500 mt-1">مرفقات: <?php echo e(count($sub->attachments)); ?> ملف</p>
                        <?php endif; ?>
                        <?php if($sub->status === 'submitted' || $sub->status === 'graded'): ?>
                            <form action="<?php echo e(route('instructor.offline-courses.activities.submissions.grade', [$offlineCourse, $activity, $sub])); ?>" method="post" class="mt-3 flex flex-wrap items-end gap-2">
                                <?php echo csrf_field(); ?>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600">الدرجة</label>
                                    <input type="number" name="score" value="<?php echo e(old('score', $sub->score)); ?>" min="0" max="<?php echo e($activity->max_score); ?>" step="0.5" class="w-24 rounded-lg border border-slate-200 px-2 py-1.5">
                                </div>
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-xs font-semibold text-slate-600">ملاحظات</label>
                                    <input type="text" name="feedback" value="<?php echo e(old('feedback', $sub->feedback)); ?>" class="w-full rounded-lg border border-slate-200 px-2 py-1.5" placeholder="ملاحظات للطالب">
                                </div>
                                <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">تصحيح</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/activities/show.blade.php ENDPATH**/ ?>