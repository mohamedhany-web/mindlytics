<?php $__env->startSection('title', $activity->title . ' - نشاط أوفلاين'); ?>
<?php $__env->startSection('header', $activity->title); ?>

<?php $__env->startSection('content'); ?>
<?php $channel = $channel ?? request()->query('channel', 'offline'); ?>
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => $channel])); ?>" class="hover:text-amber-600"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.activities.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])); ?>" class="hover:text-amber-600">الواجبات والاختبارات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($activity->title); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-800"><?php echo e($activity->title); ?></h1>
            <a href="<?php echo e(route('instructor.offline-courses.activities.edit', ['offlineCourse' => $offlineCourse, 'activity' => $activity, 'channel' => $channel])); ?>" class="px-4 py-2 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">تعديل</a>
        </div>
        <?php if($activity->description): ?>
            <p class="text-slate-600 mt-2 whitespace-pre-line"><?php echo e($activity->description); ?></p>
        <?php endif; ?>
        <p class="text-sm text-slate-500 mt-2"><?php echo e($activity->type); ?> | آخر موعد: <?php echo e($activity->due_date ? $activity->due_date->format('Y-m-d') : '—'); ?> | الدرجة العظمى: <?php echo e($activity->max_score); ?></p>
        <?php if($activity->instructions): ?>
            <div class="mt-3 text-sm text-slate-700">
                <span class="font-semibold text-slate-800">تعليمات التسليم:</span>
                <p class="mt-1 whitespace-pre-line"><?php echo e($activity->instructions); ?></p>
            </div>
        <?php endif; ?>
        <?php if($activity->attachments && count($activity->attachments)): ?>
            <div class="mt-4">
                <h3 class="text-sm font-bold text-slate-800 mb-2">مرفقات النشاط للطلاب</h3>
                <ul class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $activity->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a href="<?php echo e(stored_upload_file_url($att)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-800 rounded-lg text-sm hover:bg-slate-200">
                                <i class="fas fa-file-download text-slate-500"></i>
                                <?php echo e($att['name'] ?? 'ملف'); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
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
                                <?php elseif($sub->status === 'submitted'): ?> مقدّم — بانتظار التصحيح
                                <?php else: ?> قيد الانتظار <?php endif; ?>
                            </span>
                        </div>
                        <?php if($sub->submitted_at): ?>
                            <p class="text-sm text-slate-600">تاريخ التقديم: <?php echo e($sub->submitted_at->format('Y-m-d H:i')); ?></p>
                        <?php endif; ?>
                        <?php if($sub->status === 'graded' && $sub->graded_at): ?>
                            <p class="text-sm text-slate-600">تاريخ التصحيح: <?php echo e($sub->graded_at->format('Y-m-d H:i')); ?>

                                <?php if($sub->relationLoaded('grader') && $sub->grader): ?>
                                    — <?php echo e($sub->grader->name); ?>

                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if($sub->submission_content): ?>
                            <div class="mt-2">
                                <p class="text-xs font-semibold text-slate-500 mb-1">نص التقديم</p>
                                <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($sub->submission_content); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($sub->attachments && count($sub->attachments)): ?>
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-slate-500 mb-2">ملفات التسليم</p>
                                <ul class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $sub->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a href="<?php echo e(offline_activity_submission_file_url($f)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 text-sky-800 rounded-lg text-sm hover:bg-sky-100">
                                                <i class="fas fa-download"></i>
                                                <?php echo e($f['name'] ?? 'ملف'); ?>

                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if($sub->status === 'submitted' || $sub->status === 'graded'): ?>
                            <form action="<?php echo e(route('instructor.offline-courses.activities.submissions.grade', ['offlineCourse' => $offlineCourse, 'activity' => $activity, 'submission' => $sub, 'channel' => $channel])); ?>" method="post" class="mt-4 space-y-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3">
                                <?php echo csrf_field(); ?>
                                <div class="flex flex-wrap items-end gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">الدرجة (من <?php echo e($activity->max_score); ?>)</label>
                                        <input type="number" name="score" value="<?php echo e(old('score', $sub->score)); ?>" min="0" max="<?php echo e($activity->max_score); ?>" step="0.5" required class="w-28 rounded-lg border border-slate-200 px-2 py-1.5">
                                    </div>
                                    <div class="flex-1 min-w-[220px]">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات للطالب</label>
                                        <textarea name="feedback" rows="2" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-sm" placeholder="تعليق المدرب (يظهر للطالب بعد التصحيح)"><?php echo e(old('feedback', $sub->feedback)); ?></textarea>
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700 shrink-0">
                                        <?php echo e($sub->status === 'graded' ? 'تحديث التصحيح' : 'تسجيل التصحيح'); ?>

                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\offline-courses\activities\show.blade.php ENDPATH**/ ?>