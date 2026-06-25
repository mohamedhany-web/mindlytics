

<?php $__env->startSection('title', $model->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-semibold mb-4">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لمكتبة النماذج</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2"><?php echo e($model->title); ?></h1>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            <?php if($model->license): ?>
                <span class="inline-flex px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-600"><?php echo e($model->license); ?></span>
            <?php endif; ?>
            <?php if($model->file_size): ?>
                <span class="text-slate-500 text-sm">الحجم: <?php echo e($model->file_size); ?></span>
            <?php endif; ?>
            <?php if($model->creator): ?>
                <span class="text-slate-500 text-sm"><i class="fas fa-user ml-1"></i> <?php echo e($model->creator->name); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if($model->description): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-align-right text-cyan-600"></i>
                الوصف
            </h2>
            <div class="text-slate-600 leading-relaxed whitespace-pre-line"><?php echo e($model->description); ?></div>
        </div>
    <?php endif; ?>

    <?php if($model->community_dataset_id && $model->dataset): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-database text-cyan-600"></i>
                مجموعة البيانات المستخدمة
            </h2>
            <a href="<?php echo e(route('community.datasets.show', $model->dataset)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-50 text-cyan-700 font-bold hover:bg-cyan-100 transition-colors">
                <i class="fas fa-external-link-alt"></i>
                <?php echo e($model->dataset->title); ?>

            </a>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
        <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
            <i class="fas fa-list-ol text-amber-600"></i>
            شرح الخطوات والمنهجية (من المساهم)
        </h2>
        <div class="text-slate-700 leading-relaxed whitespace-pre-line rounded-xl bg-slate-50/80 p-4 border border-slate-100"><?php echo e($model->methodology_steps ?: '—'); ?></div>
    </div>

    <?php if($model->performance_metrics && is_array($model->performance_metrics) && count($model->performance_metrics) > 0): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-600"></i>
                مقاييس الأداء
            </h2>
            <div class="flex flex-wrap gap-3">
                <?php $__currentLoopData = $model->performance_metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 font-bold">
                        <span class="text-sm opacity-80"><?php echo e($key); ?>:</span>
                        <span><?php echo e(is_numeric($value) ? number_format((float)$value, 4) : $value); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($model->usage_instructions): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-code text-blue-600"></i>
                طريقة الاستخدام أو الاستدعاء
            </h2>
            <pre class="text-sm text-slate-700 bg-slate-900 text-slate-100 p-4 rounded-xl overflow-x-auto whitespace-pre-wrap font-mono"><?php echo e($model->usage_instructions); ?></pre>
        </div>
    <?php endif; ?>

    <?php
        $filesList = $model->files_list;
        $previewableExts = ['py', 'pyw', 'ipynb', 'json', 'txt', 'md'];
    ?>
    <?php if(!empty($filesList)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-download text-cyan-600"></i>
                تحميل الملفات
            </h2>
            <p class="text-slate-500 text-sm mb-3">الملفات مُخزَّنة على Cloudflare. ملفات بايثون (.py, .ipynb) يمكن <strong>عرض محتواها</strong> أو تحميلها.</p>
            <div class="space-y-2">
                <?php $__currentLoopData = $filesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $path = is_array($file) ? ($file['path'] ?? null) : null;
                        $name = is_array($file) ? ($file['original_name'] ?? basename($path)) : basename($path);
                        $size = is_array($file) ? ($file['size'] ?? '') : '';
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $isPy = in_array($ext, ['py', 'pyw'], true);
                        $isNotebook = $ext === 'ipynb';
                        $canPreview = in_array($ext, $previewableExts, true);
                        $fileIcon = $isPy ? 'fa-file-code' : ($isNotebook ? 'fa-book-open' : ($ext === 'json' ? 'fa-file-code' : 'fa-file'));
                        $fileBadge = $isPy ? 'سكربت بايثون' : ($isNotebook ? 'Jupyter Notebook' : ($ext === 'json' ? 'JSON' : ''));
                    ?>
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 <?php echo e($isPy || $isNotebook ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600'); ?>">
                                <i class="fas <?php echo e($fileIcon); ?> text-sm"></i>
                            </span>
                            <div class="min-w-0">
                                <span class="font-mono text-sm text-slate-800 truncate block" title="<?php echo e($name); ?>"><?php echo e($name); ?></span>
                                <?php if($fileBadge): ?>
                                    <span class="text-xs text-slate-500"><?php echo e($fileBadge); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($size): ?><span class="text-xs text-slate-500 shrink-0"><?php echo e($size); ?></span><?php endif; ?>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <?php if($canPreview): ?>
                                <a href="<?php echo e(route('community.models.file-preview', [$model, $idx])); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 text-xs font-bold hover:bg-amber-200" title="عرض المحتوى">
                                    <i class="fas fa-eye"></i>
                                    <span>عرض</span>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('community.models.download-file', [$model, $idx])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-cyan-100 text-cyan-800 text-xs font-bold hover:bg-cyan-200" title="تحميل">
                                <i class="fas fa-download"></i>
                                <span>تحميل</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/models/show.blade.php ENDPATH**/ ?>