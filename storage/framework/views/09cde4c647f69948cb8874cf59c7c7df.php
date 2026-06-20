

<?php $__env->startSection('title', 'التوظيف — Mindlytics'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('careers._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="hero-careers min-h-[42vh] flex items-center relative pt-24 pb-16 lg:pt-28 lg:pb-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-blue-100 text-sm font-semibold mb-5">
            <i class="fas fa-briefcase"></i>
            <span>انضم إلى فريق Mindlytics</span>
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
            الوظائف المتاحة
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto" style="text-shadow: 0 1px 4px rgba(0,0,0,0.2);">
            قدّم طلبك وارفع سيرتك الذاتية — فريق الموارد البشرية يراجع الطلبات ويتواصل مع المرشحين المناسبين
        </p>
    </div>
</section>

<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">
        <?php if(session('success')): ?>
            <div class="mb-8 rounded-2xl border-2 border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-check-circle text-lg"></i>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex items-center gap-3 mb-8">
            <div class="section-bar rounded-full"></div>
            <h2 class="text-2xl font-bold text-slate-800">فرص العمل الحالية</h2>
            <?php if($jobs->count() > 0): ?>
                <span class="text-sm font-bold text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full"><?php echo e($jobs->count()); ?> وظيفة</span>
            <?php endif; ?>
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('careers.show', $job)); ?>" class="careers-card bg-white rounded-2xl shadow-md p-6 block no-underline text-inherit group">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-blue-700 transition-colors truncate"><?php echo e($job->title); ?></h3>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <?php if($job->department): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                        <i class="fas fa-building text-[10px]"></i><?php echo e($job->department); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if($job->location): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                        <i class="fas fa-map-marker-alt text-[10px]"></i><?php echo e($job->location); ?>

                                    </span>
                                <?php endif; ?>
                                <?php if($job->employment_type): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-violet-50 text-violet-700 text-xs font-bold border border-violet-100">
                                        <i class="fas fa-clock text-[10px]"></i><?php echo e($job->employment_type); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25 flex-shrink-0">
                            <i class="fas fa-briefcase"></i>
                        </span>
                    </div>
                    <?php if($job->description): ?>
                        <p class="text-sm text-slate-600 mt-4 line-clamp-3 leading-relaxed"><?php echo e(\Illuminate\Support\Str::limit(strip_tags((string) $job->description), 160)); ?></p>
                    <?php endif; ?>
                    <div class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-blue-600 group-hover:gap-3 transition-all">
                        <span>عرض التفاصيل والتقديم</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="sm:col-span-2 rounded-2xl border-2 border-dashed border-slate-200 bg-white p-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p class="text-lg font-bold text-slate-800">لا توجد وظائف منشورة حالياً</p>
                    <p class="text-sm text-slate-500 mt-2">تابعنا لاحقاً أو تواصل معنا عبر صفحة التواصل</p>
                    <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl border-2 border-blue-200 bg-blue-50 text-blue-700 text-sm font-bold hover:bg-blue-100 transition-colors">
                        <i class="fas fa-envelope"></i>
                        تواصل معنا
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\careers\index.blade.php ENDPATH**/ ?>