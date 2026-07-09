

<?php $__env->startSection('title', 'إضافة سؤال جديد - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إضافة سؤال جديد</h1>
                    <p class="mt-2 text-gray-600">إضافة سؤال جديد للأسئلة الشائعة</p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.faq.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="<?php echo e(route('admin.faq.store')); ?>" method="POST" class="bg-white shadow rounded-lg p-6">
            <?php echo csrf_field(); ?>

            <div class="space-y-6">
                <!-- السؤال -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السؤال *</label>
                    <input type="text" name="question" value="<?php echo e(old('question')); ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['question'];
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

                <!-- الإجابة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الإجابة *</label>
                    <textarea name="answer" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"><?php echo e(old('answer')); ?></textarea>
                    <?php $__errorArgs = ['answer'];
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

                <!-- الفئة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <input type="text" name="category" value="<?php echo e(old('category')); ?>" list="categories"
                           placeholder="أدخل فئة جديدة أو اختر من القائمة"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <datalist id="categories">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </datalist>
                    <?php $__errorArgs = ['category'];
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

                <!-- الترتيب -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
                    <input type="number" name="order" value="<?php echo e(old('order', 0)); ?>" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['order'];
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

                <!-- الحالة -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">نشط</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="<?php echo e(route('admin.faq.index')); ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                    حفظ السؤال
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\faq\create.blade.php ENDPATH**/ ?>