<?php $__env->startSection('title', 'إعدادات خطط التسويق'); ?>
<?php $__env->startSection('header', 'إعدادات الأتمتة — خطط التسويق'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $reminderTime = $settings['reminder_time'] ?? '10:00';
    $deadlineTime = $settings['confirmation_deadline_time'] ?? '22:00';
?>
<div class="p-3 sm:p-4 md:p-6 space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 font-semibold text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-pink-50 to-violet-50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-violet-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">إعدادات أتمتة خطط التسويق</h2>
                    <p class="text-sm text-slate-600 mt-1">توجيه المهام · التذكير · غرامة عدم تأكيد الرفع · بدون مراقبة يدوية</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.moderator-marketing-plans.index')); ?>" class="text-sm text-slate-600 hover:text-pink-700 font-semibold">
                <i class="fas fa-arrow-right ml-1"></i> خطط التسويق
            </a>
        </div>

        <div class="p-6">
            <form method="post" action="<?php echo e(route('admin.moderator-marketing-plans.settings.update')); ?>" class="space-y-6 max-w-2xl">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer">
                        <input type="checkbox" name="automation_enabled" value="1" <?php if($settings['automation_enabled'] ?? true): echo 'checked'; endif; ?> class="mt-1 rounded border-slate-300 text-pink-600">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">تفعيل الأتمتة</span>
                            <span class="block text-xs text-slate-500 mt-0.5">توجيه المسؤول، إنشاء المهام، التذكير</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer">
                        <input type="checkbox" name="auto_create_tasks" value="1" <?php if($settings['auto_create_tasks'] ?? true): echo 'checked'; endif; ?> class="mt-1 rounded border-slate-300 text-violet-600">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">مهام تلقائية</span>
                            <span class="block text-xs text-slate-500 mt-0.5">تصميم → مصمم · مونتاج → مونتير</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50/40 p-4 cursor-pointer sm:col-span-2">
                        <input type="checkbox" name="penalty_enabled" value="1" <?php if($settings['penalty_enabled'] ?? true): echo 'checked'; endif; ?> class="mt-1 rounded border-rose-300 text-rose-600">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">غرامة عدم تأكيد التنفيذ</span>
                            <span class="block text-xs text-slate-500 mt-0.5">إذا لم يُؤكَّد رفع البوست/المحتوى المجدول في نفس اليوم</span>
                        </span>
                    </label>
                </div>

                <div class="rounded-xl border border-slate-200 p-4 space-y-4">
                    <h3 class="text-sm font-black text-slate-800"><i class="fas fa-coins text-amber-500 ml-1"></i> الغرامات والمواعيد</h3>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">مبلغ الغرامة (ج.م)</label>
                        <input type="number" step="0.01" min="0" name="penalty_amount" value="<?php echo e(old('penalty_amount', $settings['penalty_amount'] ?? 50)); ?>" class="w-full max-w-xs rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-pink-500">
                        <?php $__errorArgs = ['penalty_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">وقت التذكير اليومي</label>
                            <input type="time" name="reminder_time" value="<?php echo e(old('reminder_time', $reminderTime)); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد تطبيق الغرامة</label>
                            <input type="time" name="confirmation_deadline_time" value="<?php echo e(old('confirmation_deadline_time', $deadlineTime)); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        التذكير يُرسل يومياً الساعة <strong><?php echo e($reminderTime); ?></strong> —
                        الغرامات تُطبَّق الساعة <strong><?php echo e($deadlineTime); ?></strong> على أحداث اليوم غير المؤكدة.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-lg">
                        <i class="fas fa-save ml-1"></i> حفظ الإعدادات
                    </button>
                    <a href="<?php echo e(route('admin.moderator-marketing-plans.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">إلغاء</a>
                </div>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/moderator-marketing-plans/settings.blade.php ENDPATH**/ ?>