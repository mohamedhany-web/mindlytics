<?php $__env->startSection('title', $group->name); ?>
<?php $__env->startSection('header', 'مجموعة: '.$group->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $selectedMemberIds = collect(old('member_ids', $group->members->pluck('id')->all() ?: [$group->assigned_to]));
?>
<div class="space-y-4">
    <?php if(session('success')): ?><div class="text-sm text-emerald-700"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="text-sm text-rose-700"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php echo $__env->make('admin.sales.groups._whatsapp_bulk', [
        'group' => $group,
        'leadsWithPhone' => $leadsWithPhone ?? collect(),
        'formAction' => route('admin.sales.groups.whatsapp.store', $group),
        'latestBatch' => $latestBatch ?? null,
        'latestBatchUrl' => isset($latestBatch) ? route('admin.whatsapp.batches.show', $latestBatch) : null,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($group->members->isNotEmpty() || $group->assigned_to): ?>
        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 flex flex-wrap items-center gap-2">
            <span class="text-sm font-bold text-sky-900"><i class="fas fa-chart-pie ml-1"></i> تقارير أداء الموظفين في هذه المجموعة:</span>
            <?php $__currentLoopData = ($group->members->isNotEmpty() ? $group->members : collect([$group->assignee])); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($member): ?>
                    <a href="<?php echo e(route('admin.sales.reports.employee', ['user_id' => $member->id, 'group_id' => $group->id, 'lead_scope' => 'in_groups'])); ?>"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-sky-300 text-sm font-semibold text-sky-800 hover:bg-sky-100">
                        <?php echo e($member->name); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('admin.sales.groups.update', $group)); ?>" class="bg-white border rounded-xl p-5 space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div>
            <label class="block text-sm font-medium mb-1">الاسم</label>
            <input type="text" name="name" value="<?php echo e(old('name', $group->name)); ?>" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">موظفو المبيعات في المجموعة</label>
            <div class="max-h-48 overflow-y-auto border rounded-lg p-3 space-y-2">
                <?php $__currentLoopData = $reps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="member_ids[]" value="<?php echo e($rep->id); ?>" class="rounded"
                            <?php if($selectedMemberIds->contains($rep->id)): echo 'checked'; endif; ?>>
                        <span><?php echo e($rep->name); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['member_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <p class="text-xs text-slate-500 mt-1">كل موظف يرى عملاءه المسندين إليه داخل هذه المجموعة فقط.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">وصف</label>
            <input type="text" name="description" value="<?php echo e(old('description', $group->description)); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-bold mb-2">اختر العملاء (من محافظ الموظفين المحددين)</label>
            <div class="max-h-80 overflow-y-auto border rounded-lg p-3 space-y-1 text-sm">
                <?php $__currentLoopData = $availableLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="lead_ids[]" value="<?php echo e($lead->id); ?>" class="rounded"
                            <?php if(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id): echo 'checked'; endif; ?>>
                        <span><?php echo e($lead->name); ?></span>
                        <span class="text-slate-400 text-xs"><?php echo e($lead->phone); ?></span>
                        <?php if($lead->assignee): ?>
                            <span class="text-[10px] text-sky-700">(<?php echo e($lead->assignee->name); ?>)</span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <button type="submit" class="px-5 py-2 bg-slate-800 text-white rounded-lg font-semibold">حفظ</button>
    </form>

    <?php if($group->leads->isNotEmpty()): ?>
        <div class="bg-white border rounded-xl p-5">
            <h3 class="font-bold text-sm mb-2">عملاء المجموعة حالياً (<?php echo e($group->leads->count()); ?>)</h3>
            <ul class="text-sm space-y-1 max-h-48 overflow-y-auto">
                <?php $__currentLoopData = $group->leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex justify-between gap-2">
                        <span><?php echo e($lead->name); ?></span>
                        <span class="text-slate-500 text-xs"><?php echo e($lead->assignee->name ?? '—'); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('admin.sales.groups.destroy', $group)); ?>" onsubmit="return confirm('حذف المجموعة؟')">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button type="submit" class="text-rose-700 text-sm">حذف المجموعة</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\groups\show.blade.php ENDPATH**/ ?>