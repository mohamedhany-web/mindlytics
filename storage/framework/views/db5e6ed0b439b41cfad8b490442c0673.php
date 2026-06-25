<?php if($isFirst): ?>
    <span class="inline-block text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-lg px-2.5 py-1 mb-2"><?php echo e(__('public.roadmap_start')); ?></span>
<?php elseif($isLast): ?>
    <span class="inline-block text-xs font-bold text-violet-800 bg-violet-50 border border-violet-200/80 rounded-lg px-2.5 py-1 mb-2"><?php echo e(__('public.roadmap_end')); ?></span>
<?php else: ?>
    <span class="inline-block text-xs font-bold text-sky-800 bg-sky-50 border border-sky-200/80 rounded-lg px-2.5 py-1 mb-2"><?php echo e(__('public.roadmap_step', ['n' => $index])); ?></span>
<?php endif; ?>
<h2 class="text-lg md:text-xl font-black text-slate-900 leading-snug"><?php echo e($step['title'] ?? ''); ?></h2>
<?php if(!empty($step['description'])): ?>
    <p class="mt-2.5 text-slate-600 text-sm md:text-base leading-relaxed whitespace-pre-line"><?php echo e($step['description']); ?></p>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/public/partials/course-mind-map-card-inner.blade.php ENDPATH**/ ?>