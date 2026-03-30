

<?php $__env->startSection('title', 'التقييمات والمراجعات'); ?>
<?php $__env->startSection('header', 'التقييمات والمراجعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">التقييمات والمراجعات</h1>
            <p class="text-gray-600 mt-1">إدارة تقييمات ومراجعات الكورسات</p>
        </div>
    </div>

    <!-- الإحصائيات -->
    <?php if(isset($stats)): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">إجمالي المراجعات</div>
            <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo e($stats['total'] ?? 0); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">متوسط التقييم</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2"><?php echo e($stats['average_rating'] ?? 0); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">المقبولة</div>
            <div class="text-2xl font-bold text-green-600 mt-2"><?php echo e($stats['approved'] ?? 0); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">المعلقة</div>
            <div class="text-2xl font-bold text-yellow-600 mt-2"><?php echo e($stats['pending'] ?? 0); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- قائمة المراجعات -->
    <?php if(isset($reviews) && $reviews->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكورس</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التعليق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($review->user->name ?? 'غير معروف'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($review->course->title ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo e($i <= $review->rating ? 'text-yellow-400' : 'text-gray-300'); ?>"></i>
                                <?php endfor; ?>
                                <span class="mr-2 text-sm text-gray-600">(<?php echo e($review->rating); ?>)</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate"><?php echo e($review->comment ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $status = $review->status ?? ($review->is_approved ? 'approved' : 'pending');
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($status == 'approved' || $review->is_approved): ?> bg-green-100 text-green-800
                                <?php elseif($status == 'pending' || !$review->is_approved): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($status == 'rejected'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($status == 'approved' || $review->is_approved): ?> مقبولة
                                <?php elseif($status == 'pending' || !$review->is_approved): ?> معلقة
                                <?php elseif($status == 'rejected'): ?> مرفوضة
                                <?php else: ?> <?php echo e($status); ?>

                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($review->created_at->format('Y-m-d')); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('admin.reviews.show', $review)); ?>" class="text-sky-600 hover:text-sky-900">عرض</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($reviews->links()); ?>

        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-star-half-alt text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">لا توجد مراجعات</p>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/reviews/index.blade.php ENDPATH**/ ?>