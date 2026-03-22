

<?php $__env->startSection('title', $blog->title . ' - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo e($blog->title); ?></h1>
                    <p class="mt-2 text-gray-600">
                        بواسطة: <?php echo e($blog->author->name ?? 'غير محدد'); ?> | 
                        <?php echo e($blog->published_at ? $blog->published_at->format('Y-m-d') : 'غير منشور'); ?>

                    </p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="<?php echo e(route('admin.blog.edit', $blog)); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        تعديل
                    </a>
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
        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-6">
                <?php if($blog->featured_image): ?>
                <div>
                    <img src="<?php echo e(asset($blog->featured_image)); ?>" alt="<?php echo e($blog->title); ?>" class="w-full rounded-lg max-h-96 object-cover" onerror="this.src='<?php echo e(asset('images/placeholder.jpg')); ?>'">
                </div>
                <?php endif; ?>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">المحتوى</h2>
                    <div class="prose max-w-none">
                        <?php echo nl2br(e($blog->content)); ?>

                    </div>
                </div>

                <?php if($blog->excerpt): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">الملخص</h3>
                    <p class="text-gray-600"><?php echo e($blog->excerpt); ?></p>
                </div>
                <?php endif; ?>

                <?php if($blog->tags && count($blog->tags) > 0): ?>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">الوسوم</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $blog->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-3 py-1 bg-sky-100 text-sky-800 rounded-full text-sm">
                            <?php echo e($tag); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500">الحالة</p>
                        <p class="text-sm font-medium text-gray-900">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                <?php echo e($blog->status === 'published' ? 'bg-green-100 text-green-800 ': ''bg-yellow-100 text-yellow-800); ?>">']
                                <?php echo e($blog->status === 'published' ? 'منشور' : 'مسودة'); ?>

                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">مميز</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e($blog->is_featured ? 'نعم' : 'لا'); ?>

                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ الإنشاء</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($blog->created_at->format('Y-m-d H:i')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ آخر تعديل</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($blog->updated_at->format('Y-m-d H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\blog\show.blade.php ENDPATH**/ ?>