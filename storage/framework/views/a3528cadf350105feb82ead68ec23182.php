

<?php $__env->startSection('title', 'واجبات: ' . $course->title); ?>
<?php $__env->startSection('header', 'واجبات الكورس'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.assignments.index')); ?>" class="hover:text-white">الواجبات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white"><?php echo e(Str::limit($course->title, 40)); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1"><?php echo e($course->title); ?></h1>
                <p class="text-sm text-white/90 mt-1">إدارة واجبات هذا الكورس — عرض، إضافة، تعديل، حذف، تسليمات</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.assignments.index')); ?>"
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    كل الكورسات
                </a>
                <a href="<?php echo e(route('admin.assignments.create', ['course_id' => $course->id])); ?>"
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة واجب
                </a>
            </div>
        </div>
    </div>

    <!-- قائمة الواجبات -->
    <?php if($assignments->count() > 0): ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
                <h4 class="text-lg font-bold text-gray-900">الواجبات (<?php echo e($assignments->total()); ?>)</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الدرس</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الاستحقاق</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">التسليمات</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $statusClass = $assignment->status == 'published' ? 'bg-green-100 text-green-800' : ($assignment->status == 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                                $statusText = $assignment->status == 'published' ? 'منشور' : ($assignment->status == 'draft' ? 'مسودة' : 'مؤرشف');
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900"><?php echo e($assignment->title); ?></div>
                                    <?php if($assignment->description): ?>
                                        <div class="text-xs text-gray-500 mt-0.5 line-clamp-1"><?php echo e(Str::limit($assignment->description, 50)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($assignment->lesson ? Str::limit($assignment->lesson->title, 25) : '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-800"><?php echo e($assignment->submissions_count); ?> تسليم</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="<?php echo e(route('admin.assignments.show', $assignment)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo e(route('admin.assignments.submissions', $assignment)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 hover:bg-cyan-100 transition-colors" title="التسليمات"><i class="fas fa-inbox"></i></a>
                                        <a href="<?php echo e(route('admin.assignments.edit', $assignment)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                        <form action="<?php echo e(route('admin.assignments.destroy', $assignment)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الواجب؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                <?php echo e($assignments->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-tasks"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد واجبات في هذا الكورس</h3>
            <p class="text-gray-500 mb-6">يمكنك إضافة أول واجب لهذا الكورس</p>
            <a href="<?php echo e(route('admin.assignments.create', ['course_id' => $course->id])); ?>"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة واجب
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/assignments/by-course.blade.php ENDPATH**/ ?>