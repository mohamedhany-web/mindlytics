

<?php $__env->startSection('title', 'حضور '.$employee->name); ?>
<?php $__env->startSection('header', 'حضور '.$employee->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $modeLabels = [
        'working' => 'يعمل الآن',
        'manager_unlocked_working' => 'يعمل بتصريح فتح',
        'manager_unlocked' => 'مفتوح — بانتظار تسجيل الحضور',
        'awaiting_clock_in' => 'بانتظار تسجيل الحضور',
        'locked_before_shift' => 'قبل موعد العمل',
        'missed_shift' => 'فات موعد العمل',
        'completed' => 'انتهى يوم العمل',
        'off_day' => 'يوم راحة',
        'on_leave' => 'إجازة معتمدة',
        'exempt' => 'غير خاضع للمواعيد',
        'no_schedule' => 'بدون موعد عمل',
    ];
    $mode = $state['mode'] ?? '';
?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc pe-5 space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.sales-manager.attendance.index')); ?>" class="text-sm text-emerald-700 font-semibold">← العودة لحضور الفريق</a>
        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border
            <?php echo e(!empty($state['unlock']) ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600'); ?>">
            <?php echo e($modeLabels[$mode] ?? $mode); ?>

        </span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'أيام مكتملة', 'value' => $summary['completed_days']],
            ['label' => 'تأخير', 'value' => $summary['late_days']],
            ['label' => 'إجمالي ساعات', 'value' => $summary['total_hours']],
            ['label' => 'أيام نشطة', 'value' => $summary['active_days']],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500"><?php echo e($s['label']); ?></p>
                <p class="text-2xl font-bold"><?php echo e($s['value']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="rounded-2xl border border-teal-200 bg-white overflow-hidden shadow-sm">
        <div class="px-5 py-4 bg-gradient-to-l from-teal-50 to-white border-b border-teal-100">
            <h3 class="font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-unlock-alt text-teal-600"></i>
                فتح النظام لـ <?php echo e($employee->name); ?>

            </h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                يتيح للموظف الدخول والعمل خارج موعده أو في يوم راحته. يُطلب سبب ومدة، ويُحفظ في سجل التدقيق.
            </p>
        </div>

        <div class="p-5 space-y-4">
            <?php if($activeUnlock): ?>
                <div class="rounded-xl border border-teal-200 bg-teal-50/60 px-4 py-3">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-teal-900">فتح نشط حالياً</p>
                            <p class="text-xs text-teal-800 mt-1">
                                حتى <strong><?php echo e($activeUnlock->expires_at->format('Y-m-d H:i')); ?></strong>
                                (<?php echo e($activeUnlock->duration_label); ?>)
                            </p>
                            <p class="text-xs text-slate-600 mt-1">السبب: <?php echo e($activeUnlock->reason); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1">بواسطة: <?php echo e($activeUnlock->unlockedBy?->name); ?></p>
                        </div>
                        <form method="POST"
                              action="<?php echo e(route('employee.sales-manager.attendance.unlock.revoke', [$employee, $activeUnlock])); ?>"
                              onsubmit="return confirm('إلغاء فتح النظام؟ سيعود القفل حسب الموعد.');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-3 py-2 rounded-lg bg-white border border-rose-200 text-rose-700 text-xs font-bold hover:bg-rose-50">
                                إلغاء الفتح
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('employee.sales-manager.attendance.unlock', $employee)); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">مدة الفتح</label>
                    <select name="duration" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
                        <?php $__currentLoopData = $durationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(old('duration', 'end_of_day') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-6">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">سبب الفتح</label>
                    <input type="text" name="reason" required minlength="5" maxlength="500"
                           value="<?php echo e(old('reason')); ?>"
                           placeholder="مثال: تغطية وردية / حملة عاجلة / عمل في يوم الراحة"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm">
                </div>
                <div class="md:col-span-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">
                        <i class="fas fa-unlock-alt text-xs"></i>
                        <?php echo e($activeUnlock ? 'تجديد الفتح' : 'فتح النظام'); ?>

                    </button>
                </div>
            </form>

            <?php if($employee->workSchedule): ?>
                <p class="text-[11px] text-slate-500">
                    الموعد المخصص:
                    <?php echo e(substr((string) $employee->workSchedule->start_time, 0, 5)); ?>

                    —
                    <?php echo e(substr((string) $employee->workSchedule->end_time, 0, 5)); ?>

                    · <?php echo e($employee->workSchedule->name); ?>

                </p>
            <?php endif; ?>
        </div>
    </div>

    <?php if($unlockHistory->isNotEmpty()): ?>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-800">سجل فتح النظام</h4>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-right">من</th>
                        <th class="px-4 py-2 text-right">إلى</th>
                        <th class="px-4 py-2 text-right">المدة</th>
                        <th class="px-4 py-2 text-right">السبب</th>
                        <th class="px-4 py-2 text-right">بواسطة</th>
                        <th class="px-4 py-2 text-right">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $unlockHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo e($u->starts_at->format('m-d H:i')); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo e($u->expires_at->format('m-d H:i')); ?></td>
                            <td class="px-4 py-2"><?php echo e($u->duration_label); ?></td>
                            <td class="px-4 py-2 max-w-[14rem] truncate" title="<?php echo e($u->reason); ?>"><?php echo e($u->reason); ?></td>
                            <td class="px-4 py-2"><?php echo e($u->unlockedBy?->name); ?></td>
                            <td class="px-4 py-2">
                                <?php if($u->isActive()): ?>
                                    <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full">نشط</span>
                                <?php elseif($u->revoked_at): ?>
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">ملغى</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">منتهي</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="px-4 py-3 text-right">التاريخ</th>
                <th class="px-4 py-3 text-right">دخول</th>
                <th class="px-4 py-3 text-right">خروج</th>
                <th class="px-4 py-3 text-right">الحالة</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-4 py-3"><?php echo e($rec->work_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_in_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_out_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e(\App\Models\EmployeeAttendanceRecord::statusLabels()[$rec->status] ?? $rec->status); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php if($records->hasPages()): ?><div class="px-4 py-3"><?php echo e($records->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\attendance\employee.blade.php ENDPATH**/ ?>