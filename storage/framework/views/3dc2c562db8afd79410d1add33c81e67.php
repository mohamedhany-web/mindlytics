

<?php $__env->startSection('title', 'إنشاء بنك أسئلة جديد - Mindlytics'); ?>
<?php $__env->startSection('header', 'إنشاء بنك أسئلة جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة كاملاً) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.question-banks.index')); ?>" class="hover:text-sky-600 transition-colors">بنوك الأسئلة</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">إنشاء بنك جديد</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-database text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">إنشاء بنك أسئلة جديد</h1>
                    <p class="text-sm text-slate-600 mt-0.5">أضف بنكاً لإدارة الأسئلة واستخدامها في الاختبارات</p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.question-banks.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <form action="<?php echo e(route('instructor.question-banks.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="p-6 sm:p-8 space-y-8">
                <!-- معلومات بنك الأسئلة -->
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">معلومات بنك الأسئلة</h2>
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">عنوان بنك الأسئلة <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                               placeholder="مثال: بنك أسئلة البرمجة الأساسية">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                                  placeholder="وصف مختصر عن بنك الأسئلة ومحتواه..."><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="max-w-xs">
                        <label for="difficulty" class="block text-sm font-semibold text-slate-700 mb-1">مستوى الصعوبة العام</label>
                        <select name="difficulty" id="difficulty"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <option value="">اختياري</option>
                            <option value="easy" <?php echo e(old('difficulty') == 'easy' ? 'selected' : ''); ?>>سهل</option>
                            <option value="medium" <?php echo e(old('difficulty') == 'medium' ? 'selected' : ''); ?>>متوسط</option>
                            <option value="hard" <?php echo e(old('difficulty') == 'hard' ? 'selected' : ''); ?>>صعب</option>
                        </select>
                    </div>
                </div>

                <!-- الحالة -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">الحالة</h2>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                               class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-700">بنك نشط</span>
                    </label>
                </div>

                <!-- نصائح وأزرار -->
                <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-sm text-sky-800 max-w-md">
                        <span class="font-semibold">نصيحة:</span> بعد الإنشاء ستتمكن من إضافة الأسئلة إلى البنك واستخدامها في أي اختبار.
                    </div>
                    <div class="flex gap-3 shrink-0">
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-save ml-2"></i>
                            إنشاء بنك الأسئلة
                        </button>
                        <a href="<?php echo e(route('instructor.question-banks.index')); ?>" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/question-banks/create.blade.php ENDPATH**/ ?>