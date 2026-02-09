

<?php $__env->startSection('title', 'إضافة مهمة لمدرب - Mindlytics'); ?>
<?php $__env->startSection('header', 'مهام المدربين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 sm:space-y-10">
    <!-- رجوع -->
    <div>
        <a href="<?php echo e(route('admin.tasks.index')); ?>"
           class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-800 font-semibold text-sm">
            <i class="fas fa-arrow-right"></i>
            رجوع لقائمة المهام
        </a>
    </div>

    <!-- نموذج إضافة مهمة -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
                    <i class="fas fa-plus text-xl"></i>
                </span>
                إضافة مهمة جديدة لمدرب
            </h2>
            <p class="text-sm text-slate-500 mt-2">المهمة ستظهر في صفحة «المهام من الإدارة» لدى المدرب المختار.</p>
        </div>

        <form action="<?php echo e(route('admin.tasks.store')); ?>" method="POST" class="p-5 sm:p-8 lg:px-12">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <!-- المدرب -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">المدرب *</label>
                    <select name="user_id" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">اختر المدرب</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('user_id') == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['user_id'];
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

                <!-- عنوان المهمة -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">عنوان المهمة *</label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all"
                           placeholder="مثال: إعداد محتوى الوحدة الثالثة">
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
            </div>

            <!-- الوصف -->
            <div class="mt-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">الوصف</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all"
                          placeholder="تفاصيل إضافية عن المهمة..."><?php echo e(old('description')); ?></textarea>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                <!-- الأولوية -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">الأولوية</label>
                    <select name="priority"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="low" <?php echo e(old('priority', 'medium') == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                        <option value="medium" <?php echo e(old('priority', 'medium') == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                        <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>عالية</option>
                        <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                    </select>
                </div>

                <!-- تاريخ الاستحقاق -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">تاريخ الاستحقاق</label>
                    <input type="datetime-local" name="due_date" value="<?php echo e(old('due_date')); ?>"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
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
            </div>

            <div class="flex flex-wrap gap-4 justify-end mt-8 pt-6 border-t border-slate-200">
                <a href="<?php echo e(route('admin.tasks.index')); ?>"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-sky-600 text-white font-semibold shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ المهمة
                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/tasks/create.blade.php ENDPATH**/ ?>