

<?php $__env->startSection('title', $lead->name); ?>
<?php $__env->startSection('header', 'عميل محتمل: ' . $lead->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> القائمة</a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.sales.leads.edit', $lead)); ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold">تعديل / إعادة إسناد</a>
            <form action="<?php echo e(route('admin.sales.leads.destroy', $lead)); ?>" method="post" onsubmit="return confirm('حذف نهائياً؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-sm font-semibold">حذف</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap gap-2 items-center justify-between">
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span>
                <span class="text-sm text-gray-500"><?php echo e(\App\Models\SalesLead::sourceLabel($lead->source)); ?></span>
            </div>
            <p class="text-sm text-gray-600">مسند إلى: <strong><?php echo e($lead->assignee->name ?? '—'); ?></strong></p>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500">الهاتف</dt><dd class="font-medium"><?php echo e($lead->phone ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">البريد</dt><dd class="font-medium"><?php echo e($lead->email ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">الشركة</dt><dd class="font-medium"><?php echo e($lead->company ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500">قيمة متوقعة</dt><dd class="font-medium"><?php echo e($lead->expected_value !== null ? number_format($lead->expected_value, 2) . ' ج.م' : '—'); ?></dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">متابعة تالية</dt><dd class="font-medium"><?php echo e($lead->next_follow_up_at?->format('Y-m-d H:i') ?? '—'); ?></dd></div>
            <?php if($lead->interest): ?>
            <div class="sm:col-span-2"><dt class="text-gray-500">الاهتمام</dt><dd class="text-gray-800 whitespace-pre-wrap"><?php echo e($lead->interest); ?></dd></div>
            <?php endif; ?>
            <?php if($lead->notes): ?>
            <div class="sm:col-span-2"><dt class="text-gray-500">ملاحظات</dt><dd class="text-gray-800 whitespace-pre-wrap"><?php echo e($lead->notes); ?></dd></div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-4">سجل النشاط (يُسجَّل في سجل المراقبة)</h2>
        <form method="post" action="<?php echo e(route('admin.sales.leads.activities.store', $lead)); ?>" class="space-y-3 mb-8 pb-8 border-b border-gray-100">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">النوع</label>
                    <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                        <?php $__currentLoopData = \App\Models\SalesActivity::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($k !== 'stage_change'): ?>
                            <option value="<?php echo e($k); ?>"><?php echo e($label); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">عنوان</label>
                    <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <textarea name="body" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="التفاصيل"></textarea>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold">إضافة نشاط</button>
        </form>

        <ul class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $lead->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="border-r-4 border-emerald-300 pr-4">
                <div class="flex flex-wrap justify-between gap-2 text-xs text-gray-500">
                    <span class="font-semibold text-emerald-800"><?php echo e(\App\Models\SalesActivity::typeLabel($act->type)); ?></span>
                    <span><?php echo e($act->created_at->format('Y-m-d H:i')); ?> — <?php echo e($act->user->name); ?></span>
                </div>
                <?php if($act->title): ?><p class="font-medium text-gray-900 mt-1"><?php echo e($act->title); ?></p><?php endif; ?>
                <?php if($act->body): ?><p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap"><?php echo e($act->body); ?></p><?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="text-gray-500 text-sm">لا أنشطة بعد</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/leads/show.blade.php ENDPATH**/ ?>