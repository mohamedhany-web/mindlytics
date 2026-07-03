

<?php $__env->startSection('title', 'عرض تقديم نموذج'); ?>
<?php $__env->startSection('header', 'عرض تقديم نموذج'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-4 md:p-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="<?php echo e(route('admin.community.submissions.models.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة التقديمات</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-black text-slate-900"><?php echo e($model->title); ?></h1>
            <div class="flex items-center gap-2">
                <?php if($model->status === \App\Models\CommunityModel::STATUS_PENDING): ?>
                    <form action="<?php echo e(route('admin.community.submissions.model.approve', $model)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700">
                            <i class="fas fa-check ml-1"></i> موافقة ونشر
                        </button>
                    </form>
                    <form action="<?php echo e(route('admin.community.submissions.model.reject', $model)); ?>" method="POST" class="inline" onsubmit="return confirm('رفض هذا النموذج؟');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">
                            <i class="fas fa-times ml-1"></i> رفض
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <h2 class="text-sm font-bold text-slate-500 mb-1">المساهم</h2>
                <p class="text-slate-800 font-semibold"><?php echo e($model->creator->name ?? '—'); ?></p>
                <p class="text-sm text-slate-500"><?php echo e($model->creator->email ?? '—'); ?></p>
                <p class="text-xs text-slate-400 mt-1"><?php echo e($model->created_at->format('Y-m-d H:i')); ?></p>
            </div>

            <?php if($model->description): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">الوصف</h2>
                    <div class="text-slate-700 whitespace-pre-line rounded-xl bg-slate-50 p-4"><?php echo e($model->description); ?></div>
                </div>
            <?php endif; ?>

            <?php if($model->dataset): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">مجموعة البيانات المرتبطة</h2>
                    <p class="text-cyan-700 font-semibold"><?php echo e($model->dataset->title); ?></p>
                </div>
            <?php endif; ?>

            <div>
                <h2 class="text-sm font-bold text-slate-500 mb-2">شرح الخطوات والمنهجية (من المساهم)</h2>
                <div class="text-slate-700 whitespace-pre-line rounded-xl bg-amber-50/80 border border-amber-100 p-4"><?php echo e($model->methodology_steps ?: '—'); ?></div>
            </div>

            <?php if($model->performance_metrics && is_array($model->performance_metrics) && count($model->performance_metrics) > 0): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">مقاييس الأداء</h2>
                    <div class="flex flex-wrap gap-2">
                        <?php $__currentLoopData = $model->performance_metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-sm"><?php echo e($key); ?>: <?php echo e(is_numeric($value) ? number_format((float)$value, 4) : $value); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($model->license): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-1">الترخيص</h2>
                    <p class="text-slate-800 font-semibold"><?php echo e($model->license); ?></p>
                </div>
            <?php endif; ?>

            <?php if($model->usage_instructions): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">طريقة الاستخدام أو الاستدعاء</h2>
                    <pre class="text-sm text-slate-700 bg-slate-900 text-slate-100 p-4 rounded-xl overflow-x-auto whitespace-pre-wrap font-mono"><?php echo e($model->usage_instructions); ?></pre>
                </div>
            <?php endif; ?>

            <?php $filesList = $model->files_list; ?>
            <?php if(!empty($filesList)): ?>
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">ملفات النموذج</h2>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $filesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $path = is_array($file) ? ($file['path'] ?? null) : null;
                                $name = is_array($file) ? ($file['original_name'] ?? basename($path)) : basename($path);
                                $size = is_array($file) ? ($file['size'] ?? '') : '';
                            ?>
                            <li class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="font-mono text-sm text-slate-800 truncate"><?php echo e($name); ?></span>
                                <?php if($size): ?><span class="text-xs text-slate-500"><?php echo e($size); ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\community\submissions-model-show.blade.php ENDPATH**/ ?>