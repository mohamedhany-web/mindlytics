<?php $__env->startSection('title', 'طلاب المنح - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم المنح'); ?>

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

<div class="space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => 'طلاب المنح',
        'subtitle' => 'جميع التسجيلات عبر كل المنح — تفعيل، رفض، وإلغاء التفعيل',
        'icon' => 'fas fa-user-graduate',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'الإجمالي', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-users', 'description' => 'كل التسجيلات'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($stats['registered'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'description' => 'يحتاج موافقة'],
        ['label' => 'مفعّلون', 'value' => number_format($stats['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'وصول للكورس'],
        ['label' => 'مرفوضون', 'value' => number_format($stats['rejected'] ?? 0), 'icon' => 'fas fa-user-times', 'description' => 'تسجيلات مرفوضة'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._nav', ['active' => 'students'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-filter text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">ابحث وفلتر حسب المنحة والحالة</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" action="<?php echo e(route('admin.scholarships.students.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم / بريد / هاتف" class="<?php echo e($schInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-award text-blue-600 text-sm"></i> المنحة</label>
                    <select name="program_id" class="<?php echo e($schSelectClass); ?>">
                        <option value="">كل المنح</option>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->id); ?>" <?php if((string) request('program_id') === (string) $program->id): echo 'selected'; endif; ?>><?php echo e($program->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-toggle-on text-blue-600 text-sm"></i> الحالة</label>
                    <select name="status" class="<?php echo e($schSelectClass); ?>">
                        <option value="">كل الحالات</option>
                        <?php $__currentLoopData = \App\Models\ScholarshipRegistration::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if((string) request('status') === (string) $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 <?php echo e($schBtnPrimary); ?>"><i class="fas fa-search"></i><span>بحث</span></button>
                    <?php if(request()->anyFilled(['search', 'status', 'program_id'])): ?>
                        <a href="<?php echo e(route('admin.scholarships.students.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors" title="مسح الفلتر"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-user-graduate text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة الطلاب</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1"><span class="font-bold text-blue-600"><?php echo e($registrations->total()); ?></span> تسجيل</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-user text-blue-600"></i><span>الطالب</span></div></th>
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-award text-blue-600"></i><span>المنحة</span></div></th>
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-600"></i><span>المدرب</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-calendar text-blue-600"></i><span>التسجيل</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center gap-2 justify-center"><i class="fas fa-toggle-on text-blue-600"></i><span>الحالة</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-cog text-blue-600"></i><span>الإجراءات</span></div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="sch-avatar-gradient w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md"><?php echo e(mb_substr($registration->user?->name ?? '?', 0, 1, 'UTF-8')); ?></div>
                                    <div class="space-y-1">
                                        <p class="font-bold text-slate-900 text-base"><?php echo e($registration->user?->name); ?></p>
                                        <p class="text-xs text-slate-600 flex items-center gap-2"><i class="fas fa-envelope text-blue-500 text-xs"></i><?php echo e($registration->user?->email); ?></p>
                                        <p class="text-xs text-slate-600 flex items-center gap-2"><i class="fas fa-phone text-blue-500 text-xs"></i><?php echo e($registration->user?->phone ?: '—'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?php echo e(route('admin.scholarships.programs.show', $registration->program)); ?>" class="font-semibold text-slate-900 hover:text-blue-600 hover:underline"><?php echo e($registration->program?->name); ?></a>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium"><?php echo e($registration->program?->instructor?->name ?? '—'); ?></td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-semibold text-slate-900"><?php echo e($registration->registered_at?->format('Y-m-d')); ?></div>
                                <div class="text-xs text-slate-600"><?php echo e($registration->registered_at?->format('H:i')); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold <?php echo e($statusBadges[$registration->status] ?? 'bg-slate-100 text-slate-700 border border-slate-200'); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?php echo e($registration->status_label); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4"><?php echo $__env->make('admin.scholarships._registration-actions', ['registration' => $registration], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center"><i class="fas fa-user-graduate text-3xl text-blue-600"></i></div>
                                    <p class="font-bold text-slate-900 text-lg">لا يوجد طلاب مسجّلون</p>
                                    <p class="text-sm text-slate-600">جرب تغيير معايير البحث</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($registrations->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50"><?php echo e($registrations->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/scholarships/students/index.blade.php ENDPATH**/ ?>