<div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div class="flex items-center gap-3">
        <?php
            $dotClass = match ($member['status']) {
                'online' => 'bg-emerald-500 animate-pulse',
                'away' => 'bg-amber-500',
                'offline', 'logged_out' => 'bg-rose-500',
                'shift_completed' => 'bg-blue-500',
                default => 'bg-slate-400',
            };
        ?>
        <span class="w-3 h-3 rounded-full <?php echo e($dotClass); ?>"></span>
        <div>
            <p class="font-semibold text-slate-900"><?php echo e($member['name']); ?></p>
            <p class="text-xs text-slate-500"><?php echo e($member['status_label']); ?></p>
        </div>
    </div>
    <div class="text-xs text-slate-600 sm:text-left space-y-1">
        <p>آخر نشاط: <?php echo e($member['last_seen_human'] ?? '—'); ?></p>
        <p>حضور: <?php echo e($member['clock_in_at'] ?? '—'); ?> · جلسة: <?php echo e(($member['session_active'] ?? false) ? 'نشطة' : 'منتهية'); ?></p>
        <p>مخالفات اليوم: <?php echo e($member['violations_today'] ?? 0); ?> · انقطاع: <?php echo e($member['offline_minutes_today'] ?? 0); ?> د</p>
        <?php if(!empty($member['open_violation'])): ?>
            <p class="text-rose-700 font-semibold">انقطاع من <?php echo e($member['open_violation']['started_at']); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\sales-manager\presence\_member_row.blade.php ENDPATH**/ ?>