<?php $__env->startSection('title', 'المجموعات'); ?>
<?php $__env->startSection('header', 'المجموعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">المجموعات</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">المجموعات</h1>
                <p class="text-sm text-white/90 mt-1">عرض وإدارة جميع مجموعات الطلاب لكل المدربين (رقابة)</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.groups.create')); ?>" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة مجموعة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-xl text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total']); ?></p>
                    <p class="text-sm text-gray-500">إجمالي المجموعات</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-xl text-green-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['active']); ?></p>
                    <p class="text-sm text-gray-500">نشطة</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pause text-xl text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['inactive']); ?></p>
                    <p class="text-sm text-gray-500">غير نشطة</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-archive text-xl text-gray-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['archived']); ?></p>
                    <p class="text-sm text-gray-500">مؤرشفة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم المجموعة أو الوصف"
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الكورس</label>
                <select name="course_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الكورسات</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 40)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                <select name="instructor_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع المدربين</option>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($instructor->id); ?>" <?php echo e(request('instructor_id') == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الحالات</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>غير نشط</option>
                    <option value="archived" <?php echo e(request('status') == 'archived' ? 'selected' : ''); ?>>مؤرشف</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة المجموعات -->
    <?php if($groups->count() > 0): ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h4 class="text-lg font-bold text-gray-900">قائمة المجموعات</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المدرب</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">قائد المجموعة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الأعضاء</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $statusClass = $group->status == 'active' ? 'bg-green-100 text-green-800' : ($group->status == 'inactive' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                                $statusText = $group->status == 'active' ? 'نشط' : ($group->status == 'inactive' ? 'غير نشط' : 'مؤرشف');
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900"><?php echo e($group->name); ?></div>
                                    <?php if($group->description): ?>
                                        <div class="text-xs text-gray-500 mt-0.5 truncate max-w-[180px]"><?php echo e(Str::limit($group->description, 50)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 truncate max-w-[160px]" title="<?php echo e($group->course->title ?? ''); ?>"><?php echo e($group->course->title ?? '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo e($group->course->instructor->name ?? '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo e($group->leader->name ?? '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800"><?php echo e($group->members_count); ?> / <?php echo e($group->max_members); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo e(route('admin.groups.show', $group)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo e(route('admin.groups.edit', $group)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                <?php echo e($groups->withQueryString()->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد مجموعات</h3>
            <p class="text-gray-500 mb-6">لم يتم العثور على مجموعات تطابق معايير البحث، أو لم تُنشأ مجموعات بعد</p>
            <a href="<?php echo e(route('admin.groups.create')); ?>" 
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة مجموعة جديدة
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/groups/index.blade.php ENDPATH**/ ?>