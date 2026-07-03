<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <div class="xl:col-span-8 space-y-4">
        <form method="post" action="<?php echo e($r('update', $whatsappGroup)); ?>" class="sales-panel p-5 md:p-6 space-y-4">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <p class="wa-section-title">إعدادات المجموعة</p>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">الاسم</label>
                    <input type="text" name="subject" value="<?php echo e(old('subject', $whatsappGroup->subject)); ?>" required class="px-3 py-2.5">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="2" class="px-3 py-2.5"><?php echo e(old('description', $whatsappGroup->description)); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">موافقة الانضمام</label>
                    <p class="text-sm font-semibold text-slate-800"><?php echo e($whatsappGroup->join_approval_mode === 'approval_required' ? 'يتطلب موافقة' : 'تلقائي'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-500 mb-1">المزود</label>
                    <p class="text-sm font-semibold text-slate-800">Meta Cloud API</p>
                </div>
            </div>
            <button type="submit" class="btn-wa-primary" <?php if(!$whatsappGroup->isActive()): echo 'disabled'; endif; ?>>
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </form>

        <div class="sales-panel p-5 md:p-6">
            <p class="wa-section-title">المدعوون والمنضمون (<?php echo e($whatsappGroup->participants->count()); ?>)</p>
            <div class="overflow-x-auto -mx-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs border-b border-slate-100">
                            <th class="text-right py-2 px-2 font-semibold">الاسم</th>
                            <th class="text-right py-2 px-2 font-semibold">الرقم</th>
                            <th class="text-right py-2 px-2 font-semibold">الحالة</th>
                            <th class="py-2 px-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php $__empty_1 = true; $__currentLoopData = $whatsappGroup->participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-2.5 px-2 font-medium text-slate-800"><?php echo e($p->display_name ?: $p->salesLead?->name ?: '—'); ?></td>
                                <td class="py-2.5 px-2 dir-ltr text-left text-slate-600"><?php echo e($p->phone); ?></td>
                                <td class="py-2.5 px-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold <?php echo e(match($p->status) {
                                        'joined', 'added' => 'bg-emerald-100 text-emerald-800',
                                        'invited' => 'bg-sky-100 text-sky-800',
                                        'failed' => 'bg-rose-100 text-rose-800',
                                        'removed' => 'bg-slate-100 text-slate-500',
                                        default => 'bg-amber-100 text-amber-800',
                                    }); ?>"><?php echo e($p->statusLabel()); ?></span>
                                </td>
                                <td class="py-2.5 px-2 text-left">
                                    <?php if($whatsappGroup->isActive() && $p->status !== 'removed'): ?>
                                        <form method="post" action="<?php echo e($r('participants.destroy', [$whatsappGroup, $p])); ?>" onsubmit="return confirm('إزالة العضو؟')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-rose-600 text-xs font-semibold hover:underline">إزالة</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="py-8 text-center text-slate-500">لا يوجد مدعوون بعد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <aside class="xl:col-span-4 space-y-4">
        <div class="sales-panel p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">الحالة</span>
                <span class="text-xs px-2 py-0.5 rounded-md font-semibold <?php echo e($whatsappGroup->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($whatsappGroup->statusLabel()); ?></span>
            </div>
            <?php if($whatsappGroup->invite_link): ?>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">رابط الدعوة</label>
                    <input type="text" readonly value="<?php echo e($whatsappGroup->invite_link); ?>" class="w-full text-xs dir-ltr px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg" onclick="this.select()">
                </div>
            <?php endif; ?>
            <?php if($whatsappGroup->wa_group_jid): ?>
                <p class="text-[10px] text-slate-400 dir-ltr break-all">ID: <?php echo e($whatsappGroup->wa_group_jid); ?></p>
            <?php endif; ?>
            <div class="flex flex-wrap gap-2 pt-1">
                <form method="post" action="<?php echo e($r('sync', $whatsappGroup)); ?>" class="flex-1"><?php echo csrf_field(); ?>
                    <button type="submit" class="btn-wa-secondary w-full text-xs justify-center">مزامنة</button>
                </form>
                <form method="post" action="<?php echo e($r('refresh-invite', $whatsappGroup)); ?>" class="flex-1"><?php echo csrf_field(); ?>
                    <button type="submit" class="btn-wa-secondary w-full text-xs justify-center">تجديد الرابط</button>
                </form>
            </div>
        </div>

        <?php if($whatsappGroup->isActive()): ?>
            <form method="post" action="<?php echo e($r('participants.store', $whatsappGroup)); ?>" class="sales-panel p-4 space-y-3">
                <?php echo csrf_field(); ?>
                <p class="wa-section-title !mb-2 !pb-2">إرسال دعوة</p>
                <select name="invite_template_name" class="px-3 py-2 text-sm" required>
                    <option value="">قالب Group Invite</option>
                    <?php $__currentLoopData = $inviteTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tpl['name']); ?>" <?php if($whatsappGroup->invite_template_name === $tpl['name']): echo 'selected'; endif; ?>><?php echo e($tpl['label'] ?? $tpl['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="hidden" name="invite_template_language" value="<?php echo e($whatsappGroup->invite_template_language ?: 'en'); ?>">
                <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="px-3 py-2 dir-ltr text-sm">
                <?php if($availableLeads->isNotEmpty()): ?>
                    <div class="max-h-36 overflow-y-auto border border-slate-200 rounded-lg divide-y text-sm bg-slate-50/30">
                        <?php $__currentLoopData = $availableLeads->take(25); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 px-2 py-1.5 hover:bg-white cursor-pointer">
                                <input type="checkbox" name="lead_ids[]" value="<?php echo e($lead->id); ?>" class="rounded border-slate-300">
                                <span class="flex-1 truncate"><?php echo e($lead->name); ?></span>
                                <span class="text-[10px] text-slate-500 dir-ltr"><?php echo e($lead->phone); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn-wa-emerald"><i class="fas fa-paper-plane"></i> إرسال الدعوات</button>
            </form>

            <form method="post" action="<?php echo e($r('import-crm', $whatsappGroup)); ?>" class="sales-panel p-4 space-y-3">
                <?php echo csrf_field(); ?>
                <p class="wa-section-title !mb-2 !pb-2">من مجموعة CRM</p>
                <select name="sales_lead_group_id" class="px-3 py-2 text-sm" required>
                    <?php $__currentLoopData = $crmGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php if((int)$whatsappGroup->sales_lead_group_id === (int)$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button type="submit" class="btn-wa-secondary w-full justify-center text-sm" <?php if(!$whatsappGroup->invite_template_name): echo 'disabled'; endif; ?>>إرسال دعوات CRM</button>
            </form>

            <form method="post" action="<?php echo e($r('leave', $whatsappGroup)); ?>" onsubmit="return confirm('حذف المجموعة على Meta؟')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full py-2 text-sm text-rose-700 border border-rose-200 rounded-lg hover:bg-rose-50">حذف المجموعة</button>
            </form>
        <?php endif; ?>
    </aside>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\whatsapp-groups\_show_body.blade.php ENDPATH**/ ?>