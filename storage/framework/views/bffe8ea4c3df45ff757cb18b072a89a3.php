

<?php $__env->startSection('title', 'تقرير يومي — ' . $date->format('Y-m-d')); ?>
<?php $__env->startSection('header', 'التقرير اليومي'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <a href="<?php echo e(route('employee.daily-reports.index')); ?>" class="text-sm text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>

    <form method="post" action="<?php echo e(route('employee.daily-reports.store')); ?>" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="report_date" value="<?php echo e($date->toDateString()); ?>">

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">تاريخ التقرير</label>
            <p class="text-lg font-bold text-gray-900"><?php echo e($date->format('Y-m-d')); ?> — <?php echo e($date->locale('ar')->translatedFormat('l')); ?></p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ملخص اليوم *</label>
            <textarea name="summary" rows="3" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('summary', $report->summary)); ?></textarea>
            <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">المهام المنجزة *</label>
            <textarea name="tasks_done" rows="5" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm" placeholder="اذكر كل ما أنجزته اليوم..."><?php echo e(old('tasks_done', $report->tasks_done)); ?></textarea>
            <?php $__errorArgs = ['tasks_done'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">خطة الغد</label>
            <textarea name="tomorrow_plan" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('tomorrow_plan', $report->tomorrow_plan)); ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">معوقات / ملاحظات</label>
            <textarea name="blockers" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('blockers', $report->blockers)); ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ساعات العمل</label>
            <input type="number" name="hours_worked" step="0.5" min="0" max="24" value="<?php echo e(old('hours_worked', $report->hours_worked)); ?>" class="w-32 rounded-xl border border-gray-300 px-3 py-2 text-sm">
        </div>

        <div class="flex flex-wrap gap-3 pt-2 border-t">
            <button type="submit" name="submit" value="0" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm">حفظ مسودة</button>
            <button type="submit" name="submit" value="1" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                <i class="fas fa-paper-plane ml-1"></i> إرسال التقرير
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\daily-reports\edit.blade.php ENDPATH**/ ?>