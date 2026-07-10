

<?php $__env->startSection('title', 'حضور الفريق'); ?>
<?php $__env->startSection('header', 'حضور وغياب الفريق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusLabels = \App\Models\EmployeeAttendanceRecord::statusLabels();
    $modeLabels = [
        'working' => 'يعمل الآن',
        'manager_unlocked_working' => 'يعمل (مفتوح)',
        'manager_unlocked' => 'مفتوح — بانتظار الحضور',
        'awaiting_clock_in' => 'بانتظار تسجيل الحضور',
        'locked_before_shift' => 'قبل موعد العمل',
        'missed_shift' => 'فات الموعد',
        'completed' => 'انتهى اليوم',
        'off_day' => 'يوم راحة',
        'on_leave' => 'إجازة',
        'exempt' => 'غير خاضع',
        'no_schedule' => 'بدون موعد',
    ];
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

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'سجلات', 'value' => $stats['total']],
            ['label' => 'مكتمل', 'value' => $stats['completed']],
            ['label' => 'متأخر', 'value' => $stats['late']],
            ['label' => 'جاري العمل', 'value' => $stats['active_now']],
            ['label' => 'غياب', 'value' => $stats['absent']],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500"><?php echo e($s['label']); ?></p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e($s['value']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="rounded-2xl border border-teal-200 bg-gradient-to-l from-teal-50 via-white to-emerald-50/40 overflow-hidden">
        <div class="px-5 py-4 border-b border-teal-100 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-sm">
                        <i class="fas fa-unlock-alt"></i>
                    </span>
                    فتح النظام للموظفين
                </h3>
                <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    يمكنك فتح النظام لأي عضو في الفريق خارج موعد العمل أو في يوم راحته.
                    يُسجَّل السبب والمدة في سجل التدقيق، ويحتاج الموظف لتسجيل الحضور بعد الفتح.
                </p>
            </div>
            <?php if($activeUnlocks->isNotEmpty()): ?>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-teal-600 text-white">
                    <?php echo e($activeUnlocks->count()); ?> فتح نشط
                </span>
            <?php endif; ?>
        </div>

        <?php if($activeUnlocks->isNotEmpty()): ?>
            <div class="px-5 py-3 bg-white/70 border-b border-teal-100 space-y-2">
                <?php $__currentLoopData = $activeUnlocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-teal-100 bg-white px-3 py-2.5">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900"><?php echo e($u->user?->name); ?></p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                حتى <?php echo e($u->expires_at->format('H:i')); ?>

                                · <?php echo e($u->duration_label); ?>

                                · <?php echo e(\Illuminate\Support\Str::limit($u->reason, 60)); ?>

                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $u->user_id)); ?>"
                               class="text-[11px] font-semibold text-teal-700 hover:underline">التفاصيل</a>
                            <form method="POST" action="<?php echo e(route('employee.sales-manager.attendance.unlock.revoke', [$u->user_id, $u])); ?>"
                                  onsubmit="return confirm('إلغاء فتح النظام لهذا الموظف؟');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">
                                    إلغاء
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <div class="p-5">
            <form method="POST" action="#" id="wa-unlock-form" class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end"
                  onsubmit="this.action = this.dataset.base.replace('__ID__', this.employee_id.value);">
                <?php echo csrf_field(); ?>
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">الموظف</label>
                    <select name="employee_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white"
                            onchange="document.getElementById('wa-unlock-form').dataset.base = '<?php echo e(url('/employee/sales-manager/attendance/employees')); ?>/__ID__/unlock'">
                        <option value="">اختر موظفاً...</option>
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $st = $memberStates[$m->id] ?? []; ?>
                            <option value="<?php echo e($m->id); ?>">
                                <?php echo e($m->name); ?> — <?php echo e($modeLabels[$st['mode'] ?? ''] ?? ($st['mode'] ?? '—')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">مدة الفتح</label>
                    <select name="duration" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                        <?php $__currentLoopData = $durationOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if($key === 'end_of_day'): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">سبب الفتح</label>
                    <input type="text" name="reason" required minlength="5" maxlength="500"
                           value="<?php echo e(old('reason')); ?>"
                           placeholder="مثال: تغطية وردية / حملة عاجلة / يوم راحة بطلب الإدارة"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                </div>
                <div class="lg:col-span-2">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm">
                        <i class="fas fa-unlock-alt text-xs"></i>
                        فتح النظام
                    </button>
                </div>
            </form>
            <script>
                (function () {
                    var form = document.getElementById('wa-unlock-form');
                    form.dataset.base = '<?php echo e(url('/employee/sales-manager/attendance/employees')); ?>/__ID__/unlock';
                    form.addEventListener('submit', function (e) {
                        if (!form.employee_id.value) {
                            e.preventDefault();
                            alert('اختر موظفاً أولاً');
                            return;
                        }
                        form.action = form.dataset.base.replace('__ID__', form.employee_id.value);
                    });
                })();
            </script>
        </div>

        <div class="px-5 pb-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $st = $memberStates[$m->id] ?? [];
                        $mode = $st['mode'] ?? '';
                        $needsUnlock = in_array($mode, ['off_day', 'on_leave', 'locked_before_shift', 'missed_shift', 'completed'], true);
                        $hasUnlock = !empty($st['unlock']);
                    ?>
                    <div class="rounded-xl border bg-white px-3 py-3 flex items-start justify-between gap-2
                        <?php echo e($hasUnlock ? 'border-teal-300 ring-1 ring-teal-100' : 'border-slate-200'); ?>">
                        <div class="min-w-0">
                            <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $m)); ?>"
                               class="text-sm font-bold text-slate-900 hover:text-teal-700 truncate block"><?php echo e($m->name); ?></a>
                            <p class="text-[11px] mt-0.5 <?php echo e($needsUnlock ? 'text-amber-700' : 'text-slate-500'); ?>">
                                <?php echo e($modeLabels[$mode] ?? $mode); ?>

                            </p>
                            <?php if($hasUnlock): ?>
                                <p class="text-[10px] text-teal-700 mt-1 font-semibold">
                                    مفتوح حتى <?php echo e($st['unlock']['expires_at_human'] ?? '—'); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $m)); ?>"
                           class="shrink-0 text-[10px] font-bold px-2 py-1 rounded-lg border
                           <?php echo e($needsUnlock && !$hasUnlock ? 'bg-teal-50 border-teal-200 text-teal-800' : 'bg-slate-50 border-slate-200 text-slate-600'); ?>">
                            <?php echo e($hasUnlock ? 'إدارة' : ($needsUnlock ? 'فتح' : 'عرض')); ?>

                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <select name="employee_id" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                <option value="">كل الأعضاء</option>
                <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php if(request('employee_id') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-right">الموظف</th>
                    <th class="px-4 py-3 text-right">التاريخ</th>
                    <th class="px-4 py-3 text-right">دخول</th>
                    <th class="px-4 py-3 text-right">خروج</th>
                    <th class="px-4 py-3 text-right">ساعات</th>
                    <th class="px-4 py-3 text-right">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            <a href="<?php echo e(route('employee.sales-manager.attendance.employee', $rec->user_id)); ?>" class="hover:text-teal-700">
                                <?php echo e($rec->user->name ?? '—'); ?>

                            </a>
                        </td>
                        <td class="px-4 py-3"><?php echo e($rec->work_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_in_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->clock_out_at?->format('H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($rec->worked_minutes ? round($rec->worked_minutes / 60, 1) : '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($statusLabels[$rec->status] ?? $rec->status); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد سجلات.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($records->hasPages()): ?><div class="px-4 py-3 border-t"><?php echo e($records->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\attendance\index.blade.php ENDPATH**/ ?>