<?php $__env->startSection('title', 'واجبات ' . $group->name . ' - Mindlytics'); ?>
<?php $__env->startSection('header', 'واجبات المجموعة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('student.groups.index')); ?>" class="hover:text-sky-600">مجموعاتي</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('student.groups.show', $group)); ?>" class="hover:text-sky-600"><?php echo e($group->name); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">الواجبات</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">واجبات <?php echo e($group->name); ?></h1>
                <p class="text-sm text-slate-500 mt-0.5"><?php echo e($group->course->title ?? '—'); ?></p>
            </div>
            <a href="<?php echo e(route('student.groups.show', $group)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                <i class="fas fa-arrow-right"></i> العودة للمحادثة
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center gap-2">
            <i class="fas fa-tasks text-amber-500"></i>
            <h2 class="font-bold text-slate-800">واجبات المجموعة</h2>
        </div>
        <div class="p-4 sm:p-6 space-y-6">
            <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <h3 class="font-semibold text-slate-800"><?php echo e($assignment->title); ?></h3>
                            <?php if($assignment->due_date): ?>
                                <p class="text-xs text-slate-500 mt-0.5">آخر موعد: <?php echo e($assignment->due_date->format('Y/m/d')); ?></p>
                            <?php endif; ?>
                            <?php if($assignment->description): ?>
                                <p class="text-sm text-slate-600 mt-1"><?php echo e(Str::limit($assignment->description, 200)); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if(isset($assignment->group_submission) && $assignment->group_submission): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <i class="fas fa-check"></i> تم التسليم
                            </span>
                        <?php else: ?>
                            <button type="button" onclick="document.getElementById('submit-form-<?php echo e($assignment->id); ?>').classList.toggle('hidden')"
                                    class="text-sm px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-medium">
                                تسليم الواجب
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if(!isset($assignment->group_submission) || !$assignment->group_submission): ?>
                        <form id="submit-form-<?php echo e($assignment->id); ?>" action="<?php echo e(route('student.groups.assignments.submit', [$group, $assignment])); ?>" method="POST" enctype="multipart/form-data" class="mt-4 hidden border-t border-slate-200 pt-4">
                            <?php echo csrf_field(); ?>
                            <label class="block text-sm font-medium text-slate-700 mb-1">المحتوى أو الرابط</label>
                            <textarea name="content" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-xl" placeholder="اكتب إجابتك أو رابط المشروع..."></textarea>
                            <label class="block text-sm font-medium text-slate-700 mt-2 mb-1">مرفقات (اختياري)</label>
                            <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" class="w-full text-sm text-slate-600">
                            <button type="submit" class="mt-3 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold">
                                <i class="fas fa-upload ml-1"></i> تسليم
                            </button>
                        </form>
                    <?php else: ?>
                        <?php $sub = $assignment->group_submission; ?>
                        <?php if($sub->score !== null || $sub->feedback || in_array($sub->status, ['graded', 'returned'])): ?>
                            <div class="mt-4 pt-4 border-t border-slate-200 rounded-xl bg-white p-4 border border-sky-100">
                                <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-clipboard-check text-sky-500"></i>
                                    نتيجة التقييم
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <?php if($sub->score !== null): ?>
                                        <p class="text-slate-700">
                                            <span class="font-semibold">الدرجة:</span>
                                            <span class="font-bold text-sky-600"><?php echo e($sub->score); ?></span>
                                            <span class="text-slate-500">/ <?php echo e($assignment->max_score); ?></span>
                                        </p>
                                    <?php endif; ?>
                                    <?php if(in_array($sub->status, ['graded', 'returned'])): ?>
                                        <p class="text-slate-700">
                                            <span class="font-semibold">الحالة:</span>
                                            <?php if($sub->status === 'graded'): ?>
                                                <span class="text-emerald-600 font-medium">مقيّم</span>
                                            <?php else: ?>
                                                <span class="text-sky-600 font-medium">مُرجع لك</span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if($sub->feedback): ?>
                                        <div class="mt-2">
                                            <span class="font-semibold text-slate-700 block mb-1">تعليق المصحح:</span>
                                            <p class="text-slate-600 bg-slate-50 rounded-lg p-3 whitespace-pre-wrap"><?php echo e($sub->feedback); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-slate-500 text-sm text-center py-10">لا توجد واجبات مخصصة لهذه المجموعة.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/groups/assignments.blade.php ENDPATH**/ ?>