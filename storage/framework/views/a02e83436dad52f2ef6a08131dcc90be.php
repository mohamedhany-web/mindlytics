

<?php $__env->startSection('title', 'تعديل الملف - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تعديل الملف</h1>
                    <p class="mt-2 text-gray-600"><?php echo e($media->title); ?></p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.media.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="<?php echo e(route('admin.media.update', $media)); ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <!-- العنوان -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" value="<?php echo e(old('title', $media->title)); ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
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

                <!-- الوصف -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"><?php echo e(old('description', $media->description)); ?></textarea>
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

                <!-- النوع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النوع *</label>
                    <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                        <option value="image" <?php echo e(old('type', $media->type) == 'image' ? 'selected' : ''); ?>>صورة</option>
                        <option value="video" <?php echo e(old('type', $media->type) == 'video' ? 'selected' : ''); ?>>فيديو</option>
                    </select>
                    <?php $__errorArgs = ['type'];
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

                <!-- الملف الحالي -->
                <?php if($media->file_path): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الملف الحالي</label>
                    <?php if($media->type === 'image'): ?>
                    <img src="<?php echo e(asset($media->file_path)); ?>" alt="<?php echo e($media->title); ?>" class="h-32 w-32 object-cover rounded-lg mb-2">
                    <?php else: ?>
                    <p class="text-sm text-gray-600">فيديو: <?php echo e($media->file_path); ?></p>
                    <?php endif; ?>
                    <p class="text-xs text-gray-500">الملف الحالي</p>
                </div>
                <?php endif; ?>

                <!-- الملف الجديد -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الملف الجديد (اختياري)</label>
                    <input type="file" name="file" id="file" accept="image/*,video/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['file'];
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

                <!-- الصورة المصغرة -->
                <div id="thumbnail-section" style="display: <?php echo e(old('type', $media->type) == 'video' ? 'block' : 'none'); ?>;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المصغرة</label>
                    <?php if($media->thumbnail_path): ?>
                    <div class="mb-2">
                        <img src="<?php echo e(asset($media->thumbnail_path)); ?>" alt="Thumbnail" class="h-24 w-24 object-cover rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">الصورة المصغرة الحالية</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['thumbnail'];
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
                    <input type="text" name="category" value="<?php echo e(old('category', $media->category)); ?>" list="categories"
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

                <!-- مميز -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $media->is_featured) ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">مميز</label>
                </div>

                <!-- الحالة -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $media->is_active) ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">نشط</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="<?php echo e(route('admin.media.index')); ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const thumbnailSection = document.getElementById('thumbnail-section');
    if (this.value === 'video') {
        thumbnailSection.style.display = 'block';
    } else {
        thumbnailSection.style.display = 'none';
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\media\edit.blade.php ENDPATH**/ ?>