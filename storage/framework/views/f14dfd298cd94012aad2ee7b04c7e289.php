

<?php $__env->startSection('title', __('admin.community_datasets')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
        مجموعات البيانات
    </h1>
    <p class="text-slate-600 mb-6">
        استكشف مجموعات بيانات مفتوحة للتدريب والتجربة في مشاريعك. استخدم البحث والتصنيف لتضييق النتائج.
    </p>

    
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm mb-8">
        <form action="<?php echo e(route('community.datasets.index')); ?>" method="GET" class="space-y-4" role="search">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text"
                           name="q"
                           value="<?php echo e(request('q', $currentSearch ?? '')); ?>"
                           placeholder="ابحث بالعنوان أو الوصف..."
                           class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 bg-slate-50/50">
                </div>
                <input type="hidden" name="category" value="<?php echo e(request('category', $currentCategory ?? '')); ?>">
                <button type="submit" class="px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shrink-0">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-bold text-slate-600">التصنيف:</span>
                <a href="<?php echo e(route('community.datasets.index', ['q' => request('q')])); ?>"
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?php echo e(empty($currentCategory) ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
                    الكل
                </a>
                <?php $__currentLoopData = $categoriesWithCount ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($cat['count'] > 0 || ($currentCategory ?? '') === $cat['key']): ?>
                        <a href="<?php echo e(route('community.datasets.index', ['category' => $cat['key'], 'q' => request('q')])); ?>"
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?php echo e(($currentCategory ?? '') === $cat['key'] ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'); ?>">
                            <?php echo e($cat['label']); ?>

                            <span class="opacity-80">(<?php echo e($cat['count']); ?>)</span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </form>
    </div>

    <?php if($datasets->isNotEmpty()): ?>
        <p class="text-slate-500 text-sm mb-3">
            عرض <?php echo e($datasets->firstItem()); ?>–<?php echo e($datasets->lastItem()); ?> من <?php echo e($datasets->total()); ?> مجموعة
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('community.datasets.show', $dataset)); ?>" class="group block bg-white rounded-xl border border-slate-200/80 p-4 shadow-sm hover:shadow hover:border-cyan-300/60 transition-all duration-200">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0 group-hover:bg-cyan-100 transition-colors">
                            <i class="fas fa-database text-sm"></i>
                        </div>
                        <?php if($dataset->category): ?>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-500">
                                <?php echo e($dataset->category_label); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1.5 line-clamp-2 group-hover:text-cyan-700 transition-colors"><?php echo e($dataset->title); ?></h3>
                    <?php if($dataset->description): ?>
                        <p class="text-slate-500 text-xs leading-relaxed mb-3 line-clamp-2"><?php echo e(Str::limit($dataset->description, 80)); ?></p>
                    <?php else: ?>
                        <div class="mb-3"></div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <?php if($dataset->file_size): ?>
                            <span class="text-xs text-slate-400"><?php echo e($dataset->file_size); ?></span>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-cyan-600 group-hover:underline">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>عرض</span>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($datasets->hasPages()): ?>
            <div class="mt-8"><?php echo e($datasets->withQueryString()->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm text-center">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-4xl"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 mb-3">لا توجد نتائج</h2>
            <p class="text-slate-600 max-w-xl mx-auto mb-6">
                <?php if(request('q') || request('category')): ?>
                    جرّب تغيير كلمات البحث أو التصنيف.
                <?php else: ?>
                    لم تُضف مجموعات بيانات بعد. عد لاحقاً أو شارك بمجموعة من لوحة المساهم.
                <?php endif; ?>
            </p>
            <a href="<?php echo e(route('community.datasets.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700">
                <i class="fas fa-list"></i> عرض الكل
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/datasets/index.blade.php ENDPATH**/ ?>