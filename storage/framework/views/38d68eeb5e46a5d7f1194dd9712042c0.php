
<?php $text = $text ?? null; ?>
<?php if(filled($text)): ?>
    <?php
        $rawLines = preg_split('/\r\n|\r|\n/', $text);
        $lines = [];
        foreach ($rawLines as $raw) {
            if (trim($raw) === '') {
                continue;
            }
            $depth = 0;
            $s = $raw;
            while (str_starts_with($s, '  ') || str_starts_with($s, "\t")) {
                if (str_starts_with($s, '  ')) {
                    $s = substr($s, 2);
                } else {
                    $s = substr($s, 1);
                }
                $depth++;
                if ($depth > 12) {
                    break;
                }
            }
            $s = trim($s);
            if (str_starts_with($s, '- ')) {
                $s = substr($s, 2);
            } elseif (str_starts_with($s, '• ')) {
                $s = substr($s, 2);
            }
            $s = trim($s);
            if ($s === '') {
                continue;
            }
            $lines[] = ['depth' => $depth, 'text' => $s];
        }
    ?>
    <?php if(count($lines)): ?>
        <?php $previewCount = min(6, count($lines)); ?>
        <details class="group mt-3 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <summary class="cursor-pointer list-none px-4 py-3 sm:px-5 sm:py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between gap-3 select-none">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-600 text-white shadow-sm text-sm flex-shrink-0">
                        <i class="fas fa-sitemap"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm font-black text-slate-900 truncate">الخريطة الذهنية</div>
                        <div class="text-[11px] font-bold text-slate-500"><?php echo e(count($lines)); ?> نقطة — اضغط للعرض</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-slate-400 text-sm transition-transform duration-200 group-open:rotate-180"></i>
            </summary>

            <div class="p-4 sm:p-5 bg-white">
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-indigo-950/[0.02] via-cyan-950/[0.03] to-violet-950/[0.02] p-4 overflow-hidden">
                    <div class="max-h-72 overflow-auto">
                        <div class="space-y-0">
                            <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $pl = min($row['depth'], 10) * 0.75; ?>
                                <div class="flex items-start gap-2.5 py-1.5 rounded-lg hover:bg-white/60 transition-colors" style="margin-inline-start: <?php echo e($pl); ?>rem">
                                    <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-gradient-to-r from-cyan-500 to-indigo-600 shadow-sm ring-2 ring-white"></span>
                                    <span class="text-sm text-slate-800 leading-relaxed font-semibold break-words"><?php echo e($row['text']); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </details>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/partials/offline-mindmap-visual.blade.php ENDPATH**/ ?>