<?php $__env->startSection('title', 'إضافة نشاط - كورس أوفلاين'); ?>
<?php $__env->startSection('header', 'إضافة نشاط'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.activities.index', $offlineCourse)); ?>" class="hover:text-amber-600">الواجبات والاختبارات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">إضافة نشاط</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">إضافة واجب أو اختبار (أوفلاين)</h1>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="<?php echo e(route('instructor.offline-courses.activities.store', $offlineCourse)); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500" placeholder="مثال: الواجب الأول">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500"><?php echo e(old('description')); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع النشاط <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500">
                        <option value="assignment" <?php echo e(old('type', 'assignment') === 'assignment' ? 'selected' : ''); ?>>واجب</option>
                        <option value="exam" <?php echo e(old('type') === 'exam' ? 'selected' : ''); ?>>اختبار</option>
                        <option value="quiz" <?php echo e(old('type') === 'quiz' ? 'selected' : ''); ?>>اختبار قصير</option>
                        <option value="project" <?php echo e(old('type') === 'project' ? 'selected' : ''); ?>>مشروع</option>
                        <option value="presentation" <?php echo e(old('type') === 'presentation' ? 'selected' : ''); ?>>عرض</option>
                        <option value="other" <?php echo e(old('type') === 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">آخر موعد تسليم</label>
                        <input type="date" name="due_date" value="<?php echo e(old('due_date')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة العظمى <span class="text-red-500">*</span></label>
                        <input type="number" name="max_score" value="<?php echo e(old('max_score', 100)); ?>" min="0" max="1000" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">تعليمات التسليم</label>
                    <textarea name="instructions" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500" placeholder="كيف يسلّم الطالب الواجب؟"><?php echo e(old('instructions')); ?></textarea>
                </div>
                <?php if($groups->isNotEmpty()): ?>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة (اختياري)</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500">
                            <option value="">كل الطلاب</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e(old('group_id') == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-amber-500">
                        <option value="draft" <?php echo e(old('status', 'draft') === 'draft' ? 'selected' : ''); ?>>مسودة (لا يظهر للطلاب)</option>
                        <option value="published" <?php echo e(old('status') === 'published' ? 'selected' : ''); ?>>منشور (يظهر للطلاب)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات (ملفات للطلاب)</label>
                    <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">حفظ</button>
                <a href="<?php echo e(route('instructor.offline-courses.activities.index', $offlineCourse)); ?>" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/activities/create.blade.php ENDPATH**/ ?>