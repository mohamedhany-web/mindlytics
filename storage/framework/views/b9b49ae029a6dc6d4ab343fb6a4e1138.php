

<?php $__env->startSection('title', 'المهام - Mindlytics'); ?>
<?php $__env->startSection('header', 'المهام'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        [
            'label' => 'إجمالي المهام',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-list-check',
            'color' => 'text-sky-500 bg-sky-100/70',
            'description' => 'كل المهام المسجلة',
        ],
        [
            'label' => 'في الانتظار',
            'value' => number_format($stats['pending'] ?? 0),
            'icon' => 'fas fa-clock',
            'color' => 'text-amber-500 bg-amber-100/70',
            'description' => 'مهام قيد الانتظار',
        ],
        [
            'label' => 'قيد التنفيذ',
            'value' => number_format($stats['in_progress'] ?? 0),
            'icon' => 'fas fa-spinner',
            'color' => 'text-blue-500 bg-blue-100/70',
            'description' => 'مهام قيد التنفيذ',
        ],
        [
            'label' => 'مكتملة',
            'value' => number_format($stats['completed'] ?? 0),
            'icon' => 'fas fa-check-circle',
            'color' => 'text-emerald-500 bg-emerald-100/70',
            'description' => 'مهام مكتملة',
        ],
    ];
    
    $priorityBadges = [
        'urgent' => ['label' => 'عاجلة', 'classes' => 'bg-rose-100 text-rose-700'],
        'high' => ['label' => 'عالية', 'classes' => 'bg-amber-100 text-amber-700'],
        'medium' => ['label' => 'متوسطة', 'classes' => 'bg-sky-100 text-sky-700'],
        'low' => ['label' => 'منخفضة', 'classes' => 'bg-slate-100 text-slate-700'],
    ];
    
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'classes' => 'bg-blue-100 text-blue-700'],
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-rose-100 text-rose-700'],
    ];
?>

<div class="space-y-6 sm:space-y-10">
    <!-- Header Section -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">مهام المدربين</h2>
                <p class="text-sm text-slate-500 mt-2">إدارة المهام المسندة من الإدارة للمدربين</p>
            </div>
            <a href="<?php echo e(route('admin.tasks.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-xl shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                <i class="fas fa-plus"></i>
                إضافة مهمة جديدة
            </a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 flex flex-col gap-4 card-hover-effect">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500"><?php echo e($card['label']); ?></p>
                            <p class="mt-3 text-2xl font-bold text-slate-900"><?php echo e($card['value']); ?></p>
                        </div>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl <?php echo e($card['color']); ?>">
                            <i class="<?php echo e($card['icon']); ?> text-xl"></i>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="ابحث في المهام..."
                               class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">المدرب</label>
                    <select name="user_id" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع المدربين</option>
                        <?php $__currentLoopData = ($instructors ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($inst->id); ?>" <?php echo e(request('user_id') == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                        <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الأولوية</label>
                    <select name="priority" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الأولويات</option>
                        <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                        <option value="medium" <?php echo e(request('priority') == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                        <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>عالية</option>
                        <option value="urgent" <?php echo e(request('priority') == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Tasks List -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-sky-600"></i>
                قائمة المهام
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                        <th class="px-6 py-4 text-right">المهمة</th>
                        <th class="px-6 py-4 text-right">المستخدم</th>
                        <th class="px-6 py-4 text-right">الأولوية</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">تاريخ الاستحقاق</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900"><?php echo e($task->title); ?></div>
                            <?php if($task->description): ?>
                                <div class="text-xs text-slate-500 mt-1"><?php echo e(Str::limit($task->description, 50)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-sky-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                    <?php echo e(mb_substr($task->user->name, 0, 1, 'UTF-8')); ?>

                                </div>
                                <span><?php echo e($task->user->name); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php $priorityBadge = $priorityBadges[$task->priority] ?? null; ?>
                            <?php if($priorityBadge): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($priorityBadge['classes']); ?>">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    <?php echo e($priorityBadge['label']); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php $statusBadge = $statusBadges[$task->status] ?? null; ?>
                            <?php if($statusBadge): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusBadge['classes']); ?>">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    <?php echo e($statusBadge['label']); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                            <?php if($task->due_date): ?>
                                <div class="font-medium"><?php echo e($task->due_date->format('Y-m-d')); ?></div>
                                <?php if($task->due_date->isPast() && $task->status != 'completed'): ?>
                                    <span class="text-xs text-rose-600">متأخرة</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('admin.tasks.show', $task)); ?>" 
                                   class="w-9 h-9 flex items-center justify-center bg-sky-50 hover:bg-sky-100 text-sky-600 rounded-xl transition-colors"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('admin.tasks.edit', $task)); ?>" 
                                   class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl transition-colors"
                                   title="تعديل">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-list-check text-slate-400 text-2xl"></i>
                                </div>
                                <p class="font-medium">لا توجد مهام</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($tasks->hasPages()): ?>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            <?php echo e($tasks->appends(request()->query())->links()); ?>

        </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/tasks/index.blade.php ENDPATH**/ ?>