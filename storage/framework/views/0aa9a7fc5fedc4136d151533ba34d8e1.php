

<?php $__env->startSection('title', $dataset->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-6xl mx-auto">
    
    <div class="mb-6">
        <a href="<?php echo e(route('community.datasets.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-semibold mb-4">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لمجموعات البيانات</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2"><?php echo e($dataset->title); ?></h1>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            <?php if($dataset->category): ?>
                <a href="<?php echo e(route('community.datasets.index', ['category' => $dataset->category])); ?>" class="inline-flex px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-600 hover:bg-slate-200"><?php echo e($dataset->category_label); ?></a>
            <?php endif; ?>
            <?php if($dataset->file_size): ?>
                <span class="text-slate-500 text-sm">الحجم: <?php echo e($dataset->file_size); ?></span>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($dataset->description): ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-align-right text-blue-600"></i>
                وصف مجموعة البيانات
            </h2>
            <div class="text-slate-600 leading-relaxed whitespace-pre-line"><?php echo e($dataset->description); ?></div>
        </div>
    <?php endif; ?>

    
    <?php if($dataset->file_url): ?>
        <div class="mb-6">
            <a href="<?php echo e($dataset->file_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-external-link-alt"></i>
                <span>فتح رابط التحميل</span>
            </a>
        </div>
    <?php endif; ?>

    <?php if($dataset->file_path): ?>
        <div class="mb-2">
            <a href="<?php echo e(route('community.datasets.download', $dataset)); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm">
                <i class="fas fa-download"></i>
                <span>تحميل الملف</span>
            </a>
        </div>
    <?php endif; ?>

    
    <?php if(!empty($previewHeaders) || !empty($previewRows)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between flex-wrap gap-2">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-table text-blue-600"></i>
                    معاينة البيانات
                </h2>
                <span class="text-slate-500 text-sm">أول <?php echo e(count($previewRows)); ?> صف</span>
            </div>
            <div class="overflow-auto max-h-[70vh] border-b border-slate-100">
                <table class="w-full min-w-full border-collapse text-right">
                    <thead class="sticky top-0 z-10 bg-slate-100 border-b-2 border-slate-200">
                        <tr>
                            <?php $__currentLoopData = $previewHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-4 py-3 text-sm font-bold text-slate-800 whitespace-nowrap border-l border-slate-200"><?php echo e(e($cell)); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $previewRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <?php $__currentLoopData = $previewHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-4 py-2.5 text-sm text-slate-700 whitespace-nowrap border-l border-slate-100"><?php echo e(e($row[$i] ?? '')); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <?php if($dataset->file_url): ?>
            <p class="text-slate-500 text-sm mt-4">لا يمكن معاينة المحتوى لهذا الرابط. استخدم زر «فتح رابط التحميل» أعلاه.</p>
        <?php elseif($dataset->file_path): ?>
            <p class="text-slate-500 text-sm mt-4">تعذر قراءة معاينة الملف أو الملف غير مدعوم (يدعم: Excel و CSV). يمكنك تحميل الملف أعلاه.</p>
        <?php else: ?>
            <p class="text-slate-500 text-sm mt-4">لا يوجد ملف مرفق لهذه المجموعة حالياً.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\community\datasets\show.blade.php ENDPATH**/ ?>