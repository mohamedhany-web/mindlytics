<?php $__env->startSection('title', 'دورة تصميم #'.$designTaskCycle->id); ?>
<?php $__env->startSection('header', 'دورة تصميم #'.$designTaskCycle->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 w-full">
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.design-task-cycles.index')); ?>" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm font-semibold">القائمة</a>
        <?php if($designTaskCycle->designerTask): ?>
            <a href="<?php echo e(route('admin.employee-tasks.show', $designTaskCycle->designerTask)); ?>" class="px-4 py-2 rounded-lg bg-sky-50 text-sky-800 text-sm font-semibold">مهمة المصمم</a>
        <?php endif; ?>
        <?php if($designTaskCycle->moderatorDeliveryTask): ?>
            <a href="<?php echo e(route('admin.employee-tasks.show', $designTaskCycle->moderatorDeliveryTask)); ?>" class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-800 text-sm font-semibold">مهمة تسليم المشرف</a>
        <?php endif; ?>
    </div>

    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-lg font-bold"><?php echo e($designTaskCycle->title); ?></h2>
            <span class="text-sm font-bold text-fuchsia-800"><?php echo e(\App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status)); ?></span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
            <div><dt class="text-gray-500">المشرف</dt><dd class="font-semibold"><?php echo e($designTaskCycle->moderator->name ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">المصمم</dt><dd class="font-semibold"><?php echo e($designTaskCycle->designer->name ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">حد تسليم المصمم</dt><dd><?php echo e($designTaskCycle->deadline_at?->format('Y-m-d H:i')); ?></dd></div>
            <div><dt class="text-gray-500">تسليم المصمم</dt><dd><?php echo e($designTaskCycle->designer_submitted_at?->format('Y-m-d H:i') ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">اكتمال الدورة</dt><dd><?php echo e($designTaskCycle->completed_at?->format('Y-m-d H:i') ?? '—'); ?></dd></div>
        </dl>
        <?php if($designTaskCycle->description): ?>
            <div><p class="text-xs text-gray-500 mb-1">الوصف</p><p class="text-gray-800 whitespace-pre-wrap"><?php echo e($designTaskCycle->description); ?></p></div>
        <?php endif; ?>
        <div><p class="text-xs text-gray-500 mb-1">المواصفات</p><p class="text-gray-800 whitespace-pre-wrap"><?php echo e($designTaskCycle->specifications); ?></p></div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold mb-3">ملاحظات الإدارة</h3>
        <form method="post" action="<?php echo e(route('admin.design-task-cycles.notes.update', $designTaskCycle)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <textarea name="admin_notes" rows="4" class="w-full rounded-lg border-gray-300 text-sm"><?php echo e(old('admin_notes', $designTaskCycle->admin_notes)); ?></textarea>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">حفظ</button>
        </form>
    </div>

    <?php if(! in_array($designTaskCycle->status, [\App\Models\DesignTaskCycle::STATUS_COMPLETED, \App\Models\DesignTaskCycle::STATUS_CANCELLED], true)): ?>
        <form method="post" action="<?php echo e(route('admin.design-task-cycles.cancel', $designTaskCycle)); ?>" onsubmit="return confirm('إلغاء الدورة؟');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-5 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold">إلغاء من الإدارة</button>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/design-task-cycles/show.blade.php ENDPATH**/ ?>