

<?php $__env->startSection('title', $program->name); ?>
<?php $__env->startSection('header', 'تفاصيل المنحة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'activated' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'deactivated' => 'bg-slate-100 text-slate-700 border border-slate-200',
    ];
?>

<div class="w-full space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => $program->name,
        'subtitle' => 'المدرب: ' . ($program->instructor?->name ?? '—') . ' | الكورس: ' . ($program->course?->title ?? '—'),
        'icon' => 'fas fa-award',
        'actions' => '
            <a href="' . route('admin.scholarships.programs.edit', $program) . '" class="' . $schBtnSecondary . '"><i class="fas fa-edit"></i><span>تعديل</span></a>
            <button type="button" onclick="navigator.clipboard.writeText(' . json_encode($program->registrationUrl()) . '); alert(\'تم نسخ رابط التسجيل\');" class="' . $schBtnPrimary . '"><i class="fas fa-link"></i><span>نسخ الرابط</span></button>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'مسجّل', 'value' => number_format($program->registrations_count), 'icon' => 'fas fa-users', 'description' => 'إجمالي التسجيلات'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($program->pending_count), 'icon' => 'fas fa-hourglass-half', 'description' => 'يحتاج موافقة'],
        ['label' => 'مفعّل', 'value' => number_format($program->activated_count), 'icon' => 'fas fa-user-check', 'description' => 'وصول للكورس'],
        ['label' => 'مرفوض', 'value' => number_format($program->rejected_count), 'icon' => 'fas fa-user-times', 'description' => 'تسجيلات مرفوضة'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">معلومات المنحة</h3>
        </div>
        <div class="p-6 space-y-4">
            <?php if($program->description): ?>
                <p class="text-sm text-slate-700 leading-relaxed"><?php echo e($program->description); ?></p>
            <?php endif; ?>
            <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm break-all font-mono" dir="ltr"><?php echo e($program->registrationUrl()); ?></div>
            <div class="flex flex-wrap gap-2">
                <?php if($program->course): ?>
                    <a href="<?php echo e(route('admin.scholarships.courses.show', $program->course)); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-book"></i> كورس المنحة</a>
                <?php endif; ?>
                <?php if($program->instructor): ?>
                    <a href="<?php echo e(route('admin.scholarships.instructors.show', $program->instructor)); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-chalkboard-teacher"></i> المدرب</a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.scholarships.students.index', ['program_id' => $program->id])); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-user-graduate"></i> كل الطلاب</a>
                <a href="<?php echo e(route('admin.scholarships.groups.index', ['program_id' => $program->id])); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-layer-group"></i> المجموعات (<?php echo e($program->groups_count ?? ($groups->count() ?? 0)); ?>)</a>
            </div>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-black text-slate-900">مجموعات الطلبة</h3>
            <a href="<?php echo e(route('admin.scholarships.groups.index', ['program_id' => $program->id])); ?>" class="<?php echo e($schBtnSecondary); ?>"><i class="fas fa-external-link-alt"></i> إدارة المجموعات</a>
        </div>
        <div class="p-6">
            <?php if(($groups ?? collect())->isEmpty()): ?>
                <p class="text-sm text-slate-500 text-center py-6">لا توجد مجموعات لهذه المنحة بعد.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-bold text-slate-800 truncate"><?php echo e($group->name); ?></h4>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-semibold">
                                    <i class="fas fa-users"></i> <?php echo e($group->members_count); ?>

                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <?php $__empty_1 = true; $__currentLoopData = $group->members->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-700"><?php echo e($member->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-[11px] text-slate-400">لا أعضاء</span>
                                <?php endif; ?>
                                <?php if($group->members->count() > 6): ?>
                                    <span class="text-[11px] text-slate-500">+<?php echo e($group->members->count() - 6); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">المسجّلون في هذه المنحة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">البريد</th>
                        <th class="px-6 py-4 text-right">الهاتف</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="sch-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900"><?php echo e($registration->user?->name); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo e($registration->user?->email); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo e($registration->user?->phone ?: '—'); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold <?php echo e($statusBadges[$registration->status] ?? ''); ?>"><?php echo e($registration->status_label); ?></span>
                            </td>
                            <td class="px-6 py-4"><?php echo $__env->make('admin.scholarships._registration-actions', ['registration' => $registration], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">لا يوجد مسجّلون بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($registrations->hasPages()): ?><div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($registrations->links()); ?></div><?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\programs\show.blade.php ENDPATH**/ ?>