<?php $__env->startSection('title', 'تفاصيل الإشعار'); ?>
<?php $__env->startSection('header', 'تفاصيل الإشعار'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <a href="<?php echo e(route('employee.notifications')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
        <i class="fas fa-arrow-right"></i>
        رجوع إلى الإشعارات
    </a>

    <div class="bg-white shadow-lg rounded-xl border-2 <?php echo e($notification->is_read ? 'border-gray-200' : 'border-blue-300 bg-blue-50'); ?> overflow-hidden">
        <div class="p-8">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-<?php echo e($notification->type === 'task' ? 'tasks' : ($notification->type === 'leave' ? 'calendar' : 'bell')); ?> text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2"><?php echo e($notification->title); ?></h2>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-clock"></i>
                                <?php echo e($notification->created_at->format('Y-m-d H:i')); ?>

                            </span>
                            <?php if($notification->sender): ?>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user"></i>
                                    <?php echo e($notification->sender->name); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if(!$notification->is_read): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">جديد</span>
                <?php endif; ?>
            </div>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-700 text-lg leading-relaxed"><?php echo e($notification->message); ?></p>
            </div>

            <?php if($notification->action_url): ?>
                <div class="mt-6">
                    <a href="<?php echo e(route('employee.notifications.go', $notification)); ?>" 
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-colors">
                        <i class="fas fa-external-link-alt"></i>
                        <?php echo e($notification->action_text ?? 'عرض التفاصيل'); ?>

                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/notifications/show.blade.php ENDPATH**/ ?>