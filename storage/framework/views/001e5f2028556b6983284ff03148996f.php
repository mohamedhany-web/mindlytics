

<?php $__env->startSection('title', 'تعديل المهمة - ' . $task->title); ?>
<?php $__env->startSection('header', 'تعديل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" class="hover:text-sky-600">المهام من الإدارة</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.tasks.show', $task)); ?>" class="hover:text-sky-600"><?php echo e($task->title); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل المهمة</h1>
        <p class="text-sm text-slate-500 mt-0.5">تحديث الحالة أو التفاصيل</p>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
        <form action="<?php echo e(route('instructor.tasks.update', $task)); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان المهمة *</label>
                <input type="text" name="title" value="<?php echo e(old('title', $task->title)); ?>" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors"><?php echo e(old('description', $task->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الأولوية</label>
                    <select name="priority" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="low" <?php echo e(old('priority', $task->priority) == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                        <option value="medium" <?php echo e(old('priority', $task->priority) == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                        <option value="high" <?php echo e(old('priority', $task->priority) == 'high' ? 'selected' : ''); ?>>عالية</option>
                        <option value="urgent" <?php echo e(old('priority', $task->priority) == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="pending" <?php echo e(old('status', $task->status) == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="in_progress" <?php echo e(old('status', $task->status) == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="completed" <?php echo e(old('status', $task->status) == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="cancelled" <?php echo e(old('status', $task->status) == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الاستحقاق</label>
                <input type="datetime-local" name="due_date" value="<?php echo e(old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '')); ?>"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس (اختياري)</label>
                    <select name="related_course_id" id="related_course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="">لا يوجد</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('related_course_id', $task->related_course_id) == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المحاضرة (اختياري)</label>
                    <select name="related_lecture_id" id="related_lecture_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                        <option value="">لا يوجد</option>
                        <?php $__currentLoopData = $lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($lecture->id); ?>" <?php echo e(old('related_lecture_id', $task->related_lecture_id) == $lecture->id ? 'selected' : ''); ?>>
                                <?php echo e($lecture->title); ?> <?php if($lecture->scheduled_at): ?> - <?php echo e($lecture->scheduled_at->format('Y/m/d')); ?> <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <a href="<?php echo e(route('instructor.tasks.show', $task)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 bg-white text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('related_course_id').addEventListener('change', function() {
    const courseId = this.value;
    const lectureSelect = document.getElementById('related_lecture_id');
    lectureSelect.innerHTML = '<option value="">لا يوجد</option>';
    if (courseId) {
        fetch(`<?php echo e(route('instructor.tasks.lectures')); ?>?course_id=${courseId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            data.forEach(lecture => {
                const opt = document.createElement('option');
                opt.value = lecture.id;
                opt.textContent = lecture.title + (lecture.scheduled_at ? ' - ' + lecture.scheduled_at : '');
                lectureSelect.appendChild(opt);
            });
        })
        .catch(() => {});
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/tasks/edit.blade.php ENDPATH**/ ?>