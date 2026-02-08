<?php $__env->startSection('title', $lecture->title . ' - محاضرة أوفلاين'); ?>
<?php $__env->startSection('header', $lecture->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index')); ?>" class="hover:text-amber-600">كورساتي الأوفلاين</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.lectures.index', $offlineCourse)); ?>" class="hover:text-amber-600">المحاضرات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($lecture->title); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-800"><?php echo e($lecture->title); ?></h1>
            <a href="<?php echo e(route('instructor.offline-courses.lectures.edit', [$offlineCourse, $lecture])); ?>" class="px-4 py-2 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">تعديل</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <?php if($lecture->description): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">الوصف</h3>
                <p class="text-slate-700 whitespace-pre-line"><?php echo e($lecture->description); ?></p>
            </div>
        <?php endif; ?>
        <?php if($lecture->scheduled_at): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">الموعد</h3>
                <p class="text-slate-700"><?php echo e($lecture->scheduled_at->format('Y-m-d H:i')); ?> <?php if($lecture->duration_minutes): ?>(<?php echo e($lecture->duration_minutes); ?> دقيقة)<?php endif; ?></p>
            </div>
        <?php endif; ?>
        <?php if($lecture->recording_url): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">تسجيل المحاضرة</h3>
                <a href="<?php echo e($lecture->recording_url); ?>" target="_blank" rel="noopener" class="text-violet-600 hover:underline font-medium"><?php echo e($lecture->recording_url); ?></a>
            </div>
        <?php endif; ?>
        <?php if($lecture->download_links && count($lecture->download_links) > 0): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">روابط التحميل</h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $lecture->download_links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e($link['url'] ?? '#'); ?>" target="_blank" rel="noopener" class="text-violet-600 hover:underline"><?php echo e($link['label'] ?? 'رابط'); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if($lecture->attachments && count($lecture->attachments) > 0): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">مرفقات</h3>
                <ul class="space-y-2">
                    <?php $__currentLoopData = $lecture->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e(asset('storage/' . ($att['path'] ?? ''))); ?>" target="_blank" class="text-violet-600 hover:underline"><?php echo e($att['name'] ?? 'ملف'); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if($lecture->notes): ?>
            <div>
                <h3 class="text-sm font-bold text-slate-600 mb-2">ملاحظات</h3>
                <p class="text-slate-700 whitespace-pre-line"><?php echo e($lecture->notes); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/lectures/show.blade.php ENDPATH**/ ?>