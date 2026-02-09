

<?php $__env->startSection('title', 'تفاصيل المهمة - ' . $task->title); ?>
<?php $__env->startSection('header', 'تفاصيل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $priorityBadges = [
        'urgent' => ['label' => 'عاجلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'high'   => ['label' => 'عالية', 'classes' => 'bg-amber-100 text-amber-700'],
        'medium' => ['label' => 'متوسطة', 'classes' => 'bg-sky-100 text-sky-700'],
        'low'    => ['label' => 'منخفضة', 'classes' => 'bg-slate-100 text-slate-700'],
    ];
    $statusBadges = [
        'completed'   => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'classes' => 'bg-blue-100 text-blue-700'],
        'pending'     => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700'],
        'cancelled'   => ['label' => 'ملغاة', 'classes' => 'bg-rose-100 text-rose-700'],
    ];
    $deliverableStatusBadges = [
        'submitted'      => ['label' => 'مقدّم', 'classes' => 'bg-blue-100 text-blue-700'],
        'approved'       => ['label' => 'معتمد', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'rejected'       => ['label' => 'مرفوض', 'classes' => 'bg-rose-100 text-rose-700'],
        'needs_revision' => ['label' => 'يحتاج مراجعة', 'classes' => 'bg-amber-100 text-amber-700'],
    ];
?>

<div class="space-y-6 sm:space-y-8">
    
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <nav class="text-sm text-slate-500 mb-4">
                <a href="<?php echo e(route('admin.tasks.index')); ?>" class="hover:text-sky-600 transition-colors">مهام المدربين</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800 font-semibold"><?php echo e($task->title); ?></span>
            </nav>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900"><?php echo e($task->title); ?></h1>
                    <p class="text-slate-600 mt-2 flex items-center gap-2">
                        <span class="w-8 h-8 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xs inline-flex">
                            <?php echo e(mb_substr($task->user->name ?? '?', 0, 1, 'UTF-8')); ?>

                        </span>
                        <?php echo e($task->user->name ?? '—'); ?>

                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <?php if($task->status != 'completed'): ?>
                        <form action="<?php echo e(route('admin.tasks.complete', $task)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
                                <i class="fas fa-check"></i>
                                إكمال المهمة
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.tasks.edit', $task)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition-all">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                    <a href="<?php echo e(route('admin.tasks.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-arrow-right"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">الحالة</p>
                <?php $sb = $statusBadges[$task->status] ?? null; ?>
                <?php if($sb): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold <?php echo e($sb['classes']); ?>">
                        <span class="h-2 w-2 rounded-full bg-current"></span>
                        <?php echo e($sb['label']); ?>

                    </span>
                <?php else: ?>
                    <span class="text-slate-700">—</span>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">الأولوية</p>
                <?php $pb = $priorityBadges[$task->priority] ?? null; ?>
                <?php if($pb): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold <?php echo e($pb['classes']); ?>">
                        <span class="h-2 w-2 rounded-full bg-current"></span>
                        <?php echo e($pb['label']); ?>

                    </span>
                <?php else: ?>
                    <span class="text-slate-700">—</span>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">تاريخ الاستحقاق</p>
                <p class="text-slate-900 font-semibold">
                    <?php echo e($task->due_date ? $task->due_date->format('Y-m-d H:i') : '—'); ?>

                </p>
                <?php if($task->due_date && $task->due_date->isPast() && $task->status != 'completed'): ?>
                    <p class="text-rose-600 text-sm font-semibold mt-1">متأخرة</p>
                <?php endif; ?>
            </div>
            <?php if($task->assigned_by && isset($task->progress)): ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 mb-2">التقدم</p>
                <p class="text-slate-900 font-bold text-xl"><?php echo e((int)($task->progress ?? 0)); ?>%</p>
            </div>
            <?php endif; ?>
        </div>

        <?php if($task->description): ?>
            <div class="px-5 pb-6 sm:px-8 sm:pb-8">
                <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-align-right text-sky-600"></i>
                    الوصف
                </h3>
                <p class="text-slate-700 leading-relaxed whitespace-pre-wrap"><?php echo e($task->description); ?></p>
            </div>
        <?php endif; ?>

        <?php if($task->relatedCourse || $task->relatedLecture): ?>
            <div class="px-5 pb-6 sm:px-8 sm:pb-8">
                <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-link text-sky-600"></i>
                    مرتبطة بـ
                </h3>
                <p class="text-slate-700">
                    <?php if($task->relatedCourse): ?>
                        <a href="<?php echo e(route('admin.advanced-courses.show', $task->relatedCourse)); ?>" class="text-sky-600 hover:text-sky-800 font-medium underline"><?php echo e($task->relatedCourse->title); ?></a>
                    <?php elseif($task->relatedLecture): ?>
                        <a href="<?php echo e(route('admin.lectures.show', $task->relatedLecture)); ?>" class="text-sky-600 hover:text-sky-800 font-medium underline"><?php echo e($task->relatedLecture->title); ?></a>
                    <?php else: ?>
                        ID: <?php echo e($task->related_id); ?>

                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if($task->completed_at): ?>
            <div class="mx-5 mb-6 sm:mx-8 sm:mb-8 rounded-2xl bg-emerald-50 border border-emerald-200 p-5">
                <p class="text-emerald-800 font-medium flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    تم إكمال المهمة في: <?php echo e($task->completed_at->format('Y-m-d H:i')); ?>

                </p>
            </div>
        <?php endif; ?>
    </section>

    
    <?php if($task->assigned_by && $task->deliverables->count() > 0): ?>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-paper-plane text-sky-600"></i>
                تسليمات المدرب
            </h2>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            <?php $__currentLoopData = $task->deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                    <h3 class="text-lg font-bold text-slate-900"><?php echo e($d->title); ?></h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500"><?php echo e($d->submitted_at?->format('Y-m-d H:i')); ?></span>
                        <?php $dsb = $deliverableStatusBadges[$d->status] ?? ['label' => $d->status, 'classes' => 'bg-slate-100 text-slate-700']; ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($dsb['classes']); ?>"><?php echo e($dsb['label']); ?></span>
                    </div>
                </div>
                <?php if($d->description): ?>
                    <p class="text-slate-700 text-sm mb-3"><?php echo e($d->description); ?></p>
                <?php endif; ?>
                <?php if($d->delivery_type === 'link' && $d->link_url): ?>
                    <p class="text-sm mb-2">
                        <a href="<?php echo e($d->link_url); ?>" target="_blank" class="text-sky-600 hover:text-sky-800 font-medium underline">فتح الرابط</a>
                    </p>
                <?php endif; ?>
                <?php if($d->file_path): ?>
                    <p class="text-sm mb-2">
                        <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($d->file_path)); ?>" target="_blank" class="text-sky-600 hover:text-sky-800 font-medium underline">تحميل الملف: <?php echo e($d->file_name ?? 'ملف'); ?></a>
                    </p>
                <?php endif; ?>
                <?php if($d->feedback): ?>
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 mb-4">
                        <p class="text-xs font-semibold text-amber-800 mb-1">ملاحظاتك السابقة</p>
                        <p class="text-amber-900 text-sm"><?php echo e($d->feedback); ?></p>
                    </div>
                <?php endif; ?>
                <form action="<?php echo e(route('admin.tasks.review-deliverable', [$task, $d])); ?>" method="POST" class="flex flex-wrap items-end gap-3">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الملاحظات للمدرب</label>
                        <input type="text" name="feedback" value="<?php echo e(old('feedback', $d->feedback)); ?>" placeholder="اختياري"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">القرار</label>
                        <select name="status" required class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                            <option value="approved" <?php echo e($d->status === 'approved' ? 'selected' : ''); ?>>اعتماد</option>
                            <option value="rejected" <?php echo e($d->status === 'rejected' ? 'selected' : ''); ?>>رفض</option>
                            <option value="needs_revision" <?php echo e($d->status === 'needs_revision' ? 'selected' : ''); ?>>يحتاج مراجعة</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-check"></i>
                        حفظ المراجعة
                    </button>
                </form>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-comments text-sky-600"></i>
                التعليقات
            </h2>
        </div>
        <div class="p-5 sm:p-8">
            <div class="space-y-4 mb-6">
                <?php $__empty_1 = true; $__currentLoopData = $task->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                            <?php echo e(mb_substr($comment->user->name ?? '?', 0, 1, 'UTF-8')); ?>

                        </div>
                        <div>
                            <p class="font-semibold text-slate-900"><?php echo e($comment->user->name); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($comment->created_at->format('Y-m-d H:i')); ?></p>
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm"><?php echo e($comment->comment); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-slate-500 text-center py-8">لا توجد تعليقات</p>
                <?php endif; ?>
            </div>

            <form action="<?php echo e(route('admin.tasks.add-comment', $task)); ?>" method="POST" class="flex flex-wrap gap-3">
                <?php echo csrf_field(); ?>
                <input type="text" name="comment" required placeholder="أضف تعليقاً..."
                       class="flex-1 min-w-[200px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-paper-plane"></i>
                    إرسال
                </button>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/tasks/show.blade.php ENDPATH**/ ?>