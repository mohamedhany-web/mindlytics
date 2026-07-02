

<?php $__env->startSection('title', 'تفاصيل الخصم - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل الخصم'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900"><?php echo e($employeeDeduction->deduction_number); ?></h2>
                    <p class="text-sm text-slate-600 mt-1"><?php echo e($employeeDeduction->title); ?></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.employee-deductions.edit', $employeeDeduction)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold text-sm">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="<?php echo e(route('admin.employee-deductions.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الموظف</p>
                    <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeDeduction->employee->name ?? '—'); ?></p>
                    <?php if($employeeDeduction->employee): ?>
                        <p class="text-xs text-slate-500"><?php echo e($employeeDeduction->employee->email); ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الاتفاقية</p>
                    <?php if($employeeDeduction->agreement): ?>
                        <a href="<?php echo e(route('admin.employee-agreements.show', $employeeDeduction->agreement)); ?>" class="text-sm font-semibold text-rose-600 hover:underline"><?php echo e($employeeDeduction->agreement->agreement_number); ?></a>
                        <p class="text-xs text-slate-500"><?php echo e($employeeDeduction->agreement->title); ?></p>
                    <?php else: ?>
                        <p class="text-sm text-slate-500">—</p>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">نوع الخصم</p>
                    <?php $typeLabels = ['tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض', 'penalty' => 'غرامة', 'other' => 'أخرى']; ?>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-800"><?php echo e($typeLabels[$employeeDeduction->type] ?? $employeeDeduction->type); ?></span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">المبلغ</p>
                    <p class="text-xl font-bold text-rose-600"><?php echo e(number_format($employeeDeduction->amount, 2)); ?> ج.م</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الخصم</p>
                    <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeDeduction->deduction_date?->format('Y-m-d')); ?></p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                    <?php if($employeeDeduction->status === 'pending'): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">معلقة</span>
                    <?php elseif($employeeDeduction->status === 'applied'): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">مطبقة</span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-slate-100 text-slate-800">ملغاة</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($employeeDeduction->description): ?>
                <div class="mt-6 pt-4 border-t border-slate-200">
                    <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($employeeDeduction->description); ?></p>
                </div>
            <?php endif; ?>
            <?php if($employeeDeduction->notes): ?>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-500 mb-1">ملاحظات</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($employeeDeduction->notes); ?></p>
                </div>
            <?php endif; ?>
            <?php if($employeeDeduction->creator): ?>
                <div class="mt-4 pt-4 border-t border-slate-200 text-xs text-slate-500">
                    أنشئ بواسطة: <?php echo e($employeeDeduction->creator->name); ?> — <?php echo e($employeeDeduction->created_at?->format('Y-m-d H:i')); ?>

                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-deductions\show.blade.php ENDPATH**/ ?>