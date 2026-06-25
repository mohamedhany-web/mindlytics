

<?php $__env->startSection('title', 'تفاصيل الإشعار'); ?>
<?php $__env->startSection('header', 'تفاصيل الإشعار'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $priorities = \App\Models\Notification::getPriorities();
?>
<div class="space-y-6">
    <a href="<?php echo e(route('admin.employee-notifications.index')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
        <i class="fas fa-arrow-right"></i>
        رجوع إلى الإشعارات
    </a>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h2 class="text-2xl font-black text-slate-900"><?php echo e($notification->title); ?></h2>
        </div>
        <div class="p-8">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-user-tie text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                <?php if($notification->priority === 'urgent'): ?> bg-red-100 text-red-800
                                <?php elseif($notification->priority === 'high'): ?> bg-orange-100 text-orange-800
                                <?php elseif($notification->priority === 'normal'): ?> bg-blue-100 text-blue-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php echo e($priorities[$notification->priority] ?? $notification->priority); ?>

                            </span>
                            <?php if(!$notification->is_read): ?>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-amber-100 text-amber-800">غير مقروء</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-slate-600">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user"></i>
                                <?php echo e($notification->user->name ?? 'غير محدد'); ?>

                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-clock"></i>
                                <?php echo e($notification->created_at->format('Y-m-d H:i')); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-700 text-lg leading-relaxed whitespace-pre-wrap"><?php echo e($notification->message); ?></p>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/employee-notifications/show.blade.php ENDPATH**/ ?>