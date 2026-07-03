<?php $__env->startSection('title', __('student.notifications_title')); ?>
<?php $__env->startSection('header', __('student.notifications_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo e(__('student.notifications_title')); ?></h1>
            <div class="flex items-center gap-2">
                <?php if($stats['unread'] > 0): ?>
                <button onclick="markAllAsRead()" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-check ml-2"></i> <?php echo e(__('student.mark_all_read')); ?>

                </button>
                <?php endif; ?>
                <button onclick="cleanup()" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-broom ml-2"></i> <?php echo e(__('student.cleanup_btn')); ?>

                </button>
            </div>
        </div>
        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="py-3 px-4 bg-gray-50 rounded-xl border border-gray-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.total_notifications')); ?></p>
                </div>
                <div class="py-3 px-4 bg-sky-50 rounded-xl border border-sky-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-sky-600"><?php echo e($stats['unread']); ?></p>
                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.unread_label')); ?></p>
                </div>
                <div class="py-3 px-4 bg-amber-50 rounded-xl border border-amber-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-amber-600"><?php echo e($stats['today']); ?></p>
                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.today_label')); ?></p>
                </div>
                <div class="py-3 px-4 bg-red-50 rounded-xl border border-red-100 text-center">
                    <p class="text-xl sm:text-2xl font-bold text-red-600"><?php echo e($stats['urgent']); ?></p>
                    <p class="text-xs font-medium text-gray-500"><?php echo e(__('student.urgent_label')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('student.notification_type_label')); ?></label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value=""><?php echo e(__('student.all_types')); ?></option>
                    <?php $__currentLoopData = $notificationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('common.status')); ?></label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value=""><?php echo e(__('student.all_statuses')); ?></option>
                    <option value="unread" <?php echo e(request('status') == 'unread' ? 'selected' : ''); ?>><?php echo e(__('student.unread_label')); ?></option>
                    <option value="read" <?php echo e(request('status') == 'read' ? 'selected' : ''); ?>><?php echo e(__('student.read_filter')); ?></option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('student.priority_label')); ?></label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value=""><?php echo e(__('student.all_priorities')); ?></option>
                    <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('priority') == $key ? 'selected' : ''); ?>><?php echo e($priority); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    <?php echo e(__('student.filter_btn')); ?>

                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الإشعارات -->
    <?php if($notifications->count() > 0): ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border <?php echo e(!$notification->is_read ? 'border-sky-200 border-r-4 border-r-sky-500' : 'border-gray-200'); ?> shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4 flex-1 flex-row-reverse">
                            <!-- أيقونة الإشعار -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center
                                    <?php if($notification->type_color == 'blue'): ?> bg-sky-100
                                    <?php elseif($notification->type_color == 'green'): ?> bg-emerald-100
                                    <?php elseif($notification->type_color == 'yellow'): ?> bg-amber-100
                                    <?php elseif($notification->type_color == 'red'): ?> bg-red-100
                                    <?php elseif($notification->type_color == 'purple'): ?> bg-violet-100
                                    <?php elseif($notification->type_color == 'orange'): ?> bg-amber-100
                                    <?php else: ?> bg-gray-100
                                    <?php endif; ?>">
                                    <i class="<?php echo e($notification->type_icon); ?> 
                                        <?php if($notification->type_color == 'blue'): ?> text-sky-600
                                        <?php elseif($notification->type_color == 'green'): ?> text-emerald-600
                                        <?php elseif($notification->type_color == 'yellow'): ?> text-amber-600
                                        <?php elseif($notification->type_color == 'red'): ?> text-red-600
                                        <?php elseif($notification->type_color == 'purple'): ?> text-violet-600
                                        <?php elseif($notification->type_color == 'orange'): ?> text-amber-600
                                        <?php else: ?> text-gray-600
                                        <?php endif; ?>"></i>
                                </div>
                            </div>
                            
                            <!-- محتوى الإشعار -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900"><?php echo e($notification->title); ?></h3>
                                    
                                    <?php if($notification->priority !== 'normal'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            <?php if($notification->priority_color == 'red'): ?> bg-red-100 text-red-800
                                            <?php elseif($notification->priority_color == 'yellow'): ?> bg-amber-100 text-amber-800
                                            <?php else: ?> bg-gray-100 text-gray-800
                                            <?php endif; ?>">
                                            <?php echo e($priorities[$notification->priority] ?? $notification->priority); ?>

                                        </span>
                                    <?php endif; ?>

                                    <?php if(!$notification->is_read): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-800">
                                            <i class="fas fa-circle text-[6px] ml-1"></i> جديد
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-gray-600 mb-3"><?php echo e($notification->message); ?></p>
                                
                                <div class="flex items-center gap-6 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-user ml-1"></i>
                                        من: <?php echo e($notification->sender->name ?? 'النظام'); ?>

                                    </span>
                                    
                                    <span class="flex items-center">
                                        <i class="fas fa-clock ml-1"></i>
                                        <?php echo e($notification->created_at->diffForHumans()); ?>

                                    </span>

                                    <?php if($notification->expires_at): ?>
                                        <span class="flex items-center">
                                            <i class="fas fa-hourglass-end ml-1"></i>
                                            ينتهي <?php echo e($notification->expires_at->diffForHumans()); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if($notification->action_url && $notification->action_text): ?>
                                    <div class="mt-4">
                                        <a href="<?php echo e(route('notifications.go', $notification)); ?>" class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-700 text-sm font-semibold transition-colors">
                                            <?php echo e($notification->action_text); ?> <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <?php if(!$notification->is_read): ?>
                            <button onclick="markAsRead(<?php echo e($notification->id); ?>)" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="تحديد كمقروء"><i class="fas fa-check"></i></button>
                            <?php endif; ?>
                            <a href="<?php echo e(route('notifications.show', $notification)); ?>" class="p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                            <button onclick="deleteNotification(<?php echo e($notification->id); ?>)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($notifications->hasPages()): ?>
        <div class="flex justify-center mt-6"><?php echo e($notifications->appends(request()->query())->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fas fa-bell-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد إشعارات</h3>
            <p class="text-sm text-gray-500">ستظهر هنا آخر التحديثات والرسائل المهمة</p>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function cleanup() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\notifications\index.blade.php ENDPATH**/ ?>