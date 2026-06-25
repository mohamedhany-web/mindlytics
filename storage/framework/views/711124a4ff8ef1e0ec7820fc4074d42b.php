<?php $__env->startSection('title', 'مهامي'); ?>
<?php $__env->startSection('header', 'مهامي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">معلقة</p>
                        <p class="text-3xl font-black text-yellow-700"><?php echo e($stats['pending']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">قيد التنفيذ</p>
                        <p class="text-3xl font-black text-blue-700"><?php echo e($stats['in_progress']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-spinner text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['completed']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-red-200/50 hover:border-red-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 50%, rgba(254, 226, 226, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">متأخرة</p>
                        <p class="text-3xl font-black text-red-700"><?php echo e($stats['overdue']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                    <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                    <option value="on_hold" <?php echo e(request('status') == 'on_hold' ? 'selected' : ''); ?>>معلقة مؤقتاً</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">الأولوية</label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأولويات</option>
                    <option value="urgent" <?php echo e(request('priority') == 'urgent' ? 'selected' : ''); ?>>عاجل</option>
                    <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>عالي</option>
                    <option value="medium" <?php echo e(request('priority') == 'medium' ? 'selected' : ''); ?>>متوسط</option>
                    <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>منخفض</option>
                </select>
            </div>

            <div>
                <label for="task_type" class="block text-sm font-medium text-gray-700 mb-2">نوع المهمة</label>
                <select name="task_type" id="task_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">الكل</option>
                    <option value="general" <?php echo e(request('task_type') == 'general' ? 'selected' : ''); ?>>مهمة عامة</option>
                    <option value="video_editing" <?php echo e(request('task_type') == 'video_editing' ? 'selected' : ''); ?>>مونتاج فيديو</option>
                    <option value="sales" <?php echo e(request('task_type') == 'sales' ? 'selected' : ''); ?>>مبيعات</option>
                    <option value="design" <?php echo e(request('task_type') == 'design' ? 'selected' : ''); ?>>تصميم</option>
                    <option value="design_moderator_delivery" <?php echo e(request('task_type') == 'design_moderator_delivery' ? 'selected' : ''); ?>>تسليم مشرف</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>بحث
                </button>
                <?php if(request()->hasAny(['status', 'priority', 'task_type'])): ?>
                    <a href="<?php echo e(route('employee.tasks.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- قائمة المهام -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <?php if($tasks->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المهمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكلف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموعد النهائي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($task->title); ?></div>
                                <span class="inline-flex mt-1 px-2 py-0.5 text-xs font-semibold rounded-full
                                    <?php if($task->task_type === 'video_editing'): ?> bg-violet-100 text-violet-800
                                    <?php elseif($task->task_type === 'sales'): ?> bg-emerald-100 text-emerald-800
                                    <?php elseif($task->task_type === 'design'): ?> bg-fuchsia-100 text-fuchsia-800
                                    <?php elseif($task->task_type === 'design_moderator_delivery'): ?> bg-indigo-100 text-indigo-800
                                    <?php else: ?> bg-slate-100 text-slate-700
                                    <?php endif; ?>"><?php echo e(\App\Models\EmployeeTask::taskTypeLabel($task->task_type)); ?></span>
                                <?php if($task->description): ?>
                                    <div class="text-sm text-gray-500 mt-1"><?php echo e(Str::limit($task->description, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($task->assigner->name); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?php if($task->priority === 'urgent'): ?> bg-red-100 text-red-800
                                    <?php elseif($task->priority === 'high'): ?> bg-orange-100 text-orange-800
                                    <?php elseif($task->priority === 'medium'): ?> bg-yellow-100 text-yellow-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php if($task->priority === 'urgent'): ?> عاجل
                                    <?php elseif($task->priority === 'high'): ?> عالي
                                    <?php elseif($task->priority === 'medium'): ?> متوسط
                                    <?php else: ?> منخفض
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($task->deadline): ?>
                                    <div class="text-sm text-gray-900 <?php echo e($task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600 font-semibold' : ''); ?>">
                                        <?php echo e($task->deadline->format('Y-m-d')); ?>

                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400">غير محدد</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'on_hold' => 'bg-orange-100 text-orange-800',
                                    ];
                                    $statusTexts = [
                                        'pending' => 'معلقة',
                                        'in_progress' => 'قيد التنفيذ',
                                        'completed' => 'مكتملة',
                                        'cancelled' => 'ملغاة',
                                        'on_hold' => 'معلقة مؤقتاً',
                                    ];
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($statusColors[$task->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e($statusTexts[$task->status] ?? $task->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($task->progress); ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600"><?php echo e($task->progress); ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo e(route('employee.tasks.show', $task)); ?>" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($tasks->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600">لا توجد مهام</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/tasks/index.blade.php ENDPATH**/ ?>