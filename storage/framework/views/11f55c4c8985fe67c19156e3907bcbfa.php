

<?php $__env->startSection('title', ($model->title ?? 'نموذج') . ' - مكتبة النماذج'); ?>

<?php $__env->startPush('styles'); ?>
<style>[x-cloak]{display:none!important}</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $filesList = $model->files_list ?? [];
    $previewableExts = ['py', 'pyw', 'ipynb', 'json', 'txt', 'md'];
?>

<div class="w-full min-h-screen bg-slate-50/80" style="padding-top: 4.5rem;">
    
    <div class="bg-white border-b border-slate-200">
        <div class="w-full px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 text-sm font-semibold shrink-0">
                        <i class="fas fa-arrow-right"></i>
                        <span>مكتبة النماذج</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <h1 class="text-lg sm:text-xl font-black text-slate-900 truncate"><?php echo e($model->title); ?></h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <?php if($model->license): ?>
                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600"><?php echo e($model->license); ?></span>
                    <?php endif; ?>
                    <?php if($model->creator): ?>
                        <span class="text-slate-500 text-sm"><i class="fas fa-user ml-1"></i> <?php echo e($model->creator->name); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8" x-data="modelShowPanel()">
            
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                <?php if($model->description): ?>
                    <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h2 class="text-base font-black text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-align-right text-cyan-600"></i>
                            الوصف
                        </h2>
                        <div class="text-slate-600 leading-relaxed whitespace-pre-line"><?php echo e($model->description); ?></div>
                    </section>
                <?php endif; ?>

                <?php if($model->community_dataset_id && $model->dataset): ?>
                    <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h2 class="text-base font-black text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-database text-cyan-600"></i>
                            مجموعة البيانات المستخدمة
                        </h2>
                        <a href="<?php echo e(route('community.data.show', $model->dataset)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-50 text-cyan-700 font-bold hover:bg-cyan-100 transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                            <?php echo e($model->dataset->title); ?>

                        </a>
                    </section>
                <?php endif; ?>

                <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-base font-black text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-list-ol text-amber-600"></i>
                        شرح الخطوات والمنهجية (من المساهم)
                    </h2>
                    <div class="text-slate-700 leading-relaxed whitespace-pre-line rounded-xl bg-slate-50/80 p-4 border border-slate-100 text-sm"><?php echo e($model->methodology_steps ?: '—'); ?></div>
                </section>

                <?php if($model->performance_metrics && is_array($model->performance_metrics) && count($model->performance_metrics) > 0): ?>
                    <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h2 class="text-base font-black text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-chart-line text-emerald-600"></i>
                            مقاييس الأداء
                        </h2>
                        <div class="flex flex-wrap gap-3">
                            <?php $__currentLoopData = $model->performance_metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 font-bold text-sm">
                                    <span class="opacity-80"><?php echo e($key); ?>:</span>
                                    <span><?php echo e(is_numeric($value) ? number_format((float)$value, 4) : $value); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if($model->usage_instructions): ?>
                    <section class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <h2 class="text-base font-black text-slate-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-code text-blue-600"></i>
                            طريقة الاستخدام أو الاستدعاء
                        </h2>
                        <pre class="text-sm text-slate-100 bg-slate-900 p-4 rounded-xl overflow-x-auto whitespace-pre-wrap font-mono border border-slate-700"><?php echo e($model->usage_instructions); ?></pre>
                    </section>
                <?php endif; ?>
            </div>

            
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <i class="fas fa-folder-open text-cyan-600"></i>
                                الملفات (<?php echo e(count($filesList)); ?>)
                            </h2>
                            <?php if($model->file_size): ?>
                                <span class="text-xs text-slate-500"><?php echo e($model->file_size); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 max-h-[320px] overflow-y-auto">
                            <?php if(!empty($filesList)): ?>
                                <ul class="space-y-1.5">
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
                                            $fileBadge = $isPy ? 'بايثون' : ($isNotebook ? 'Notebook' : ($ext === 'json' ? 'JSON' : ''));
                                        ?>
                                        <li class="rounded-xl border border-slate-100 overflow-hidden transition-colors <?php echo e($canPreview ? 'hover:border-amber-200' : ''); ?>"
                                            :class="{ 'ring-2 ring-amber-400 bg-amber-50/50': selectedFileIndex === <?php echo e($idx); ?> }">
                                            <div class="flex items-center gap-2 p-3">
                                                <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 <?php echo e($isPy || $isNotebook ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600'); ?>">
                                                    <i class="fas <?php echo e($fileIcon); ?> text-xs"></i>
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-mono text-sm text-slate-800 truncate" title="<?php echo e($name); ?>"><?php echo e($name); ?></p>
                                                    <?php if($fileBadge): ?><span class="text-xs text-slate-500"><?php echo e($fileBadge); ?></span><?php endif; ?>
                                                    <?php if($size): ?><span class="text-xs text-slate-400 block"><?php echo e($size); ?></span><?php endif; ?>
                                                </div>
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <?php if($canPreview): ?>
                                                        <button type="button"
                                                                @click="loadPreview(<?php echo e($idx); ?>, '<?php echo e(addslashes($name)); ?>')"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-colors"
                                                                :class="selectedFileIndex === <?php echo e($idx); ?> ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800 hover:bg-amber-200'"
                                                                title="عرض المحتوى في الصفحة">
                                                            <i class="fas fa-eye"></i>
                                                            <span>عرض</span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="<?php echo e(route('community.models.download-file', [$model, $idx])); ?>" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-cyan-100 text-cyan-800 text-xs font-bold hover:bg-cyan-200" title="تحميل">
                                                        <i class="fas fa-download"></i>
                                                        <span>تحميل</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-slate-500 text-sm text-center py-6">لا توجد ملفات.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    
                    <section x-show="previewContent !== null" x-transition
                             class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                             x-cloak>
                        <div class="px-4 py-3 border-b border-slate-200 bg-slate-800 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <i class="fas fa-file-code text-amber-400"></i>
                                <span x-text="previewFileName || 'معاينة الملف'"></span>
                            </h3>
                            <button type="button" @click="closePreview()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors" title="إغلاق المعاينة">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="p-0 relative min-h-[200px] max-h-[480px] overflow-auto bg-slate-900">
                            <div x-show="loadingPreview" class="absolute inset-0 flex items-center justify-center bg-slate-900/90 z-10">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                                    <span class="text-sm font-semibold">جاري تحميل المحتوى...</span>
                                </div>
                            </div>
                            <pre x-show="!loadingPreview && previewContent !== ''" class="p-4 text-sm text-slate-100 font-mono whitespace-pre-wrap overflow-x-auto m-0" x-text="previewContent"></pre>
                            <pre x-show="!loadingPreview && previewContent === ''" class="p-4 text-slate-500 text-sm m-0" x-text="'المحتوى فارغ أو غير متاح.'"></pre>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        
        <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-center gap-4">
            <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
                <i class="fas fa-arrow-right"></i>
                العودة لمكتبة النماذج
            </a>
            <span class="text-slate-300">|</span>
            <a href="<?php echo e(route('public.community.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
                <i class="fas fa-home"></i>
                صفحة المجتمع
            </a>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function modelShowPanel() {
    return {
        selectedFileIndex: null,
        previewContent: null,
        previewFileName: '',
        loadingPreview: false,
        previewUrl: '<?php echo e(route("community.models.file-preview", [$model, "__INDEX__"])); ?>',

        loadPreview(index, fileName) {
            if (this.selectedFileIndex === index && this.previewContent !== null) {
                this.closePreview();
                return;
            }
            this.selectedFileIndex = index;
            this.previewFileName = fileName;
            this.loadingPreview = true;
            this.previewContent = '';
            const url = this.previewUrl.replace('__INDEX__', index);
            fetch(url)
                .then(r => r.text())
                .then(text => {
                    this.previewContent = text;
                    this.loadingPreview = false;
                })
                .catch(() => {
                    this.previewContent = 'تعذر تحميل المحتوى. جرّب التحميل أو افتح الرابط في نافذة جديدة.';
                    this.loadingPreview = false;
                });
        },

        closePreview() {
            this.selectedFileIndex = null;
            this.previewContent = null;
            this.previewFileName = '';
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\community\model-show.blade.php ENDPATH**/ ?>