<?php
    $attendees = $confirmedAttendees ?? collect();
    $count = $confirmedCount ?? $attendees->count();
    $showPhone = $showPhone ?? false;
?>
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden <?php echo e($wrapperClass ?? ''); ?>">
    <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h4 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-check text-emerald-600"></i>
                <?php echo e($title ?? 'من أكّدوا الحضور'); ?>

            </h4>
            <?php if(!empty($subtitle)): ?>
                <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200">
            <?php echo e(number_format($count)); ?>

        </span>
    </div>
    <div class="overflow-y-auto divide-y divide-slate-50" style="max-height: <?php echo e($maxHeight ?? '360px'); ?>">
        <?php $__empty_1 = true; $__currentLoopData = $attendees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-slate-50/80 text-sm">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-[10px] font-bold">
                        <?php echo e(mb_substr($attendee->name, 0, 1)); ?>

                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-800 truncate"><?php echo e($attendee->name); ?></p>
                        <?php if($showPhone && $attendee->phone): ?>
                            <p class="text-[11px] text-slate-500 truncate" dir="ltr"><?php echo e($attendee->phone); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 whitespace-nowrap flex-shrink-0">
                    <?php echo e($attendee->checked_in_at?->format('Y-m-d H:i')); ?>

                </span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-4 py-10 text-center text-slate-500">
                <i class="fas fa-user-clock text-2xl text-slate-300 mb-2 block"></i>
                <p class="text-xs font-medium"><?php echo e($emptyText ?? 'لا يوجد مؤكدون بعد'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\partials\workshop-confirmed-attendees-list.blade.php ENDPATH**/ ?>