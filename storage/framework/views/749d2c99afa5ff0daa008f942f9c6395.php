

<?php $__env->startSection('title', $assignment->title); ?>
<?php $__env->startSection('header', 'تفاصيل الواجب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-bold text-slate-800"><?php echo e($assignment->title); ?></h1>
        <p class="text-sm text-slate-500 mt-1"><?php echo e($assignment->course->title ?? '—'); ?></p>
        <?php if($assignment->description): ?>
            <p class="text-slate-700 mt-4 whitespace-pre-wrap"><?php echo e($assignment->description); ?></p>
        <?php endif; ?>
        <?php if($assignment->instructions): ?>
            <div class="mt-4 p-4 bg-sky-50 border border-sky-200 rounded-xl text-sky-900 whitespace-pre-wrap"><?php echo e($assignment->instructions); ?></div>
        <?php endif; ?>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">الدرجة:</span> <span class="font-semibold text-slate-800"><?php echo e($assignment->max_score); ?></span></div>
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">آخر موعد:</span> <span class="font-semibold text-slate-800"><?php echo e($assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : 'غير محدد'); ?></span></div>
            <div class="rounded-lg border border-slate-200 p-3"><span class="text-slate-500">التسليم المتأخر:</span> <span class="font-semibold text-slate-800"><?php echo e($assignment->allow_late_submission ? 'مسموح' : 'غير مسموح'); ?></span></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">تسليم الواجب</h2>
        <form method="POST" action="<?php echo e(route('student.assignments.submit', $assignment)); ?>" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">المحتوى / رابط المشروع</label>
                <textarea name="content" rows="5" class="w-full px-3 py-2 border border-slate-200 rounded-xl" placeholder="اكتب الحل أو رابط المشروع..."><?php echo e(old('content', $submission->content ?? '')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات (حتى 100MB لكل ملف)</label>
                <input type="file" name="attachments[]" multiple class="w-full text-sm text-slate-600" />
                <p class="text-xs text-slate-500 mt-1">الملفات سترفع على Cloudflare تلقائياً.</p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold">
                <i class="fas fa-upload"></i>
                تسليم الواجب
            </button>
        </form>

        <?php if($submission): ?>
            <div class="mt-6 pt-6 border-t border-slate-200">
                <h3 class="font-bold text-slate-800 mb-2">آخر تسليم</h3>
                <p class="text-sm text-slate-600">الحالة: <span class="font-semibold"><?php echo e($submission->status); ?></span></p>
                <?php if($submission->submitted_at): ?>
                    <p class="text-sm text-slate-600">تاريخ التسليم: <?php echo e($submission->submitted_at->format('Y-m-d H:i')); ?></p>
                <?php endif; ?>
                <?php if($submission->score !== null): ?>
                    <p class="text-sm text-slate-600">الدرجة: <span class="font-semibold text-sky-700"><?php echo e($submission->score); ?></span> / <?php echo e($assignment->max_score); ?></p>
                <?php endif; ?>
                <?php if($submission->feedback): ?>
                    <div class="mt-2 p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 whitespace-pre-wrap"><?php echo e($submission->feedback); ?></div>
                <?php endif; ?>
                <?php if($submission->attachments && count($submission->attachments)): ?>
                    <ul class="mt-3 space-y-1 text-sm">
                        <?php $__currentLoopData = $submission->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                $url = is_array($att) && !empty($att['url']) ? $att['url'] : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/'.$path)) : '#');
                                $name = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                            ?>
                            <li><a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="text-sky-600 hover:underline"><?php echo e($name); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\assignments\show.blade.php ENDPATH**/ ?>