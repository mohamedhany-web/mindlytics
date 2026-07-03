<?php $__env->startSection('title', 'تفاصيل التسجيل'); ?>
<?php $__env->startSection('header', 'تفاصيل التسجيل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="hover:text-primary-600">التسجيلات</a>
                <span class="mx-2">/</span>
                <span>تفاصيل التسجيل</span>
            </nav>
        </div>
        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات التسجيل -->
        <div class="xl:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات التسجيل</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        <?php if($enrollment->status_color == 'yellow'): ?> bg-yellow-100 text-yellow-800
                        <?php elseif($enrollment->status_color == 'green'): ?> bg-green-100 text-green-800
                        <?php elseif($enrollment->status_color == 'blue'): ?> bg-blue-100 text-blue-800
                        <?php elseif($enrollment->status_color == 'red'): ?> bg-red-100 text-red-800
                        <?php else: ?> bg-gray-100 text-gray-800
                        <?php endif; ?>">
                        <?php echo e($enrollment->status_text); ?>

                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الطالب</label>
                                <div class="font-semibold text-gray-900"><?php echo e($enrollment->student->name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($enrollment->student->email); ?></div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                                <div class="text-gray-900"><?php echo e($enrollment->student->phone ?? 'غير محدد'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الكورس</label>
                                <div class="font-semibold text-gray-900"><?php echo e($enrollment->course->title); ?></div>
                                <div class="text-sm text-gray-500">
                                    <?php echo e($enrollment->course->academicYear->name ?? 'غير محدد'); ?> - 
                                    <?php echo e($enrollment->course->academicSubject->name ?? 'غير محدد'); ?>

                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">التقدم</label>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600"><?php echo e($enrollment->progress); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-primary-600 h-3 rounded-full transition-all duration-300" 
                                         style="width: <?php echo e($enrollment->progress); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التسجيل</label>
                                <div class="text-gray-900"><?php echo e($enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i') : 'غير محدد'); ?></div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التفعيل</label>
                                <div class="text-gray-900"><?php echo e($enrollment->activated_at ? $enrollment->activated_at->format('Y-m-d H:i') : 'غير مفعل'); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if($enrollment->activatedBy): ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">تم التفعيل بواسطة</label>
                            <div class="text-gray-900"><?php echo e($enrollment->activatedBy->name); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if($enrollment->notes): ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-gray-900"><?php echo e($enrollment->notes); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h4>
                </div>
                <div class="p-6 space-y-3">
                    <?php if($enrollment->status === 'pending'): ?>
                        <form action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" method="POST" class="mb-2">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')">
                                <i class="fas fa-check ml-1"></i>
                                تفعيل التسجيل
                            </button>
                        </form>
                    <?php elseif($enrollment->status === 'active'): ?>
                        <form action="<?php echo e(route('admin.online-enrollments.deactivate', $enrollment)); ?>" method="POST" class="mb-2">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إلغاء تفعيل هذا التسجيل؟')">
                                <i class="fas fa-pause ml-1"></i>
                                إلغاء التفعيل
                            </button>
                        </form>
                    <?php elseif($enrollment->status === 'suspended'): ?>
                        <form action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" method="POST" class="mb-2">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')">
                                <i class="fas fa-redo ml-1"></i>
                                إعادة التفعيل وفتح الكورس
                            </button>
                        </form>
                    <?php endif; ?>

                    <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-1"></i>
                        عرض جميع التسجيلات
                    </a>

                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">معلومات إضافية</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">ID التسجيل</span>
                        <span class="text-sm text-gray-900"><?php echo e($enrollment->id); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">تم الإنشاء</span>
                        <span class="text-sm text-gray-900"><?php echo e($enrollment->created_at->format('Y-m-d H:i')); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">آخر تحديث</span>
                        <span class="text-sm text-gray-900"><?php echo e($enrollment->updated_at->format('Y-m-d H:i')); ?></span>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الكورس -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إحصائيات الكورس</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-primary-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600"><?php echo e($enrollment->course->lessons->count()); ?></div>
                            <div class="text-sm text-gray-500">دروس</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600"><?php echo e($enrollment->course->duration_hours); ?></div>
                            <div class="text-sm text-gray-500">ساعة</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600"><?php echo e($enrollment->course->enrollments->where('status', 'active')->count()); ?></div>
                            <div class="text-sm text-gray-500">طالب مسجل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\online-enrollments\show.blade.php ENDPATH**/ ?>