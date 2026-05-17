<?php $__env->startSection('title', $activity->title . ' - نشاط أوفلاين'); ?>
<?php $__env->startSection('header', $activity->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $maxScore = (int) $activity->max_score;
?>
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse)); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900"><?php echo e($activity->title); ?></h1>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo e($activity->type); ?>

                | الدرجة العظمى: <?php echo e($activity->max_score); ?>

                <?php if($activity->due_date): ?>| آخر موعد: <?php echo e($activity->due_date->format('Y-m-d')); ?><?php endif; ?>
            </p>
        </div>
        <div class="p-5 sm:p-6 space-y-6">
            <?php if($activity->description): ?>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">الوصف</h3>
                    <p class="text-gray-700 whitespace-pre-line"><?php echo e($activity->description); ?></p>
                </div>
            <?php endif; ?>
            <?php if($activity->instructions): ?>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">تعليمات التسليم</h3>
                    <p class="text-gray-700 whitespace-pre-line"><?php echo e($activity->instructions); ?></p>
                </div>
            <?php endif; ?>
            <?php if($activity->attachments && count($activity->attachments) > 0): ?>
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">مرفقات من المدرب</h3>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $activity->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(stored_upload_file_url($att)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sky-600 hover:underline font-medium">
                                    <i class="fas fa-paperclip text-slate-400"></i>
                                    <?php echo e($att['name'] ?? 'ملف'); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if($submission): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 sm:p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-800">تسليمك</h3>
                        <?php if($submission->submitted_at): ?>
                            <span class="text-xs text-slate-500">آخر تحديث: <?php echo e($submission->submitted_at->format('Y-m-d H:i')); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($submission->status === 'submitted'): ?>
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">تسليمك قيد المراجعة من المدرب. ستظهر الدرجة والملاحظات هنا بعد التصحيح.</p>
                    <?php endif; ?>
                    <?php if($submission->submission_content): ?>
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-1">نص التقديم</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm"><?php echo e($submission->submission_content); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($submission->attachments && count($submission->attachments)): ?>
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-2">ملفاتك المرفوعة</h4>
                            <ul class="space-y-2">
                                <?php $__currentLoopData = $submission->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(offline_activity_submission_file_url($f)); ?>" target="_blank" rel="noopener" download="<?php echo e($f['name'] ?? 'download'); ?>" class="inline-flex items-center gap-2 text-sky-600 hover:underline text-sm font-medium">
                                            <i class="fas fa-download text-slate-400"></i>
                                            <?php echo e($f['name'] ?? 'ملف'); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($submission && $submission->status === 'graded'): ?>
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 space-y-3">
                    <h3 class="font-bold text-emerald-900">نتيجة التصحيح</h3>
                    <div class="flex flex-wrap items-baseline gap-3">
                        <p class="text-2xl font-bold text-emerald-800"><?php echo e($submission->score); ?></p>
                        <span class="text-emerald-700 font-medium">/ <?php echo e($activity->max_score); ?></span>
                        <?php if($maxScore > 0): ?>
                            <?php $pct = round((float) $submission->score / $maxScore * 100, 1); ?>
                            <span class="text-sm text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-lg">نسبة: <?php echo e($pct); ?>%</span>
                        <?php endif; ?>
                    </div>
                    <?php if($submission->graded_at): ?>
                        <p class="text-xs text-emerald-800/90">تاريخ التصحيح: <?php echo e($submission->graded_at->format('Y-m-d H:i')); ?></p>
                    <?php endif; ?>
                    <?php if($submission->relationLoaded('grader') && $submission->grader): ?>
                        <p class="text-xs text-emerald-800/90">المدرب: <?php echo e($submission->grader->name); ?></p>
                    <?php endif; ?>
                    <?php if($submission->feedback): ?>
                        <div>
                            <h4 class="text-xs font-semibold text-emerald-900 mb-1">ملاحظات المدرب</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm leading-relaxed"><?php echo e($submission->feedback); ?></p>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-emerald-800/80">لم يُضف ملاحظة نصية مع هذه الدرجة.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($activity->status !== 'published'): ?>
                <p class="text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">هذا النشاط غير متاح للتسليم حالياً.</p>
            <?php elseif(!$submission || $submission->status !== 'graded'): ?>
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-3"><?php if($submission && $submission->status === 'submitted'): ?>تحديث التسليم<?php else: ?>تسليم النشاط<?php endif; ?></h3>
                    <form action="<?php echo e(route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.submit', [$offlineCourse, $activity])); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">نص التقديم (اختياري)</label>
                            <textarea name="submission_content" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500" placeholder="اكتب إجابتك أو وصف التقديم هنا..."><?php echo e(old('submission_content', $submission->submission_content ?? '')); ?></textarea>
                            <?php $__errorArgs = ['submission_content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">مرفقات (اختياري)</label>
                            <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                            <p class="text-xs text-gray-500 mt-1">يمكنك رفع أكثر من ملف (حتى 20 ميجابايت لكل ملف). الملفات تُخزَّن على خوادم المنصة بشكل آمن.</p>
                            <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">
                            <?php if($submission && $submission->status === 'submitted'): ?>
                                تحديث التقديم
                            <?php else: ?>
                                تسليم النشاط
                            <?php endif; ?>
                        </button>
                    </form>
                </div>
            <?php elseif($submission && $submission->status === 'graded'): ?>
                <p class="text-sm text-slate-600 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50">تم تثبيت درجتك بعد التصحيح. لا يمكن تعديل التسليم من هذه الصفحة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/offline-courses/activity-show.blade.php ENDPATH**/ ?>