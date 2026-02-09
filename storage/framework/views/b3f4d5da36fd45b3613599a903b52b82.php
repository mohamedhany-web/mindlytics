

<?php $__env->startSection('title', $task->title . ' - المهام من الإدارة'); ?>
<?php $__env->startSection('header', 'تفاصيل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" class="hover:text-sky-600">المهام من الإدارة</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e($task->title); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800"><?php echo e($task->title); ?></h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <?php if($task->assigner): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">من الإدارة</span>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                        <?php if($task->priority == 'urgent'): ?> bg-rose-100 text-rose-700
                        <?php elseif($task->priority == 'high'): ?> bg-amber-100 text-amber-700
                        <?php elseif($task->priority == 'medium'): ?> bg-sky-100 text-sky-700
                        <?php else: ?> bg-slate-100 text-slate-600
                        <?php endif; ?>">
                        <?php if($task->priority == 'urgent'): ?> عاجلة
                        <?php elseif($task->priority == 'high'): ?> عالية
                        <?php elseif($task->priority == 'medium'): ?> متوسطة
                        <?php else: ?> منخفضة
                        <?php endif; ?>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                        <?php if($task->status == 'completed'): ?> bg-emerald-100 text-emerald-700
                        <?php elseif($task->status == 'in_progress'): ?> bg-blue-100 text-blue-700
                        <?php else: ?> bg-amber-100 text-amber-700
                        <?php endif; ?>">
                        <?php if($task->status == 'completed'): ?> مكتملة
                        <?php elseif($task->status == 'in_progress'): ?> قيد التنفيذ
                        <?php else: ?> معلقة
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <?php if(!$task->assigned_by): ?>
                    <a href="<?php echo e(route('instructor.tasks.edit', $task)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm transition-colors">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('instructor.tasks.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>
    </div>

    <?php if($task->description): ?>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-2">الوصف</h3>
            <p class="text-slate-600 whitespace-pre-wrap"><?php echo e($task->description); ?></p>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="font-bold text-slate-800 mb-4">تفاصيل إضافية</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php if($task->relatedCourse): ?>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">
                        <i class="fas fa-book"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">الكورس</p>
                        <p class="text-slate-800 font-medium"><?php echo e($task->relatedCourse->title); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($task->relatedLecture): ?>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">المحاضرة</p>
                        <p class="text-slate-800 font-medium"><?php echo e($task->relatedLecture->title); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($task->due_date): ?>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">تاريخ الاستحقاق</p>
                        <p class="text-slate-800 font-medium"><?php echo e($task->due_date->format('Y-m-d H:i')); ?></p>
                        <?php if($task->due_date->isPast() && $task->status != 'completed'): ?>
                            <p class="text-rose-600 text-sm font-semibold mt-0.5">متأخرة</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($task->completed_at): ?>
                <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-emerald-700">تم الإكمال</p>
                        <p class="text-slate-800 font-medium"><?php echo e($task->completed_at->format('Y-m-d H:i')); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($task->assigned_by && isset($task->progress)): ?>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">التقدم</p>
                        <p class="text-slate-800 font-medium"><?php echo e((int)($task->progress ?? 0)); ?>%</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($task->assigned_by): ?>
        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-4">تحديث التقدم</h3>
            <form action="<?php echo e(route('instructor.tasks.update-progress', $task)); ?>" method="POST" class="flex flex-wrap items-end gap-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="pending" <?php echo e($task->status === 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="in_progress" <?php echo e($task->status === 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="completed" <?php echo e($task->status === 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نسبة التقدم % (0–100)</label>
                    <input type="number" name="progress" min="0" max="100" value="<?php echo e((int)($task->progress ?? 0)); ?>"
                           class="w-24 px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save ml-1"></i>
                    حفظ التقدم
                </button>
            </form>
        </div>

        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="font-bold text-slate-800 mb-4">تسليماتي</h3>
            <?php if($task->deliverables->count() > 0): ?>
                <ul class="space-y-3 mb-6">
                    <?php $__currentLoopData = $task->deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex flex-wrap items-start justify-between gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($d->title); ?></p>
                                <?php if($d->description): ?>
                                    <p class="text-sm text-slate-600 mt-1"><?php echo e($d->description); ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-500 mt-2">
                                    <?php echo e($d->submitted_at?->format('Y-m-d H:i')); ?>

                                    <?php if($d->delivery_type === 'link' && $d->link_url): ?>
                                        · <a href="<?php echo e($d->link_url); ?>" target="_blank" class="text-sky-600 hover:underline">فتح الرابط</a>
                                    <?php endif; ?>
                                    <?php if($d->file_path): ?>
                                        · <a href="<?php echo e(Storage::url($d->file_path)); ?>" target="_blank" class="text-sky-600 hover:underline">تحميل الملف</a>
                                    <?php endif; ?>
                                </p>
                                <?php if($d->feedback): ?>
                                    <p class="text-sm mt-2 p-2 bg-amber-50 rounded-lg text-amber-800 border border-amber-100"><strong>ملاحظات الإدارة:</strong> <?php echo e($d->feedback); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                <?php if($d->status === 'approved'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($d->status === 'rejected' || $d->status === 'needs_revision'): ?> bg-rose-100 text-rose-700
                                <?php else: ?> bg-blue-100 text-blue-700
                                <?php endif; ?>">
                                <?php if($d->status === 'approved'): ?> معتمد
                                <?php elseif($d->status === 'rejected'): ?> مرفوض
                                <?php elseif($d->status === 'needs_revision'): ?> يحتاج مراجعة
                                <?php else: ?> مقدّم
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <form action="<?php echo e(route('instructor.tasks.submit-deliverable', $task)); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان التسليم <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required maxlength="255" value="<?php echo e(old('title')); ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                               placeholder="مثال: التقرير النهائي">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">نوع التسليم</label>
                        <select name="delivery_type" id="delivery_type" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                            <option value="file">ملف</option>
                            <option value="image">صورة</option>
                            <option value="link">رابط</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف (اختياري)</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" placeholder="وصف مختصر للتسليم"><?php echo e(old('description')); ?></textarea>
                </div>
                <div id="file_input" class="flex items-center gap-2">
                    <label class="block text-sm font-semibold text-slate-700">الملف</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" class="border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    <span class="text-xs text-slate-500">حد أقصى 10 ميجا</span>
                </div>
                <div id="link_input" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">رابط التسليم</label>
                    <input type="url" name="link_url" value="<?php echo e(old('link_url')); ?>" placeholder="https://..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-paper-plane"></i>
                    تسليم العمل
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('delivery_type').addEventListener('change', function() {
    var type = this.value;
    document.getElementById('file_input').classList.toggle('hidden', type === 'link');
    document.getElementById('link_input').classList.toggle('hidden', type !== 'link');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/tasks/show.blade.php ENDPATH**/ ?>