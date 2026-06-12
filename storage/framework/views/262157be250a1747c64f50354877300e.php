<?php $__env->startSection('title', 'خطط تسويق المشرفين'); ?>
<?php $__env->startSection('header', 'خطط التسويق والمنصات (مشرفو المحتوى)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <form method="get" class="flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">المشرف</label>
            <select name="moderator_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm min-w-[200px]">
                <option value="">الكل</option>
                <?php $__currentLoopData = $moderators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php echo e((string) request('moderator_id') === (string) $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">الحالة</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = ['draft', 'active', 'paused', 'completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($v); ?>" <?php echo e(request('status') === $v ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">بحث في العنوان</label>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="...">
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">تصفية</button>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">#</th>
                        <th class="text-right px-4 py-3 font-semibold">المشرف</th>
                        <th class="text-right px-4 py-3 font-semibold">العنوان</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold">منصات</th>
                        <th class="text-right px-4 py-3 font-semibold">أحداث</th>
                        <th class="text-right px-4 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-mono text-xs"><?php echo e($p->id); ?></td>
                            <td class="px-4 py-3"><?php echo e($p->moderator->name ?? '—'); ?></td>
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($p->title); ?></td>
                            <td class="px-4 py-3"><span class="text-xs font-medium px-2 py-1 rounded bg-slate-100"><?php echo e($p->status); ?></span></td>
                            <td class="px-4 py-3"><?php echo e($p->platforms_count); ?></td>
                            <td class="px-4 py-3"><?php echo e($p->calendar_events_count); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="<?php echo e(route('admin.moderator-marketing-plans.show', $p)); ?>" class="text-pink-700 font-semibold hover:underline">عرض</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">لا توجد خطط.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($plans->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($plans->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/moderator-marketing-plans/index.blade.php ENDPATH**/ ?>