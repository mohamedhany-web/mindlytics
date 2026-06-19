

<?php $__env->startSection('title', 'مكتبة النماذج (Model Zoo) - مجتمع الذكاء الاصطناعي'); ?>

<?php $__env->startSection('content'); ?>
<section class="min-h-screen bg-gradient-to-b from-slate-50 to-white w-full" style="padding-top: 6rem;">
    <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 md:py-12">
        
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 text-sm font-bold mb-4">
                    <i class="fas fa-brain"></i>
                    <span>مجتمع الذكاء الاصطناعي</span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-3" style="font-family: 'Tajawal', 'Cairo', sans-serif;">
                    مكتبة النماذج (Model Zoo)
                </h1>
                <p class="text-slate-600 text-lg max-w-2xl">
                    نماذج مدربة جاهزة للاستخدام أو إعادة التدريب، مرتبطة بمجموعات البيانات مع شرح المنهجية وأداء كل نموذج — بدون تسجيل دخول.
                </p>
            </div>
            <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()->is_community_contributor ?? false): ?>
            <a href="<?php echo e(route('community.contributor.models.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-amber-600 text-white font-bold shadow-lg hover:bg-amber-700 transition-all shrink-0">
                <i class="fas fa-th-list"></i>
                <span>لوحة نماذجي</span>
            </a>
            <?php else: ?>
            <a href="<?php echo e(route('community.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-amber-600 text-white font-bold shadow-lg hover:bg-amber-700 transition-all shrink-0">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة المجتمع</span>
            </a>
            <?php endif; ?>
            <?php else: ?>
            <a href="<?php echo e(route('community.login')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-100 text-slate-700 font-bold border-2 border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-all shrink-0">
                <i class="fas fa-sign-in-alt"></i>
                <span>تسجيل الدخول للمشاركة</span>
            </a>
            <?php endif; ?>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-8">
            <form action="<?php echo e(route('community.models.index')); ?>" method="GET" role="search" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    <input type="text"
                           name="q"
                           value="<?php echo e(request('q', $currentSearch ?? '')); ?>"
                           placeholder="ابحث في النماذج (العنوان، الوصف، المنهجية)..."
                           class="w-full pl-4 pr-12 py-3.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 bg-slate-50/50 text-slate-900 placeholder-slate-400">
                </div>
                <button type="submit" class="px-6 py-3.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shrink-0">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
            </form>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-brain text-amber-600"></i>
                النماذج المعتمدة
            </h2>
            <?php if($models->isNotEmpty()): ?>
                <p class="text-slate-500 text-sm">
                    عرض <?php echo e($models->firstItem()); ?>–<?php echo e($models->lastItem()); ?> من <?php echo e(number_format($models->total())); ?> نموذج
                </p>
            <?php endif; ?>
        </div>

        <?php if($models->isNotEmpty()): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4">
                <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('community.models.show', $model)); ?>" class="group block bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-lg hover:border-amber-200 overflow-hidden transition-all duration-300">
                        <div class="aspect-[3/1] bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-white/80 shadow flex items-center justify-center text-amber-600 group-hover:scale-105 transition-transform">
                                <i class="fas fa-brain text-lg"></i>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-bold text-slate-900 mb-1 line-clamp-2 group-hover:text-amber-700 transition-colors"><?php echo e($model->title); ?></h3>
                            <?php if($model->creator): ?>
                                <p class="text-xs text-slate-500 mb-1.5 truncate"><?php echo e($model->creator->name); ?></p>
                            <?php endif; ?>
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                <?php if($model->license): ?>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600"><?php echo e(Str::limit($model->license, 12)); ?></span>
                                <?php endif; ?>
                                <?php if($model->file_size): ?>
                                    <span class="text-[10px] text-slate-400"><?php echo e($model->file_size); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($model->dataset): ?>
                                <p class="text-[10px] text-cyan-600 mb-1.5 truncate" title="<?php echo e($model->dataset->title); ?>"><i class="fas fa-database ml-0.5"></i> <?php echo e(Str::limit($model->dataset->title, 22)); ?></p>
                            <?php endif; ?>
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 text-[10px] text-slate-500">
                                <?php if($model->downloads_count > 0): ?>
                                    <span><i class="fas fa-download ml-0.5"></i> <?php echo e(number_format($model->downloads_count)); ?> تحميل</span>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>
                                <span class="inline-flex items-center gap-0.5 font-bold text-amber-600 group-hover:underline">
                                    <span>عرض</span>
                                    <i class="fas fa-arrow-left text-[8px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($models->hasPages()): ?>
                <div class="mt-10 flex justify-center">
                    <?php echo e($models->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-24 h-24 rounded-2xl bg-amber-50 text-amber-400 flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-brain text-5xl"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-3">لا توجد نماذج حالياً</h2>
                <p class="text-slate-600 max-w-xl mx-auto mb-6">
                    <?php if(request('q')): ?>
                        جرّب تغيير كلمات البحث.
                    <?php else: ?>
                        سيتم إضافة النماذج قريباً. عد لاحقاً أو سجّل دخولك كمساهم في الذكاء الاصطناعي للمشاركة.
                    <?php endif; ?>
                </p>
                <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700">
                    <i class="fas fa-list"></i> عرض الكل
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-12 text-center">
            <a href="<?php echo e(route('public.community.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
                <i class="fas fa-arrow-right"></i>
                العودة لصفحة المجتمع
            </a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\community\models.blade.php ENDPATH**/ ?>