

<?php $__env->startSection('title', ($medium ?? $media)->title . ' - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo e(($medium ?? $media)->title); ?></h1>
                    <p class="mt-2 text-gray-600"><?php echo e(($medium ?? $media)->type === 'image' ? 'صورة' : 'فيديو'); ?></p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="<?php echo e(route('admin.media.edit', $medium ?? $media)); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
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
        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-6">
                <?php
                    $mediaItem = $medium ?? $media;
                ?>
                
                <?php if($mediaItem->type === 'image' && $mediaItem->file_path): ?>
                <div>
                    <img src="<?php echo e(asset($mediaItem->file_path)); ?>" alt="<?php echo e($mediaItem->title); ?>" class="w-full rounded-lg">
                </div>
                <?php elseif($mediaItem->type === 'video' && $mediaItem->file_path): ?>
                <div class="bg-gray-200 rounded-lg p-8 text-center">
                    <i class="fas fa-video text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">فيديو: <?php echo e($mediaItem->file_path); ?></p>
                </div>
                <?php endif; ?>

                <?php if($mediaItem->description): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">الوصف</h3>
                    <p class="text-gray-600"><?php echo e($mediaItem->description); ?></p>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">النوع</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e($mediaItem->type === 'image' ? 'صورة' : 'فيديو'); ?>

                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الفئة</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($mediaItem->category ?? 'غير محدد'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">مميز</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e($mediaItem->is_featured ? 'نعم' : 'لا'); ?>

                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الحالة</p>
                        <p class="text-sm font-medium text-gray-900">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                <?php echo e($mediaItem->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800); ?>">']
                                <?php echo e($mediaItem->is_active ? 'نشط' : 'غير نشط'); ?>

                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($mediaItem->created_at->format('Y-m-d H:i')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ آخر تعديل</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($mediaItem->updated_at->format('Y-m-d H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/media/show.blade.php ENDPATH**/ ?>