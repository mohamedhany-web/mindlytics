

<?php $__env->startSection('title', $group->name); ?>
<?php $__env->startSection('header', 'مجموعة: '.$group->name); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('employee.sales.groups._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold text-slate-900"><?php echo e($group->name); ?></h2>
                <?php if($group->is_admin_managed): ?>
                    <span class="text-xs px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-semibold">من الإدارة</span>
                <?php endif; ?>
            </div>
            <?php if($group->description): ?>
                <p class="text-sm text-slate-500 mt-1"><?php echo e($group->description); ?></p>
            <?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.sales.groups.index')); ?>" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700">المجموعات</a>
            <a href="<?php echo e(route('employee.sales.leads.index', ['group_id' => $group->id])); ?>" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700">عرض العملاء</a>
            <a href="<?php echo e(route('employee.sales.leads.create')); ?>?group=<?php echo e($group->id); ?>" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">+ عميل في المجموعة</a>
        </div>
    </div>

    <?php if(session('success')): ?><div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm"><?php echo e(session('error')); ?></div><?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 space-y-4">
            <form method="post" action="<?php echo e(route('employee.sales.groups.update', $group)); ?>" class="sales-panel p-5 space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">الاسم</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $group->name)); ?>" required class="px-3 py-2.5" <?php if($group->is_admin_managed): echo 'disabled'; endif; ?>>
                        <?php if($group->is_admin_managed): ?><p class="text-xs text-slate-500 mt-1">اسم مجموعات الإدارة لا يُعدَّل</p><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">وصف</label>
                        <input type="text" name="description" value="<?php echo e(old('description', $group->description)); ?>" class="px-3 py-2.5">
                    </div>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <label class="block text-sm font-bold text-slate-800">اختر العملاء</label>
                        <span class="text-xs text-slate-500"><?php echo e($group->leads->count()); ?> محدّد حالياً</span>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-200 rounded-lg divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $availableLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-50 cursor-pointer text-sm">
                                <input type="checkbox" name="lead_ids[]" value="<?php echo e($lead->id); ?>" class="rounded border-slate-300"
                                    <?php if(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id): echo 'checked'; endif; ?>>
                                <span class="font-medium text-slate-900 flex-1"><?php echo e($lead->name); ?></span>
                                <?php if($lead->phone): ?><span class="text-slate-500 text-xs" dir="ltr"><?php echo e($lead->phone); ?></span><?php endif; ?>
                                <?php if($lead->sales_lead_group_id && (int)$lead->sales_lead_group_id !== (int)$group->id): ?>
                                    <span class="text-[10px] text-amber-700">مجموعة أخرى</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="p-4 text-sm text-slate-500 text-center">لا يوجد عملاء متاحون — <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="underline">سجّل عميلاً</a></p>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold">حفظ التغييرات</button>
            </form>

            <?php if(!$group->is_admin_managed): ?>
                <form method="post" action="<?php echo e(route('employee.sales.groups.destroy', $group)); ?>" onsubmit="return confirm('حذف المجموعة؟ العملاء يبقون بدون مجموعة.')" class="inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-4 py-2 text-sm text-rose-700 border border-rose-200 rounded-lg hover:bg-rose-50">حذف المجموعة</button>
                </form>
            <?php endif; ?>
        </div>

        <aside class="xl:col-span-4 space-y-4">
            <div class="sales-panel p-4">
                <h3 class="font-bold text-slate-900 text-sm mb-3">عملاء المجموعة (<?php echo e($group->leads->count()); ?>)</h3>
                <ul class="space-y-2 max-h-96 overflow-y-auto text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $group->leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                            <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="font-medium text-slate-800 hover:underline"><?php echo e($lead->name); ?></a>
                            <span class="text-xs text-slate-500"><?php echo e(\App\Models\SalesLead::stageLabel($lead->stage)); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-slate-500 text-sm">لا يوجد عملاء بعد</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="sales-panel p-4 text-xs text-slate-600">
                <p class="font-semibold text-slate-800 mb-1">اختصار</p>
                <p>عند تسجيل عميل جديد اختر هذه المجموعة من القائمة — أو استخدم زر «+ عميل في المجموعة».</p>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales\groups\show.blade.php ENDPATH**/ ?>