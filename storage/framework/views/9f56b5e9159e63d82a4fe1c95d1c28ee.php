<?php $__env->startSection('title', 'التقرير اليومي'); ?>
<?php $__env->startSection('header', 'التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-semibold"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">التقرير اليومي الإلزامي</h1>
            <p class="text-sm text-gray-600 mt-1">قسمان: نشاط اليوم + إنتاجية المكالمات والاجتماعات. التسليم يدخل في KPI — التأخير يُنشئ خصماً تلقائياً.</p>
        </div>
        <a href="<?php echo e(route('employee.sales.daily-reports.edit', ['date' => $date->toDateString()])); ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm">
            <i class="fas fa-pen"></i>
            <?php echo e($report?->isSubmitted() ? 'عرض التقرير' : 'تعبئة / تعديل التقرير'); ?>

        </a>
    </div>

    <?php if($date->isToday() && !($isWorkDayToday ?? true)): ?>
        <div class="rounded-2xl border-2 border-sky-200 bg-sky-50 px-5 py-4 text-sky-900 text-sm">
            <p class="font-bold"><i class="fas fa-umbrella-beach ml-1"></i> اليوم لا يُطلَب فيه تقرير يومي</p>
            <p class="mt-1">
                <?php if($isLeaveToday ?? false): ?>
                    لديك إجازة معتمدة اليوم.
                <?php elseif($isWeeklyOffToday ?? false): ?>
                    اليوم هو إجازتك الأسبوعية: <strong><?php echo e(auth()->user()->weeklyOffDayLabel()); ?></strong>.
                <?php else: ?>
                    هذا اليوم مستثنى من أيام العمل.
                <?php endif; ?>
            </p>
        </div>
    <?php elseif(($settings['enabled'] ?? true) && !$todaySubmitted && $date->isToday()): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 px-5 py-4 text-amber-900 text-sm">
            <p class="font-bold"><i class="fas fa-clock ml-1"></i> لم يُسلَّم تقرير اليوم بعد</p>
            <p class="mt-1">إجازتك الأسبوعية: <strong><?php echo e(auth()->user()->weeklyOffDayLabel() ?? 'عطلة نهاية الأسبوع'); ?></strong> — آخر موعد: <?php echo e($settings['deadline_time'] ?? '23:59'); ?> — عدم التسليم قد يُنشئ خصماً بقيمة <?php echo e(number_format($settings['penalty_amount'] ?? 50, 2)); ?> ج.م.</p>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <form method="get" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ</label>
                <input type="date" name="date" value="<?php echo e($date->toDateString()); ?>" max="<?php echo e(today()->toDateString()); ?>" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">عرض</button>
        </form>

        <?php if($report): ?>
            <div class="mt-6 grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="font-bold text-gray-900 mb-3"><i class="fas fa-bolt text-amber-500 ml-1"></i> نشاط اليوم</h2>
                    <dl class="text-sm space-y-1">
                        <div class="flex justify-between"><dt>ردود رسائل</dt><dd class="font-bold"><?php echo e($report->messages_replied ?? '—'); ?></dd></div>
                        <div class="flex justify-between"><dt>مؤهلون</dt><dd class="font-bold"><?php echo e($report->leads_qualified ?? '—'); ?></dd></div>
                        <div class="flex justify-between"><dt>حجوزات من Leads</dt><dd class="font-bold"><?php echo e($report->bookings_from_leads ?? '—'); ?></dd></div>
                    </dl>
                    <?php if($report->activity_notes): ?><p class="mt-2 text-xs text-gray-600"><?php echo e($report->activity_notes); ?></p><?php endif; ?>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 mb-3"><i class="fas fa-phone text-emerald-600 ml-1"></i> الإنتاجية</h2>
                    <dl class="text-sm space-y-1">
                        <div class="flex justify-between"><dt>أرقام</dt><dd class="font-bold"><?php echo e($report->numbers_worked ?? '—'); ?></dd></div>
                        <div class="flex justify-between"><dt>متابعات</dt><dd class="font-bold"><?php echo e($report->followups_done ?? '—'); ?></dd></div>
                        <div class="flex justify-between"><dt>مكالمات / اجتماعات / ردود</dt><dd class="font-bold"><?php echo e($report->calls_made ?? '—'); ?> / <?php echo e($report->meetings_held ?? '—'); ?> / <?php echo e($report->calls_answered ?? '—'); ?></dd></div>
                    </dl>
                </div>
            </div>
            <p class="mt-4 text-xs">
                الحالة:
                <?php if($report->isSubmitted()): ?>
                    <span class="text-emerald-700 font-bold">مسلّم <?php echo e($report->submitted_at?->format('Y-m-d H:i')); ?></span>
                <?php else: ?>
                    <span class="text-amber-700 font-bold">مسودة — أكمل الحقول ثم سلّم</span>
                <?php endif; ?>
                <?php if($report->auto_deduction_id): ?>
                    <span class="text-rose-700 font-bold mr-2">| تم تسجيل خصم تلقائي</span>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <p class="mt-4 text-sm text-gray-500">لا يوجد تقرير لهذا التاريخ.</p>
        <?php endif; ?>
    </section>

    <section class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
        <h2 class="px-5 py-3 font-bold text-gray-900 border-b bg-gray-50 text-sm">آخر التقارير</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-2 text-right">التاريخ</th>
                        <th class="px-4 py-2 text-right">الحالة</th>
                        <th class="px-4 py-2 text-right">مكالمات</th>
                        <th class="px-4 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo e($r->report_date->format('Y-m-d')); ?></td>
                            <td class="px-4 py-2"><?php echo e($r->isSubmitted() ? 'مسلّم' : 'مسودة'); ?></td>
                            <td class="px-4 py-2"><?php echo e($r->calls_made ?? '—'); ?></td>
                            <td class="px-4 py-2"><a href="<?php echo e(route('employee.sales.daily-reports.edit', ['date' => $r->report_date->toDateString()])); ?>" class="text-emerald-700 font-semibold">فتح</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد تقارير بعد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\daily-reports\index.blade.php ENDPATH**/ ?>