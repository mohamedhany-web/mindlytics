<?php
    $lead = $act->resolvedLead ?? $act->salesLead;
    $assignee = $lead?->assignee;
    $formId = 'promo-sales-'.$act->id;
?>

<td class="px-4 py-3 whitespace-nowrap">
    <?php if($assignee): ?>
        <div class="font-semibold text-slate-800"><?php echo e($assignee->name); ?></div>
        <?php if($lead): ?>
            <a href="<?php echo e(route('admin.sales.leads.show', $lead)); ?>" class="text-xs text-blue-600 hover:underline">عرض Lead</a>
        <?php endif; ?>
    <?php else: ?>
        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-200">غير مسند</span>
    <?php endif; ?>
</td>
<td class="px-4 py-3 whitespace-nowrap text-slate-600">
    <?php if($lead?->next_follow_up_at): ?>
        <span class="<?php if($lead->isFollowUpOverdue()): ?> text-rose-600 font-semibold <?php endif; ?>">
            <?php echo e($lead->next_follow_up_at->format('Y-m-d H:i')); ?>

        </span>
    <?php else: ?>
        <span class="text-slate-400">—</span>
    <?php endif; ?>
</td>
<td class="px-4 py-3">
    <details class="group">
        <summary class="cursor-pointer list-none inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold
            <?php echo e($assignee ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-blue-600 text-white hover:bg-blue-700'); ?>">
            <i class="fas fa-user-plus text-[10px]"></i>
            <?php echo e($assignee ? 'إعادة إسناد / متابعة' : 'إسناد للمبيعات'); ?>

        </summary>
        <form method="POST" action="<?php echo e(route('admin.workshop-promo-activations.sales-task', $act)); ?>"
              class="mt-3 p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-2 min-w-[240px]">
            <?php echo csrf_field(); ?>
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">موظف المبيعات</label>
                <select name="assigned_to" required class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                    <option value="">— اختر —</option>
                    <?php $__currentLoopData = $salesReps ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rep->id); ?>" <?php if(old('assigned_to', $assignee?->id) == $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">متابعة يوم</label>
                <input type="datetime-local" name="next_follow_up_at" required
                       value="<?php echo e(old('next_follow_up_at', $lead?->next_follow_up_at?->format('Y-m-d\TH:i') ?: now()->addDay()->format('Y-m-d\TH:i'))); ?>"
                       class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
            </div>
            <?php if(($salesLeadGroups ?? collect())->isNotEmpty()): ?>
                <div>
                    <label class="text-[10px] font-bold text-slate-600 block mb-1">مجموعة (اختياري)</label>
                    <select name="sales_lead_group_id" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                        <option value="">بدون مجموعة</option>
                        <?php $__currentLoopData = $salesLeadGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($group->id); ?>" <?php if(old('sales_lead_group_id', $lead?->sales_lead_group_id) == $group->id): echo 'selected'; endif; ?>><?php echo e($group->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div>
                <label class="text-[10px] font-bold text-slate-600 block mb-1">ملاحظات المتابعة (اختياري)</label>
                <textarea name="task_notes" rows="2" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="تفاصيل للموظف…"><?php echo e(old('task_notes')); ?></textarea>
            </div>
            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold">
                <?php echo e($assignee ? 'تحديث الإسناد والمتابعة' : 'إسناد وإنشاء متابعة'); ?>

            </button>
        </form>
    </details>
</td>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshop-promo-codes\_activation_sales_cells.blade.php ENDPATH**/ ?>