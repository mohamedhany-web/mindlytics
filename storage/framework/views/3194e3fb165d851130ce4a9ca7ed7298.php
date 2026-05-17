<?php $__env->startSection('title', $plan->title); ?>
<?php $__env->startSection('header', 'خطة تسويق: ' . $plan->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $evtStatus = fn ($s) => match($s) {
        'idea' => 'فكرة',
        'draft' => 'مسودة',
        'scheduled' => 'مجدول',
        'published' => 'منشور',
        'skipped' => 'تم التخطي',
        default => $s,
    };
?>
<div class="space-y-6">
    <a href="<?php echo e(route('admin.moderator-marketing-plans.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> العودة للقائمة
    </a>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-2">
        <p class="text-sm text-slate-600">المشرف: <strong class="text-slate-900"><?php echo e($plan->moderator->name ?? '—'); ?></strong></p>
        <p class="text-sm text-slate-600">الحالة: <span class="font-semibold"><?php echo e($plan->status); ?></span></p>
        <?php if($plan->start_date || $plan->end_date): ?>
            <p class="text-sm text-slate-600">
                <?php if($plan->start_date): ?> من <?php echo e($plan->start_date->format('Y-m-d')); ?> <?php endif; ?>
                <?php if($plan->end_date): ?> — إلى <?php echo e($plan->end_date->format('Y-m-d')); ?> <?php endif; ?>
            </p>
        <?php endif; ?>
        <?php if($plan->designTaskCycle): ?>
            <p class="text-sm text-slate-600">دورة تصميم مرتبطة: <a href="<?php echo e(route('admin.design-task-cycles.show', $plan->designTaskCycle)); ?>" class="text-fuchsia-700 font-semibold">#<?php echo e($plan->designTaskCycle->id); ?> <?php echo e($plan->designTaskCycle->title); ?></a></p>
        <?php endif; ?>
    </div>

    <?php if($plan->summary): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-500 uppercase mb-2">الملخص</h2>
            <p class="text-slate-800 whitespace-pre-wrap"><?php echo e($plan->summary); ?></p>
        </div>
    <?php endif; ?>
    <?php if($plan->goals): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-500 uppercase mb-2">الأهداف</h2>
            <p class="text-slate-800 whitespace-pre-wrap"><?php echo e($plan->goals); ?></p>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-900">المنصات</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-right px-4 py-2">المنصة</th>
                        <th class="text-right px-4 py-2">رابط</th>
                        <th class="text-right px-4 py-2">استراتيجية</th>
                        <th class="text-right px-4 py-2">إيقاع النشر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $plan->platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2 font-semibold">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full border" style="background: <?php echo e($plat->color_hex); ?>"></span>
                                    <?php echo e($plat->displayName()); ?>

                                </span>
                            </td>
                            <td class="px-4 py-2 break-all max-w-xs">
                                <?php if($plat->profile_url): ?><a href="<?php echo e($plat->profile_url); ?>" target="_blank" class="text-blue-600 text-xs"><?php echo e(\Illuminate\Support\Str::limit($plat->profile_url, 40)); ?></a><?php else: ?> — <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-slate-700"><?php echo e($plat->strategy_notes ?: '—'); ?></td>
                            <td class="px-4 py-2 text-slate-700"><?php echo e($plat->cadence_notes ?: '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا منصات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 font-bold text-slate-900">أحداث التقويم</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-right px-4 py-2">الوقت</th>
                        <th class="text-right px-4 py-2">العنوان</th>
                        <th class="text-right px-4 py-2">المنصة</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">دورة تصميم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $plan->calendarEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap"><?php echo e($ev->starts_at->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-2"><?php echo e($ev->title); ?></td>
                            <td class="px-4 py-2"><?php echo e($ev->platform ? $ev->platform->displayName() : '—'); ?></td>
                            <td class="px-4 py-2"><?php echo e($evtStatus($ev->status)); ?></td>
                            <td class="px-4 py-2">
                                <?php if($ev->design_task_cycle_id): ?>
                                    <a href="<?php echo e(route('admin.design-task-cycles.show', $ev->design_task_cycle_id)); ?>" class="text-fuchsia-700 font-semibold">#<?php echo e($ev->design_task_cycle_id); ?></a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا أحداث.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/moderator-marketing-plans/show.blade.php ENDPATH**/ ?>