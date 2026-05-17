

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">تفاصيل النشاط</h1>
                        <p class="text-sm text-gray-500 mt-1">عرض تفاصيل النشاط المسجل</p>
                    </div>
                    <a href="<?php echo e(route('admin.activity-log')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة إلى القائمة
                    </a>
                </div>
            </div>

            <!-- تفاصيل النشاط -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- معلومات أساسية -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            المعلومات الأساسية
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">نوع النشاط</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1
                                <?php if($activityLog->type == 'create'): ?> bg-green-100 text-green-800
                                <?php elseif($activityLog->type == 'update'): ?> bg-blue-100 text-blue-800
                                <?php elseif($activityLog->type == 'delete'): ?> bg-red-100 text-red-800
                                <?php elseif($activityLog->type == 'login'): ?> bg-purple-100 text-purple-800
                                <?php elseif($activityLog->type == 'logout'): ?> bg-gray-100 text-gray-800
                                <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                <?php switch($activityLog->type):
                                    case ('create'): ?>
                                        إنشاء
                                        <?php break; ?>
                                    <?php case ('update'): ?>
                                        تحديث
                                        <?php break; ?>
                                    <?php case ('delete'): ?>
                                        حذف
                                        <?php break; ?>
                                    <?php case ('login'): ?>
                                        تسجيل دخول
                                        <?php break; ?>
                                    <?php case ('logout'): ?>
                                        تسجيل خروج
                                        <?php break; ?>
                                    <?php default: ?>
                                        <?php echo e($activityLog->type); ?>

                                <?php endswitch; ?>
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">الوصف</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($activityLog->description); ?></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">التاريخ والوقت</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php echo e($activityLog->created_at->format('Y-m-d H:i:s')); ?>

                                <span class="text-gray-500">
                                    (<?php echo e($activityLog->created_at->diffForHumans()); ?>)
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- معلومات المستخدم -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
                            معلومات المستخدم
                        </h3>
                        
                        <?php if($activityLog->user): ?>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?php echo e($activityLog->user->name); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo e($activityLog->user->email); ?></p>
                                    <p class="text-xs text-gray-400">ID: <?php echo e($activityLog->user->id); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-gray-500">مستخدم غير معروف</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- معلومات النموذج -->
                <?php if($activityLog->model_type && $activityLog->model_id): ?>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات النموذج</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">نوع النموذج</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e(class_basename($activityLog->model_type)); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">معرف النموذج</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($activityLog->model_id); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- البيانات الإضافية -->
                <?php if($activityLog->data && is_array($activityLog->data) && count($activityLog->data) > 0): ?>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">البيانات الإضافية</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-800 whitespace-pre-wrap"><?php echo e(json_encode($activityLog->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <!-- معلومات تقنية -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات تقنية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block font-medium text-gray-700">عنوان IP</label>
                            <p class="text-gray-900"><?php echo e($activityLog->ip_address ?? 'غير متوفر'); ?></p>
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700">وكيل المستخدم</label>
                            <p class="text-gray-900 break-all"><?php echo e($activityLog->user_agent ?? 'غير متوفر'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>










<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/activity-log/show.blade.php ENDPATH**/ ?>