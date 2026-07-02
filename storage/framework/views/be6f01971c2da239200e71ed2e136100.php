

<?php $__env->startSection('title', $program->name); ?>
<?php $__env->startSection('header', 'تفاصيل المنحة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'programs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._page-header', [
        'title' => $program->name,
        'subtitle' => 'المدرب: ' . ($program->instructor?->name ?? '—') . ' | الكورس: ' . ($program->course?->title ?? '—'),
        'icon' => 'fas fa-award',
        'actions' => '
            <a href="' . route('admin.scholarships.programs.edit', $program) . '" class="' . $schBtnSecondary . '"><i class="fas fa-edit"></i> تعديل</a>
            <button type="button" onclick="navigator.clipboard.writeText(' . json_encode($program->registrationUrl()) . '); alert(\'تم نسخ رابط التسجيل\');" class="' . $schBtnPrimary . '"><i class="fas fa-link"></i> نسخ الرابط</button>
        ',
        'statCards' => [
            ['label' => 'مسجّل', 'value' => number_format($program->registrations_count), 'icon' => 'fas fa-users', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'بانتظار التفعيل', 'value' => number_format($program->pending_count), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
            ['label' => 'مفعّل', 'value' => number_format($program->activated_count), 'icon' => 'fas fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'مرفوض', 'value' => number_format($program->rejected_count), 'icon' => 'fas fa-user-times', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="p-5 space-y-3">
            <?php if($program->description): ?>
                <p class="text-sm text-slate-700"><?php echo e($program->description); ?></p>
            <?php endif; ?>
            <div class="rounded-xl bg-violet-50 border border-violet-200 p-3 text-sm break-all" dir="ltr"><?php echo e($program->registrationUrl()); ?></div>
            <div class="flex flex-wrap gap-2">
                <?php if($program->course): ?>
                    <a href="<?php echo e(route('admin.scholarships.courses.show', $program->course)); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-book"></i> كورس المنحة</a>
                <?php endif; ?>
                <?php if($program->instructor): ?>
                    <a href="<?php echo e(route('admin.scholarships.instructors.show', $program->instructor)); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-chalkboard-teacher"></i> المدرب</a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.scholarships.students.index', ['program_id' => $program->id])); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-user-graduate"></i> كل الطلاب</a>
            </div>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-slate-900">المسجّلون في هذه المنحة</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">البريد</th>
                        <th class="px-4 py-3 text-right">الهاتف</th>
                        <th class="px-4 py-3 text-center">الحالة</th>
                        <th class="px-4 py-3 text-left">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold"><?php echo e($registration->user?->name); ?></td>
                            <td class="px-4 py-3"><?php echo e($registration->user?->email); ?></td>
                            <td class="px-4 py-3"><?php echo e($registration->user?->phone); ?></td>
                            <td class="px-4 py-3 text-center"><?php echo e($registration->status_label); ?></td>
                            <td class="px-4 py-3"><?php echo $__env->make('admin.scholarships._registration-actions', ['registration' => $registration], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا يوجد مسجّلون بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($registrations->hasPages()): ?><div class="p-4 border-t border-slate-200"><?php echo e($registrations->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/programs/show.blade.php ENDPATH**/ ?>