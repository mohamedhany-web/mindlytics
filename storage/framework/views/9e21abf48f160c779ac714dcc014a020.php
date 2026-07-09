<?php $__env->startSection('title', $notification->title); ?>
<?php $__env->startSection('header', __('student.notification_details')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-primary-600"><?php echo e(__('student.dashboard')); ?></a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('notifications')); ?>" class="hover:text-primary-600"><?php echo e(__('student.notifications')); ?></a>
                <span class="mx-2">/</span>
                <span><?php echo e($notification->title); ?></span>
            </nav>
        </div>
        <a href="<?php echo e(route('notifications')); ?>" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            <?php echo e(__('student.back_to_notifications')); ?>

        </a>
    </div>

    <!-- محتوى الإشعار -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <!-- هيدر الإشعار -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- أيقونة النوع -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center
                            <?php if($notification->type_color == 'blue'): ?> bg-blue-100
                            <?php elseif($notification->type_color == 'green'): ?> bg-green-100
                            <?php elseif($notification->type_color == 'yellow'): ?> bg-yellow-100
                            <?php elseif($notification->type_color == 'red'): ?> bg-red-100
                            <?php elseif($notification->type_color == 'purple'): ?> bg-purple-100
                            <?php elseif($notification->type_color == 'orange'): ?> bg-orange-100
                            <?php else: ?> bg-gray-100
                            <?php endif; ?>">
                            <i class="<?php echo e($notification->type_icon); ?> 
                                <?php if($notification->type_color == 'blue'): ?> text-blue-600
                                <?php elseif($notification->type_color == 'green'): ?> text-green-600
                                <?php elseif($notification->type_color == 'yellow'): ?> text-yellow-600
                                <?php elseif($notification->type_color == 'red'): ?> text-red-600
                                <?php elseif($notification->type_color == 'purple'): ?> text-purple-600
                                <?php elseif($notification->type_color == 'orange'): ?> text-orange-600
                                <?php else: ?> text-gray-600
                                <?php endif; ?> text-xl"></i>
                        </div>

                        <!-- العنوان والحالة -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($notification->title); ?></h1>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    <?php if($notification->type_color == 'blue'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($notification->type_color == 'green'): ?> bg-green-100 text-green-800
                                    <?php elseif($notification->type_color == 'yellow'): ?> bg-yellow-100 text-yellow-800
                                    <?php elseif($notification->type_color == 'red'): ?> bg-red-100 text-red-800
                                    <?php elseif($notification->type_color == 'purple'): ?> bg-purple-100 text-purple-800
                                    <?php elseif($notification->type_color == 'orange'): ?> bg-orange-100 text-orange-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php echo e(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type); ?>

                                </span>

                                <?php if($notification->priority !== 'normal'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        <?php if($notification->priority_color == 'red'): ?> bg-red-100 text-red-800
                                        <?php elseif($notification->priority_color == 'yellow'): ?> bg-yellow-100 text-yellow-800
                                        <?php else: ?> bg-gray-100 text-gray-800
                                        <?php endif; ?>">
                                        <?php echo e(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority); ?>

                                    </span>
                                <?php endif; ?>

                                <?php if($notification->is_read): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-check ml-1"></i>
                                        مقروء
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-circle text-xs ml-1"></i>
                                        جديد
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محتوى الإشعار -->
                <div class="p-6">
                    <div class="prose max-w-none">
                        <div class="text-gray-900 text-lg leading-relaxed whitespace-pre-wrap"><?php echo e($notification->message); ?></div>
                    </div>

                    <!-- زر الإجراء -->
                    <?php if($notification->action_url && $notification->action_text): ?>
                        <div class="mt-8 p-6 bg-primary-50 border border-primary-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-primary-900 mb-1">إجراء مطلوب</h4>
                                    <p class="text-sm text-primary-700">انقر على الزر للمتابعة</p>
                                </div>
                                <a href="<?php echo e(route('notifications.go', $notification)); ?>" 
                                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    <?php echo e($notification->action_text); ?>

                                    <i class="fas fa-external-link-alt mr-2"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- بيانات إضافية -->
                    <?php if($notification->data): ?>
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">معلومات إضافية</h4>
                            <div class="text-sm text-gray-600">
                                <?php $__currentLoopData = $notification->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between py-1">
                                        <span class="font-medium"><?php echo e(ucfirst($key)); ?>:</span>
                                        <span><?php echo e(is_array($value) ? json_encode($value) : $value); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات الإشعار -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات الإشعار</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">المرسل</span>
                        <span class="text-sm text-gray-900"><?php echo e($notification->sender->name ?? 'النظام'); ?></span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">تاريخ الإرسال</span>
                        <span class="text-sm text-gray-900"><?php echo e($notification->created_at->format('Y-m-d H:i')); ?></span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">تاريخ القراءة</span>
                        <span class="text-sm text-gray-900">
                            <?php echo e($notification->read_at ? $notification->read_at->format('Y-m-d H:i') : 'لم يُقرأ بعد'); ?>

                        </span>
                    </div>

                    <?php if($notification->expires_at): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">ينتهي في</span>
                            <span class="text-sm <?php echo e($notification->isExpired() ? 'text-red-600' : 'text-gray-900'); ?>">
                                <?php echo e($notification->expires_at->format('Y-m-d H:i')); ?>

                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">الحالة</span>
                        <span class="text-sm font-medium <?php echo e($notification->is_read ? 'text-green-600' : 'text-blue-600'); ?>">
                            <?php echo e($notification->is_read ? 'مقروء' : 'جديد'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- إجراءات -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">إجراءات</h3>
                </div>
                <div class="p-6 space-y-3">
                    <?php if(!$notification->is_read): ?>
                        <button onclick="markAsRead()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-check ml-2"></i>
                            تحديد كمقروء
                        </button>
                    <?php endif; ?>
                    
                    <button onclick="deleteNotification()" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash ml-2"></i>
                        حذف الإشعار
                    </button>
                    
                    <a href="<?php echo e(route('notifications')); ?>" 
                       class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-2"></i>
                        جميع الإشعارات
                    </a>
                </div>
            </div>

            <!-- إشعارات أخرى -->
            <?php
                $otherNotifications = auth()->user()->customNotifications()
                                                 ->where(function ($q) { $q->whereNull('audience')->orWhere('audience', 'student'); })
                                                 ->where('id', '!=', $notification->id)
                                                 ->valid()
                                                 ->orderBy('created_at', 'desc')
                                                 ->take(5)
                                                 ->get();
            ?>

            <?php if($otherNotifications->count() > 0): ?>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">إشعارات أخرى</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <?php $__currentLoopData = $otherNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('notifications.show', $other)); ?>" 
                                   class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            <?php if($other->type_color == 'blue'): ?> bg-blue-100
                                            <?php elseif($other->type_color == 'green'): ?> bg-green-100
                                            <?php elseif($other->type_color == 'yellow'): ?> bg-yellow-100
                                            <?php elseif($other->type_color == 'red'): ?> bg-red-100
                                            <?php elseif($other->type_color == 'purple'): ?> bg-purple-100
                                            <?php elseif($other->type_color == 'orange'): ?> bg-orange-100
                                            <?php else: ?> bg-gray-100
                                            <?php endif; ?>">
                                            <i class="<?php echo e($other->type_icon); ?> text-sm
                                                <?php if($other->type_color == 'blue'): ?> text-blue-600
                                                <?php elseif($other->type_color == 'green'): ?> text-green-600
                                                <?php elseif($other->type_color == 'yellow'): ?> text-yellow-600
                                                <?php elseif($other->type_color == 'red'): ?> text-red-600
                                                <?php elseif($other->type_color == 'purple'): ?> text-purple-600
                                                <?php elseif($other->type_color == 'orange'): ?> text-orange-600
                                                <?php else: ?> text-gray-600
                                                <?php endif; ?>"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($other->title); ?></p>
                                                <?php if(!$other->is_read): ?>
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1"><?php echo e($other->created_at->diffForHumans()); ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead() {
    fetch(`<?php echo e(route('notifications.mark-read', $notification)); ?>`, {
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

function deleteNotification() {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`<?php echo e(route('notifications.destroy', $notification)); ?>`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?php echo e(route("notifications")); ?>';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\notifications\show.blade.php ENDPATH**/ ?>