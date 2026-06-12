

<?php $__env->startSection('title', 'تعديل المقال - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تعديل المقال</h1>
                    <p class="mt-2 text-gray-600"><?php echo e($blog->title); ?></p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.blog.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="<?php echo e(route('admin.blog.update', $blog)); ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">
                <!-- العنوان -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" value="<?php echo e(old('title', $blog->title)); ?>" required
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

                <!-- الرابط (Slug) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                    <input type="text" name="slug" value="<?php echo e(old('slug', $blog->slug)); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['slug'];
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

                <!-- الملخص -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الملخص</label>
                    <textarea name="excerpt" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"><?php echo e(old('excerpt', $blog->excerpt)); ?></textarea>
                    <?php $__errorArgs = ['excerpt'];
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

                <!-- المحتوى -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى *</label>
                    <textarea name="content" id="content" rows="15" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"><?php echo e(old('content', $blog->content)); ?></textarea>
                    <?php $__errorArgs = ['content'];
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

                <!-- الصورة المميزة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المميزة</label>
                    <?php if($blog->featured_image): ?>
                    <div class="mb-2">
                        <img src="<?php echo e(asset($blog->featured_image)); ?>" alt="<?php echo e($blog->title); ?>" class="h-32 w-32 object-cover rounded-lg mb-2" onerror="this.style.display='none'">
                        <p class="text-xs text-gray-500">الصورة الحالية</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="featured_image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    <?php $__errorArgs = ['featured_image'];
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                        <option value="draft" <?php echo e(old('status', $blog->status) == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="published" <?php echo e(old('status', $blog->status) == 'published' ? 'selected' : ''); ?>>منشور</option>
                    </select>
                    <?php $__errorArgs = ['status'];
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
                    <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $blog->is_featured) ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <label class="mr-2 text-sm font-medium text-gray-700">مقال مميز</label>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوسوم (افصل بينها بفواصل)</label>
                    <?php
                        $tagsInput = old('tags_input', is_array($blog->tags) ? implode(', ', $blog->tags) : '');
                    ?>
                    <input type="text" name="tags_input" value="<?php echo e($tagsInput); ?>"
                           placeholder="مثال: برمجة, تطوير, Laravel"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                </div>

                <!-- Meta Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان SEO</label>
                    <input type="text" name="meta_title" value="<?php echo e(old('meta_title', $blog->meta_title)); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                </div>

                <!-- Meta Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وصف SEO</label>
                    <textarea name="meta_description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900"><?php echo e(old('meta_description', $blog->meta_description)); ?></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                <a href="<?php echo e(route('admin.blog.index')); ?>" 
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
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/blog/edit.blade.php ENDPATH**/ ?>