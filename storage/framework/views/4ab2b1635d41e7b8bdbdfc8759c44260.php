

<?php $__env->startSection('title', $lead->name); ?>
<?php $__env->startSection('header', 'تفاصيل العميل'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl">
    <?php if(session('success')): ?>
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900"><?php echo e($lead->name); ?></h1>
                <p class="text-sm text-slate-500 mt-1">مسند إلى: <strong><?php echo e($lead->assignee->name ?? '—'); ?></strong></p>
                <p class="text-sm text-slate-500">المرحلة: <?php echo e(\App\Models\SalesLead::STAGES[$lead->stage] ?? $lead->stage); ?></p>
                <?php if($lead->phone): ?><p class="text-sm text-slate-600 mt-2"><i class="fas fa-phone ml-1"></i> <?php echo e($lead->phone); ?></p><?php endif; ?>
                <?php if($lead->email): ?><p class="text-sm text-slate-600"><i class="fas fa-envelope ml-1"></i> <?php echo e($lead->email); ?></p><?php endif; ?>
            </div>
            <a href="<?php echo e(route('employee.sales-manager.leads.index')); ?>" class="text-sm text-slate-600 hover:text-slate-900">← العودة للقائمة</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h2 class="font-bold text-slate-900 mb-4">تحويل Lead لعضو آخر في الفريق</h2>
        <form method="POST" action="<?php echo e(route('employee.sales-manager.leads.transfer', $lead)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">تحويل إلى</label>
                <select name="to_user_id" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if((int)$m->user_id !== (int)$lead->assigned_to): ?>
                            <option value="<?php echo e($m->user_id); ?>"><?php echo e($m->user->name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">سبب التحويل (اختياري)</label>
                <textarea name="reason" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"><?php echo e(old('reason')); ?></textarea>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold" onclick="return confirm('تأكيد تحويل هذا العميل؟')">تحويل الآن</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\leads\show.blade.php ENDPATH**/ ?>