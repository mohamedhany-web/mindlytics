

<?php $__env->startSection('title', 'تحويل Leads'); ?>
<?php $__env->startSection('header', 'تحويل Leads بين الفريق'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
    <?php if(session('success')): ?>
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900">تحويل جميع Leads من موظف لآخر</h1>
        <p class="text-sm text-slate-500 mt-1">فريق: <?php echo e($team->name); ?></p>

        <form method="POST" action="<?php echo e(route('employee.sales-manager.transfer.store')); ?>" class="mt-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">من موظف</label>
                    <select name="from_user_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg" required>
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->user_id); ?>" <?php if($fromId == $m->user_id): echo 'selected'; endif; ?>><?php echo e($m->user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">إلى موظف</label>
                    <select name="to_user_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg" required>
                        <option value="">— اختر —</option>
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->user_id); ?>"><?php echo e($m->user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <?php if($stats): ?>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-sm">
                    <p class="font-semibold text-slate-800">إحصائيات <?php echo e($fromRep->name); ?>:</p>
                    <p class="mt-1">إجمالي Leads: <?php echo e($stats['leads_total']); ?></p>
                </div>
            <?php endif; ?>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="confirm" value="1" required class="rounded border-slate-300">
                أؤكد تحويل جميع Leads المسندة من الموظف الأول إلى الثاني
            </label>
            <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-semibold text-sm">تنفيذ التحويل</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\transfer\index.blade.php ENDPATH**/ ?>