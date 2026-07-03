<?php $__env->startSection('title', $course->title); ?>
<?php $__env->startSection('header', 'كورس المنحة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'courses'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => $course->title,
        'subtitle' => 'منحة: ' . ($course->scholarshipProgram?->name ?? '—') . ' | المدرب: ' . ($course->instructor?->name ?? '—'),
        'icon' => 'fas fa-book-open',
        'actions' => $course->scholarshipProgram ? '<a href="' . route('admin.scholarships.programs.show', $course->scholarshipProgram) . '" class="' . $schBtnSecondary . '"><i class="fas fa-award"></i><span>صفحة المنحة</span></a>' : null,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'طلاب مفعّلون', 'value' => number_format($course->active_enrollments_count ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'تسجيلات نشطة'],
        ['label' => 'حالة الكورس', 'value' => $course->is_active ? 'نشط' : 'متوقف', 'icon' => 'fas fa-power-off', 'description' => 'حالة النشر'],
        ['label' => 'السعر', 'value' => 'مجاني', 'icon' => 'fas fa-gift', 'description' => 'كورس منحة'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">الطلاب المفعّلون في هذا الكورس</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">البريد</th>
                        <th class="px-6 py-4 text-right">الهاتف</th>
                        <th class="px-6 py-4 text-center">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="sch-avatar-gradient w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md"><?php echo e(mb_substr($registration->user?->name ?? '?', 0, 1, 'UTF-8')); ?></div>
                                    <span class="font-bold text-slate-900"><?php echo e($registration->user?->name); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?php echo e($registration->user?->email); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo e($registration->user?->phone ?: '—'); ?></td>
                            <td class="px-6 py-4 text-center text-slate-700"><?php echo e($registration->activated_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">لا يوجد طلاب مفعّلون بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($registrations->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($registrations->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\courses\show.blade.php ENDPATH**/ ?>