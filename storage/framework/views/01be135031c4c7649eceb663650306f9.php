<?php
    $nodes = $nodes ?? [];
    $depth = $depth ?? 0;
    $rootType = $rootType ?? 'asset';

    $typeStyles = [
        'asset' => ['badge' => 'bg-amber-50 text-amber-800 border-amber-100', 'tag' => 'bg-amber-100 text-amber-800', 'toggle' => 'bg-amber-100 text-amber-800 hover:bg-amber-200'],
        'liability' => ['badge' => 'bg-rose-50 text-rose-800 border-rose-100', 'tag' => 'bg-rose-100 text-rose-800', 'toggle' => 'bg-rose-100 text-rose-800 hover:bg-rose-200'],
        'equity' => ['badge' => 'bg-violet-50 text-violet-800 border-violet-100', 'tag' => 'bg-violet-100 text-violet-800', 'toggle' => 'bg-violet-100 text-violet-800 hover:bg-violet-200'],
        'revenue' => ['badge' => 'bg-emerald-50 text-emerald-800 border-emerald-100', 'tag' => 'bg-emerald-100 text-emerald-800', 'toggle' => 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'],
        'expense' => ['badge' => 'bg-orange-50 text-orange-800 border-orange-100', 'tag' => 'bg-orange-100 text-orange-800', 'toggle' => 'bg-orange-100 text-orange-800 hover:bg-orange-200'],
    ];
    $styles = $typeStyles[$rootType] ?? $typeStyles['asset'];
?>
<ul class="space-y-1 <?php echo e($depth > 0 ? 'mr-3 sm:mr-5 mt-1.5 border-r-2 border-slate-200/90 pr-3 sm:pr-4' : ''); ?>">
    <?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $hasChildren = !empty($node['children']);
            $type = $node['type'] ?? $rootType;
            $typeIcon = match ($type) {
                'asset' => 'fa-coins text-amber-600',
                'liability' => 'fa-arrow-down text-rose-600',
                'equity' => 'fa-balance-scale text-violet-600',
                'revenue' => 'fa-chart-line text-emerald-600',
                'expense' => 'fa-fire text-orange-600',
                default => 'fa-circle text-slate-400',
            };
            $nodeIcon = $node['icon'] ?? null;
            $hasRoute = !empty($node['route']) && Route::has($node['route']);
        ?>
        <li class="rounded-xl border border-transparent hover:border-slate-200 hover:bg-white/90 transition-colors"
            x-data="{ open: <?php echo e($depth < 1 ? 'true' : 'false'); ?> }"
            @chart-tree-toggle.window="open = $event.detail.open"
            x-show="$store.chartTreeSearch.query.trim() === '' || chartNodeMatches(<?php echo \Illuminate\Support\Js::from($node)->toHtml() ?>, $store.chartTreeSearch.query)"
            x-transition>
            <div class="flex flex-wrap items-start gap-2 py-2 px-2 sm:px-3 rounded-xl">
                <?php if($hasChildren): ?>
                    <button type="button" @click="open = !open"
                            class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition-colors <?php echo e($styles['toggle']); ?>"
                            :aria-expanded="open">
                        <i class="fas text-[10px]" :class="open ? 'fa-chevron-down' : 'fa-chevron-left'"></i>
                    </button>
                <?php else: ?>
                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="fas fa-minus text-[8px]"></i>
                    </span>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center rounded-lg px-2 py-0.5 font-mono text-[11px] font-bold border <?php echo e($styles['badge']); ?>"><?php echo e($node['code'] ?? ''); ?></span>
                        <i class="fas <?php echo e($nodeIcon ?? $typeIcon); ?> text-xs opacity-90"></i>
                        <span class="font-bold text-slate-900"><?php echo e($node['name'] ?? ''); ?></span>
                        <?php if($type): ?>
                            <span class="rounded-md px-1.5 py-0.5 text-[10px] font-semibold <?php echo e($styles['tag']); ?>">
                                <?php if($type === 'asset'): ?> أصول
                                <?php elseif($type === 'liability'): ?> خصوم
                                <?php elseif($type === 'equity'): ?> حقوق ملكية
                                <?php elseif($type === 'revenue'): ?> إيرادات
                                <?php elseif($type === 'expense'): ?> مصروفات
                                <?php else: ?> <?php echo e($type); ?>

                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <?php if($hasRoute): ?>
                            <a href="<?php echo e(route($node['route'])); ?>"
                               class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-[11px] font-bold text-sky-700 hover:bg-sky-100 border border-sky-200 transition-colors">
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
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="pb-1">
                    <?php echo $__env->make('admin.accounting.partials.chart-node', [
                        'nodes' => $node['children'],
                        'depth' => $depth + 1,
                        'rootType' => $rootType,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\accounting\partials\chart-node.blade.php ENDPATH**/ ?>