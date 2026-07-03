<form method="post" action="<?php echo e($r('store')); ?>" class="sales-panel p-5 md:p-6 space-y-6">
    <?php echo csrf_field(); ?>

    <div>
        <p class="wa-section-title"><i class="fas fa-info-circle text-slate-400 ml-1"></i> بيانات المجموعة</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة *</label>
                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" required maxlength="120" class="px-3 py-2.5" placeholder="مثال: عملاء حملة يوليو">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">الوصف</label>
                <textarea name="description" rows="2" class="px-3 py-2.5" placeholder="وصف مختصر يظهر للمدعوين"><?php echo e(old('description')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ربط بمجموعة CRM</label>
                <select name="sales_lead_group_id" class="px-3 py-2.5">
                    <option value="">— بدون —</option>
                    <?php $__currentLoopData = $crmGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php if((int)old('sales_lead_group_id', $prefillCrmGroupId) === (int)$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">موافقة الانضمام</label>
                <select name="join_approval_mode" class="px-3 py-2.5">
                    <option value="auto_approve" <?php if(old('join_approval_mode', 'auto_approve') === 'auto_approve'): echo 'selected'; endif; ?>>انضمام تلقائي</option>
                    <option value="approval_required" <?php if(old('join_approval_mode') === 'approval_required'): echo 'selected'; endif; ?>>يتطلب موافقة</option>
                </select>
            </div>
        </div>
    </div>

    <div>
        <p class="wa-section-title"><i class="fas fa-paper-plane text-slate-400 ml-1"></i> قالب الدعوة (Meta)</p>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">قالب Group Invite</label>
                <select name="invite_template_name" class="px-3 py-2.5" id="invite-template">
                    <option value="">— لاحقاً من صفحة المجموعة —</option>
                    <?php $__currentLoopData = $inviteTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tpl['name']); ?>" data-lang="<?php echo e($tpl['language']); ?>" <?php if(old('invite_template_name') === $tpl['name']): echo 'selected'; endif; ?>><?php echo e($tpl['label'] ?? $tpl['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">من مكتبة Meta: utility · group_invite_link</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">لغة القالب</label>
                <input type="text" name="invite_template_language" id="invite-template-lang" value="<?php echo e(old('invite_template_language', 'en')); ?>" class="px-3 py-2.5 dir-ltr">
            </div>
        </div>
    </div>

    <div>
        <p class="wa-section-title"><i class="fas fa-user-plus text-slate-400 ml-1"></i> مدعوون (اختياري)</p>
        <?php if($prefillParticipants->isNotEmpty()): ?>
            <p class="text-xs text-emerald-700 mb-2"><i class="fas fa-check-circle ml-1"></i> <?php echo e($prefillParticipants->count()); ?> عميل من مجموعة CRM</p>
            <?php $__currentLoopData = $prefillParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="phones[]" value="<?php echo e($p['phone']); ?>">
                <input type="hidden" name="lead_ids[]" value="<?php echo e($p['sales_lead_id']); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <ul class="text-sm border border-slate-200 rounded-lg divide-y max-h-40 overflow-y-auto mb-3 bg-slate-50/50">
                <?php $__currentLoopData = $prefillParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="px-3 py-2 flex justify-between gap-2">
                        <span class="font-medium text-slate-800"><?php echo e($p['display_name']); ?></span>
                        <span dir="ltr" class="text-slate-500 text-xs"><?php echo e($p['phone']); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
        <div id="phones-wrap" class="space-y-2">
            <?php for($i = 0; $i < 3; $i++): ?>
                <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="px-3 py-2.5 dir-ltr" value="<?php echo e(old('phones.'.$i)); ?>">
            <?php endfor; ?>
        </div>
        <button type="button" onclick="addWaPhone()" class="mt-2 text-xs text-sky-700 font-semibold hover:underline">+ إضافة رقم</button>
    </div>

    <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100">
        <button type="submit" class="btn-wa-primary">
            <i class="fab fa-whatsapp"></i> إنشاء المجموعة
        </button>
        <a href="<?php echo e($r('index')); ?>" class="btn-wa-secondary">إلغاء</a>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
function addWaPhone() {
    const wrap = document.getElementById('phones-wrap');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'phones[]';
    input.placeholder = '2010xxxxxxxx';
    input.className = 'px-3 py-2.5 dir-ltr';
    wrap.appendChild(input);
}
document.getElementById('invite-template')?.addEventListener('change', function () {
    const lang = this.selectedOptions[0]?.dataset?.lang;
    if (lang) document.getElementById('invite-template-lang').value = lang;
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/sales/whatsapp-groups/_form_create.blade.php ENDPATH**/ ?>