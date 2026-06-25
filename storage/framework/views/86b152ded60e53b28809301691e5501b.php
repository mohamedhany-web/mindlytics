<?php $__env->startSection('title', $group->name); ?>
<?php $__env->startSection('header', 'مجموعة: '.$group->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $selectedMemberIds = collect(old('member_ids', $group->members->pluck('id')->all() ?: [$group->assigned_to]));
?>
<div class="space-y-4">
    <?php if(session('success')): ?><div class="text-sm text-emerald-700"><?php echo e(session('success')); ?></div><?php endif; ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/sales/groups/show.blade.php ENDPATH**/ ?>