<?php $__env->startSection('title', 'عميل محتمل جديد'); ?>
<?php $__env->startSection('header', 'عميل محتمل جديد'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .lead-form-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .lead-form-panel input,
    .lead-form-panel select,
    .lead-form-panel textarea {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
    }
    .lead-form-panel input:focus,
    .lead-form-panel select:focus,
    .lead-form-panel textarea:focus {
        outline: none;
        border-color: #64748b;
        box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.15);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $defaultSource = old('source', 'call');
    $defaultPriority = old('priority', 'normal');
    $defaultFollow = old('follow_preset', 'tomorrow');
?>

<div class="space-y-4" x-data="fastLeadCreate({
    followPreset: <?php echo json_encode($defaultFollow, 15, 512) ?>,
    customFollow: <?php echo json_encode(old('next_follow_up_at', ''), 512) ?>,
    showDetails: <?php echo e(old('email') || old('company') || old('notes') || old('expected_value') ? 'true' : 'false'); ?>,
})">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">تسجيل عميل جديد</h2>
            <p class="text-sm text-slate-500 mt-0.5">الاسم مطلوب — الباقي اختياري · Ctrl+Enter للحفظ</p>
        </div>
        <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="text-sm text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-right ml-1"></i> قائمة العملاء
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('employee.sales.leads.store')); ?>"
          class="grid grid-cols-1 xl:grid-cols-12 gap-6"
          @keydown.ctrl.enter.prevent="$refs.primarySubmit.click()">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="stage" value="new">
        <input type="hidden" name="follow_preset" :value="followPreset">
        <input type="hidden" name="next_follow_up_at" :value="resolvedFollowUp()">

        <div class="xl:col-span-9 lead-form-panel p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="md:col-span-2 xl:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم العميل <span class="text-red-600">*</span></label>
                <input type="text" name="name" required autofocus value="<?php echo e(old('name')); ?>"
                       placeholder="مثال: أحمد محمد"
                       class="w-full px-3 py-2.5 text-base">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">الهاتف</label>
                <input type="tel" name="phone" inputmode="tel" value="<?php echo e(old('phone')); ?>"
                       placeholder="01xxxxxxxxx" class="w-full px-3 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">الاهتمام</label>
                <input type="text" name="interest" value="<?php echo e(old('interest')); ?>"
                       placeholder="مثال: كورس Python" class="w-full px-3 py-2.5 text-sm">
            </div>
        </div>

        <div class="border-t border-slate-100 pt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">المصدر</label>
                <select name="source" class="w-full px-3 py-2.5 text-sm bg-white">
                    <?php $__currentLoopData = \App\Models\SalesLead::SOURCES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('source', $defaultSource) === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">الأولوية</label>
                <select name="priority" class="w-full px-3 py-2.5 text-sm bg-white">
                    <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php if(old('priority', $defaultPriority) === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">متابعة</label>
                <select x-model="followPreset" class="w-full px-3 py-2.5 text-sm bg-white">
                    <option value="none">بدون موعد</option>
                    <option value="today">اليوم 17:00</option>
                    <option value="tomorrow">غداً 10:00</option>
                    <option value="3days">بعد 3 أيام</option>
                    <option value="week">بعد أسبوع</option>
                    <option value="custom">موعد مخصص</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">المجموعة <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <select name="sales_lead_group_id" class="w-full px-3 py-2.5 text-sm bg-white">
                    <option value="">— بدون مجموعة —</option>
                    <?php $__currentLoopData = $groups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->id); ?>" <?php if(old('sales_lead_group_id', $preselectedGroupId ?? '') == $group->id): echo 'selected'; endif; ?>>
                            <?php echo e($group->name); ?><?php if($group->is_admin_managed): ?> (إدارة) <?php endif; ?>
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['sales_lead_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php if(($groups ?? collect())->isEmpty()): ?>
                <div class="flex items-end">
                    <a href="<?php echo e(route('employee.sales.groups.create')); ?>" class="text-sm text-slate-600 hover:text-slate-900 underline">+ إنشاء مجموعة أولاً</a>
                </div>
            <?php endif; ?>
        </div>

        <div x-show="followPreset === 'custom'" x-cloak>
            <label class="block text-sm font-medium text-slate-700 mb-1">الموعد المخصص</label>
            <input type="datetime-local" x-model="customFollow" class="w-full max-w-xs px-3 py-2.5 text-sm">
            <?php $__errorArgs = ['next_follow_up_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <button type="button" @click="showDetails = !showDetails"
                    class="text-sm font-medium text-slate-700 flex items-center gap-2">
                <i class="fas fa-chevron-down text-xs text-slate-400" :class="showDetails && 'rotate-180'"></i>
                تفاصيل إضافية (اختياري)
            </button>
            <div x-show="showDetails" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">البريد</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="w-full px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">الشركة</label>
                    <input type="text" name="company" value="<?php echo e(old('company')); ?>" class="w-full px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">قيمة متوقعة (ج.م)</label>
                    <input type="number" step="0.01" min="0" name="expected_value" value="<?php echo e(old('expected_value')); ?>" class="w-full px-3 py-2.5 text-sm">
                </div>
                <div class="md:col-span-2 xl:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2.5 text-sm"><?php echo e(old('notes')); ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
            <button type="submit" name="save_action" value="show" x-ref="primarySubmit"
                    class="px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">
                حفظ وعرض
            </button>
            <button type="submit" name="save_action" value="another"
                    class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 text-sm font-semibold">
                حفظ وإضافة آخر
            </button>
            <a href="<?php echo e(route('employee.sales.leads.index')); ?>"
               class="px-5 py-2.5 rounded-lg text-slate-600 text-sm hover:text-slate-900">
                إلغاء
            </a>
        </div>
        </div>

        <aside class="xl:col-span-3 space-y-4">
            <div class="lead-form-panel p-4 text-sm text-slate-600 space-y-2">
                <p class="font-semibold text-slate-800">تسجيل سريع</p>
                <p><kbd class="px-1 py-0.5 bg-slate-100 rounded text-xs">Ctrl</kbd>+<kbd class="px-1 py-0.5 bg-slate-100 rounded text-xs">Enter</kbd> للحفظ</p>
                <p>المرحلة تُسجّل «جديد» تلقائياً</p>
                <p>«حفظ وإضافة آخر» لتسجيل عدة عملاء</p>
                <p class="pt-2 border-t border-slate-100">اختر <strong>مجموعة</strong> لتنظيم العملاء</p>
                <a href="<?php echo e(route('employee.sales.groups.index')); ?>" class="inline-block text-slate-800 font-medium hover:underline">إدارة المجموعات</a>
            </div>
            <div class="lead-form-panel p-4 text-sm">
                <p class="text-slate-500">مسؤول المبيعات</p>
                <p class="font-semibold text-slate-900 mt-1"><?php echo e(auth()->user()->name); ?></p>
            </div>
        </aside>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function fastLeadCreate(config) {
    const pad = (n) => String(n).padStart(2, '0');
    const fmtLocal = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    const presets = {
        none: () => '',
        today: () => { const d = new Date(); d.setHours(17, 0, 0, 0); return fmtLocal(d); },
        tomorrow: () => { const d = new Date(); d.setDate(d.getDate() + 1); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        '3days': () => { const d = new Date(); d.setDate(d.getDate() + 3); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        week: () => { const d = new Date(); d.setDate(d.getDate() + 7); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        custom: () => config.customFollow || '',
    };
    return {
        followPreset: config.followPreset || 'tomorrow',
        customFollow: config.customFollow || '',
        showDetails: !!config.showDetails,
        resolvedFollowUp() {
            const fn = presets[this.followPreset];
            return fn ? fn() : '';
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/sales/leads/create.blade.php ENDPATH**/ ?>