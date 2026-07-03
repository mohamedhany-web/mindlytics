

<?php $__env->startSection('title', $whatsappGroup->subject); ?>
<?php $__env->startSection('header', 'مجموعة واتساب: '.$whatsappGroup->subject); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $r = fn($name, ...$p) => route('employee.sales.whatsapp-groups.'.$name, ...$p); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <a href="<?php echo e($r('index')); ?>" class="text-sm text-slate-600 hover:underline">← مجموعات واتساب</a>
        <div class="flex flex-wrap gap-2">
            <form method="post" action="<?php echo e($r('sync', $whatsappGroup)); ?>"><?php echo csrf_field(); ?><button type="submit" class="px-3 py-1.5 text-xs border rounded-lg">مزامنة</button></form>
            <form method="post" action="<?php echo e($r('refresh-invite', $whatsappGroup)); ?>"><?php echo csrf_field(); ?><button type="submit" class="px-3 py-1.5 text-xs border rounded-lg">تحديث رابط الدعوة</button></form>
        </div>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 space-y-4">
            <form method="post" action="<?php echo e($r('update', $whatsappGroup)); ?>" class="sales-panel p-5 space-y-4">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <h3 class="font-bold text-slate-900">إعدادات المجموعة</h3>
                <div>
                    <label class="block text-sm font-medium mb-1">الاسم</label>
                    <input type="text" name="subject" value="<?php echo e(old('subject', $whatsappGroup->subject)); ?>" required class="w-full px-3 py-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">الوصف</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2.5 border rounded-lg"><?php echo e(old('description', $whatsappGroup->description)); ?></textarea>
                </div>
                <div class="flex flex-wrap gap-4 text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" name="announce_only" value="1" <?php if($whatsappGroup->announce_only): echo 'checked'; endif; ?>> رسائل للمشرفين فقط</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="restrict_info" value="1" <?php if($whatsappGroup->restrict_info): echo 'checked'; endif; ?>> إعدادات للمشرفين فقط</label>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold" <?php if(!$whatsappGroup->isActive()): echo 'disabled'; endif; ?>>حفظ على واتساب</button>
            </form>

            <div class="sales-panel p-5">
                <h3 class="font-bold text-slate-900 mb-3">الأعضاء (<?php echo e($whatsappGroup->participants->count()); ?>)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-500 text-xs"><tr><th class="text-right py-2">الاسم</th><th class="text-right py-2">الرقم</th><th class="text-right py-2">الحالة</th><th></th></tr></thead>
                        <tbody class="divide-y">
                            <?php $__currentLoopData = $whatsappGroup->participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="py-2"><?php echo e($p->display_name ?: $p->salesLead?->name ?: '—'); ?></td>
                                    <td class="py-2 dir-ltr text-left"><?php echo e($p->phone); ?></td>
                                    <td class="py-2"><?php echo e($p->statusLabel()); ?></td>
                                    <td class="py-2 text-left">
                                        <?php if($whatsappGroup->isActive() && $p->status !== 'removed'): ?>
                                            <form method="post" action="<?php echo e($r('participants.destroy', [$whatsappGroup, $p])); ?>" onsubmit="return confirm('إزالة العضو؟')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-rose-600 text-xs">إزالة</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="xl:col-span-5 space-y-4">
            <div class="sales-panel p-4 text-sm space-y-2">
                <p><span class="text-slate-500">الحالة:</span> <strong><?php echo e($whatsappGroup->statusLabel()); ?></strong></p>
                <?php if($whatsappGroup->invite_link): ?>
                    <p class="text-slate-500">رابط الدعوة:</p>
                    <input type="text" readonly value="<?php echo e($whatsappGroup->invite_link); ?>" class="w-full text-xs dir-ltr border rounded px-2 py-1" onclick="this.select()">
                <?php endif; ?>
                <?php if($whatsappGroup->wa_group_jid): ?>
                    <p class="text-[10px] text-slate-400 dir-ltr break-all"><?php echo e($whatsappGroup->wa_group_jid); ?></p>
                <?php endif; ?>
            </div>

            <?php if($whatsappGroup->isActive()): ?>
                <form method="post" action="<?php echo e($r('participants.store', $whatsappGroup)); ?>" class="sales-panel p-4 space-y-3">
                    <?php echo csrf_field(); ?>
                    <h3 class="font-bold text-sm">إضافة أرقام</h3>
                    <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="w-full px-3 py-2 border rounded-lg dir-ltr">
                    <input type="text" name="phones[]" placeholder="2011xxxxxxxx" class="w-full px-3 py-2 border rounded-lg dir-ltr">
                    <?php if($availableLeads->isNotEmpty()): ?>
                        <p class="text-xs font-semibold text-slate-600">أو من العملاء:</p>
                        <div class="max-h-40 overflow-y-auto border rounded divide-y text-sm">
                            <?php $__currentLoopData = $availableLeads->take(30); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer">
                                    <input type="checkbox" name="lead_ids[]" value="<?php echo e($lead->id); ?>" class="rounded">
                                    <span class="flex-1"><?php echo e($lead->name); ?></span>
                                    <span class="text-xs text-slate-500 dir-ltr"><?php echo e($lead->phone); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="w-full py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">إضافة للمجموعة</button>
                </form>

                <form method="post" action="<?php echo e($r('import-crm', $whatsappGroup)); ?>" class="sales-panel p-4 space-y-3">
                    <?php echo csrf_field(); ?>
                    <h3 class="font-bold text-sm">استيراد من مجموعة CRM</h3>
                    <select name="sales_lead_group_id" class="w-full px-3 py-2 border rounded-lg" required>
                        <?php $__currentLoopData = $crmGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($g->id); ?>" <?php if((int)$whatsappGroup->sales_lead_group_id === (int)$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="w-full py-2 border border-emerald-600 text-emerald-700 rounded-lg text-sm font-semibold">استيراد الأرقام</button>
                </form>

                <form method="post" action="<?php echo e($r('leave', $whatsappGroup)); ?>" onsubmit="return confirm('الخروج من المجموعة على واتساب؟')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full py-2 text-rose-700 border border-rose-200 rounded-lg text-sm">الخروج من المجموعة</button>
                </form>
            <?php endif; ?>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\show.blade.php ENDPATH**/ ?>