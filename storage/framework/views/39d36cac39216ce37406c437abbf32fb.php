

<?php $__env->startSection('title', 'إدارة معرض الصور والفيديوهات - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إدارة معرض الصور والفيديوهات</h1>
                    <p class="mt-2 text-gray-600">إدارة الصور والفيديوهات في المعرض</p>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.media.create')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة ملف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg mb-6">
            <form method="GET" action="<?php echo e(route('admin.media.index')); ?>" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                               placeholder="العنوان أو الوصف"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                            <option value="">جميع الأنواع</option>
                            <option value="image" <?php echo e(request('type') == 'image' ? 'selected' : ''); ?>>صورة</option>
                            <option value="video" <?php echo e(request('type') == 'video' ? 'selected' : ''); ?>>فيديو</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                            <option value="">جميع الفئات</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category); ?>" <?php echo e(request('category') == $category ? 'selected' : ''); ?>><?php echo e($category); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                            <i class="fas fa-search mr-2"></i>
                            بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    الملفات (<?php echo e($media->total()); ?>)
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php $__empty_1 = true; $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow">
                        <div class="relative aspect-video bg-gray-200">
                            <?php if($item->type === 'image'): ?>
                                <img src="<?php echo e(asset($item->file_path)); ?>" alt="<?php echo e($item->title); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-video text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <?php if($item->is_featured): ?>
                            <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">
                                مميز
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-1"><?php echo e($item->title); ?></h4>
                            <p class="text-xs text-gray-500 mb-2"><?php echo e($item->type === 'image' ? 'صورة' : 'فيديو'); ?></p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-xs text-gray-500">
                                    <?php echo e($item->created_at->format('Y-m-d')); ?>

                                </span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="<?php echo e(route('admin.media.show', $item)); ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.media.edit', $item)); ?>" class="text-sky-600 hover:text-sky-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.media.destroy', $item)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        لا توجد ملفات
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($media->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($media->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/media/index.blade.php ENDPATH**/ ?>