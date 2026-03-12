

<?php $__env->startSection('title', 'لوحة المساهم'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php if(session('success')): ?>
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="p-4 rounded-xl bg-amber-100 border border-amber-300 text-amber-800"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>

    <div class="bg-gradient-to-br from-cyan-600 via-teal-600 to-slate-800 rounded-3xl border border-cyan-500/20 p-6 sm:p-8 shadow-xl text-white overflow-hidden relative">
        <div class="absolute top-0 left-0 w-40 h-40 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative z-10">
            <p class="text-cyan-200 text-sm font-bold mb-1">لوحة المساهم — مجتمع الذكاء الاصطناعي</p>
            <h1 class="text-2xl sm:text-3xl font-black mb-2">مرحباً، <?php echo e($user->name); ?>!</h1>
            <p class="text-white/90 text-sm sm:text-base max-w-xl">يمكنك رفع مجموعات البيانات وستتم مراجعتها من الإدارة قبل النشر. تابع حالة تقديماتك من هنا.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="<?php echo e(route('community.contributor.datasets.index')); ?>" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-cyan-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">إجمالي التقديمات</p>
                    <p class="text-3xl font-black text-slate-800"><?php echo e($myDatasetsCount ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-cyan-500 group-hover:text-white transition-colors">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
            </div>
        </a>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">قيد المراجعة</p>
                    <p class="text-3xl font-black text-amber-600"><?php echo e($pendingCount ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">معتمدة</p>
                    <p class="text-3xl font-black text-emerald-600"><?php echo e($approvedCount ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">مرفوضة</p>
                    <p class="text-3xl font-black text-red-600"><?php echo e($rejectedCount ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('community.contributor.datasets.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md">
            <i class="fas fa-plus"></i>
            <span>تقديم مجموعة بيانات جديدة</span>
        </a>
        <a href="<?php echo e(route('community.contributor.datasets.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
            <i class="fas fa-list"></i>
            <span>عرض كل تقديماتي</span>
        </a>
    </div>

    <?php if(isset($recentSubmissions) && $recentSubmissions->count() > 0): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-900">آخر التقديمات</h2>
            <a href="<?php echo e(route('community.contributor.datasets.index')); ?>" class="text-sm font-bold text-cyan-600 hover:text-cyan-700">عرض الكل</a>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__currentLoopData = $recentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-bold text-slate-900"><?php echo e($d->title); ?></p>
                    <p class="text-sm text-slate-500"><?php echo e($d->created_at->diffForHumans()); ?></p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                        <?php if($d->status === 'pending'): ?> bg-amber-100 text-amber-700
                        <?php elseif($d->status === 'approved'): ?> bg-emerald-100 text-emerald-700
                        <?php else: ?> bg-red-100 text-red-700 <?php endif; ?>">
                        <?php if($d->status === 'pending'): ?> قيد المراجعة
                        <?php elseif($d->status === 'approved'): ?> معتمدة
                        <?php else: ?> مرفوضة <?php endif; ?>
                    </span>
                    <?php if($d->status === 'approved' && $d->is_active): ?>
                        <?php $shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode(url(route('community.data.show', $d))); ?>
                        <a href="<?php echo e($shareUrl); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#0A66C2] text-white text-xs font-bold hover:bg-[#004182] transition-colors" title="مشاركة على LinkedIn — ساهمت في مجتمع البيانات والذكاء الاصطناعي">
                            <i class="fab fa-linkedin"></i>
                            <span>مشاركة LinkedIn</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
        <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
        <p class="text-slate-600 mb-4">لم تقدم أي مجموعة بيانات بعد.</p>
        <a href="<?php echo e(route('community.contributor.datasets.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>تقديم مجموعة بيانات</span>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/community/contributor/dashboard.blade.php ENDPATH**/ ?>