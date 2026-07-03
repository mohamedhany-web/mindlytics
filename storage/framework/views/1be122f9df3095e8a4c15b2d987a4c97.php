

<?php $__env->startSection('title', 'مجموعة واتساب جديدة'); ?>
<?php $__env->startSection('header', 'مجموعة واتساب جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.whatsapp-groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $r = fn($name, ...$p) => route('admin.sales.whatsapp-groups.'.$name, ...$p); ?>

<div class="p-4 md:p-6 space-y-4 max-w-3xl">
    <a href="<?php echo e($r('index')); ?>" class="text-sm text-slate-600 hover:underline">← مجموعات واتساب</a>

    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <form method="post" action="<?php echo e($r('store')); ?>" class="sales-panel p-5 space-y-5">
        <?php echo csrf_field(); ?>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة على واتساب *</label>
                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" required maxlength="120" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">وصف المجموعة</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg"><?php echo e(old('description')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">ربط بمجموعة عملاء (CRM)</label>
                <select name="sales_lead_group_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg" id="crm-group-select">
                    <option value="">— بدون —</option>
                    <?php $__currentLoopData = $crmGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php if((int)old('sales_lead_group_id', $prefillCrmGroupId) === (int)$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="announce_only" value="1" class="rounded" <?php if(old('announce_only')): echo 'checked'; endif; ?>>
                <span>الرسائل للمشرفين فقط (Announce)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="restrict_info" value="1" class="rounded" <?php if(old('restrict_info')): echo 'checked'; endif; ?>>
                <span>تعديل معلومات المجموعة للمشرفين فقط</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-800 mb-2">الأعضاء (رقم واحد على الأقل) *</label>
            <?php if($prefillParticipants->isNotEmpty()): ?>
                <p class="text-xs text-emerald-700 mb-2">تم تحميل <?php echo e($prefillParticipants->count()); ?> عميل من مجموعة CRM</p>
                <?php $__currentLoopData = $prefillParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="phones[]" value="<?php echo e($p['phone']); ?>">
                    <input type="hidden" name="lead_ids[]" value="<?php echo e($p['sales_lead_id']); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <ul class="text-sm border border-slate-200 rounded-lg divide-y max-h-48 overflow-y-auto mb-3">
                    <?php $__currentLoopData = $prefillParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="px-3 py-2 flex justify-between"><span><?php echo e($p['display_name']); ?></span><span dir="ltr" class="text-slate-500"><?php echo e($p['phone']); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
            <div id="phones-wrap" class="space-y-2">
                <?php for($i = 0; $i < 3; $i++): ?>
                    <input type="text" name="phones[]" placeholder="2010xxxxxxxx" class="w-full px-3 py-2 border border-slate-200 rounded-lg dir-ltr" value="<?php echo e(old('phones.'.$i)); ?>">
                <?php endfor; ?>
            </div>
            <button type="button" onclick="addPhone()" class="mt-2 text-xs text-emerald-700 font-semibold">+ رقم آخر</button>
        </div>

        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold" <?php if(!($bridge['connected'] ?? false)): echo 'disabled'; endif; ?>>
            إنشاء على واتساب
        </button>
        <?php if(!($bridge['connected'] ?? false)): ?>
            <p class="text-xs text-amber-700">يجب أن تكون جلسة الجسر متصلة أولاً.</p>
        <?php endif; ?>
    </form>
</div>

<script>
function addPhone() {
    const wrap = document.getElementById('phones-wrap');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'phones[]';
    input.placeholder = '2010xxxxxxxx';
    input.className = 'w-full px-3 py-2 border border-slate-200 rounded-lg dir-ltr';
    wrap.appendChild(input);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\whatsapp-groups\create.blade.php ENDPATH**/ ?>