
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
        <div class="relative mt-3 rounded-2xl border-2 border-dashed border-indigo-300/60 bg-gradient-to-br from-indigo-950/[0.03] via-cyan-950/[0.04] to-violet-950/[0.03] p-4 sm:p-5 overflow-x-auto">
            <div class="flex items-center gap-2 mb-4 text-indigo-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-600 text-white shadow-md text-sm">
                    <i class="fas fa-sitemap"></i>
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-indigo-700/90">خريطة الحضور الأوفلاين</span>
            </div>
            <div class="relative space-y-0">
                <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pl = min($row['depth'], 10) * 0.9; ?>
                    <div class="flex items-start gap-2.5 py-1.5 rounded-lg hover:bg-white/40 transition-colors" style="margin-inline-start: <?php echo e($pl); ?>rem">
                        <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-gradient-to-r from-cyan-500 to-indigo-600 shadow-sm ring-2 ring-white"></span>
                        <span class="text-sm text-slate-800 leading-relaxed font-medium"><?php echo e($row['text']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/partials/offline-mindmap-visual.blade.php ENDPATH**/ ?>