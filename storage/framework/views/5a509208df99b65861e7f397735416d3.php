<?php
    $nodes = $nodes ?? [];
    $depth = $depth ?? 0;
?>
<ul class="space-y-1 <?php echo e($depth > 0 ? 'mr-3 sm:mr-5 mt-2 border-r-2 border-slate-200/90 pr-3 sm:pr-4' : ''); ?>">
    <?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $hasChildren = !empty($node['children']);
            $type = $node['type'] ?? '';
            $typeIcon = match ($type) {
                'asset' => 'fa-coins text-amber-600',
                'liability' => 'fa-arrow-down text-rose-600',
                'equity' => 'fa-balance-scale text-violet-600',
                'revenue' => 'fa-chart-line text-emerald-600',
                'expense' => 'fa-fire text-orange-600',
                default => 'fa-circle text-slate-400',
            };
            $nodeIcon = $node['icon'] ?? null;
        ?>
        <li class="rounded-xl border border-transparent hover:border-slate-200 hover:bg-white/80 transition-colors" x-data="{ open: <?php echo e($depth < 1 ? 'true' : 'false'); ?> }">
            <div class="flex flex-wrap items-start gap-2 py-2 px-2 sm:px-3 rounded-xl bg-white/40">
                <?php if($hasChildren): ?>
                    <button type="button" @click="open = !open" class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-200/80 text-slate-700 hover:bg-slate-300 transition-colors" aria-expanded="true" :aria-expanded="open">
                        <i class="fas text-[10px]" :class="open ? 'fa-chevron-down' : 'fa-chevron-left'"></i>
                    </button>
                <?php else: ?>
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="fas fa-minus text-[8px]"></i>
                    </span>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-0.5 font-mono text-[11px] font-bold text-indigo-700 border border-indigo-100"><?php echo e($node['code'] ?? ''); ?></span>
                        <i class="fas <?php echo e($nodeIcon ?? $typeIcon); ?> text-xs opacity-90"></i>
                        <span class="font-bold text-slate-900"><?php echo e($node['name'] ?? ''); ?></span>
                        <?php if($type): ?>
                            <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">
                                <?php if($type === 'asset'): ?> أصول
                                <?php elseif($type === 'liability'): ?> خصوم
                                <?php elseif($type === 'equity'): ?> حقوق ملكية
                                <?php elseif($type === 'revenue'): ?> إيرادات
                                <?php elseif($type === 'expense'): ?> مصروفات
                                <?php else: ?> <?php echo e($type); ?>

                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($node['route']) && Route::has($node['route'])): ?>
                            <a href="<?php echo e(route($node['route'])); ?>" class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-700 hover:bg-sky-100 border border-sky-100">
                                <i class="fas fa-external-link-alt text-[9px]"></i>
                                فتح في النظام
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($node['description'])): ?>
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed pr-1"><?php echo e($node['description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($hasChildren): ?>
                <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-1">
                    <?php echo $__env->make('admin.accounting.partials.chart-node', ['nodes' => $node['children'], 'depth' => $depth + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/accounting/partials/chart-node.blade.php ENDPATH**/ ?>