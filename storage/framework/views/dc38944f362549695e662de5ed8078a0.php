

<?php $__env->startSection('title', 'إنشاء خطة تسويق'); ?>
<?php $__env->startSection('header', 'خطة تسويق جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl">
    <form method="post" action="<?php echo e(route('admin.moderator-marketing-plans.store')); ?>" class="rounded-2xl bg-white border p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-semibold mb-1">المشرف *</label>
            <select name="moderator_id" required class="w-full rounded-xl border px-3 py-2 text-sm">
                <?php $__currentLoopData = $moderators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php if(old('moderator_id') == $m->id): echo 'selected'; endif; ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">العنوان *</label>
            <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">ملخص</label>
            <textarea name="summary" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm"><?php echo e(old('summary')); ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">الأهداف</label>
            <textarea name="goals" rows="3" class="w-full rounded-xl border px-3 py-2 text-sm"><?php echo e(old('goals')); ?></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs mb-1">من</label><input type="date" name="start_date" value="<?php echo e(old('start_date')); ?>" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div><label class="block text-xs mb-1">إلى</label><input type="date" name="end_date" value="<?php echo e(old('end_date')); ?>" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">الحالة</label>
            <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                <?php $__currentLoopData = ['draft','active','paused','completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php if(old('status','active')===$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-pink-600 text-white rounded-xl font-semibold text-sm">إنشاء الخطة</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/moderator-marketing-plans/create.blade.php ENDPATH**/ ?>