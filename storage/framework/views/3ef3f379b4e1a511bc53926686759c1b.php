<?php $__env->startSection('title', $activity->title . ' - نشاط أوفلاين'); ?>
<?php $__env->startSection('header', $activity->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route('student.offline-courses.show', $offlineCourse)); ?>" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900"><?php echo e($activity->title); ?></h1>
            <p class="text-sm text-gray-600 mt-1"><?php echo e($activity->type); ?> | الدرجة العظمى: <?php echo e($activity->max_score); ?> <?php if($activity->due_date): ?>| آخر موعد: <?php echo e($activity->due_date->format('Y-m-d')); ?><?php endif; ?></p>
        </div>
        <div class="p-5 sm:p-6">
            <?php if($activity->description): ?>
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">الوصف</h3>
                    <p class="text-gray-700 whitespace-pre-line"><?php echo e($activity->description); ?></p>
                </div>
            <?php endif; ?>
            <?php if($activity->instructions): ?>
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">تعليمات التسليم</h3>
                    <p class="text-gray-700 whitespace-pre-line"><?php echo e($activity->instructions); ?></p>
                </div>
            <?php endif; ?>
            <?php if($activity->attachments && count($activity->attachments) > 0): ?>
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-700 mb-2">مرفقات</h3>
                    <ul class="space-y-1">
                        <?php $__currentLoopData = $activity->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e(asset('storage/' . ($att['path'] ?? ''))); ?>" target="_blank" class="text-sky-600 hover:underline"><?php echo e($att['name'] ?? 'ملف'); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if($submission && $submission->status === 'graded'): ?>
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 mb-4">
                    <h3 class="font-semibold text-emerald-800 mb-1">تم التصحيح</h3>
                    <p class="text-emerald-700">الدرجة: <?php echo e($submission->score); ?>/<?php echo e($activity->max_score); ?></p>
                    <?php if($submission->feedback): ?>
                        <p class="text-gray-700 mt-2"><?php echo e($submission->feedback); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($activity->status !== 'published'): ?>
                <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">هذا النشاط غير متاح للتسليم حالياً.</p>
            <?php elseif(!$submission || $submission->status !== 'graded'): ?>
                <form action="<?php echo e(route('student.offline-courses.activities.submit', [$offlineCourse, $activity])); ?>" method="post" enctype="multipart/form-data" class="space-y-4">
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
                        <p class="text-xs text-gray-500 mt-1">يمكنك رفع أكثر من ملف. الحد الأقصى 20 ميجا للملف.</p>
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
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/offline-courses/activity-show.blade.php ENDPATH**/ ?>