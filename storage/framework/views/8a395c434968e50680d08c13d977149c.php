

<?php $__env->startSection('title', __('auth.dashboard')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-8">
    
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-cyan-600 via-blue-600 to-slate-800 p-8 sm:p-10 shadow-2xl text-white">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80"></div>
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 bg-cyan-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-2xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <p class="text-cyan-200/90 text-sm font-bold mb-2">مجتمع البيانات والذكاء الاصطناعي</p>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black mb-2 tracking-tight">مرحباً، <?php echo e($user->name); ?>!</h1>
                <p class="text-white/90 text-sm sm:text-base max-w-xl">مكانك للمسابقات، مجموعات البيانات، مكتبة النماذج والمناقشات. انضم للمجتمع وارتقِ بمهاراتك.</p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="<?php echo e(route('community.data.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur border border-white/20 font-bold text-sm transition-all">
                    <i class="fas fa-database"></i>
                    <span>صفحة البيانات</span>
                </a>
                <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur border border-white/20 font-bold text-sm transition-all">
                    <i class="fas fa-brain"></i>
                    <span>مكتبة النماذج</span>
                </a>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="<?php echo e(route('community.competitions.index')); ?>" class="group block bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-xl hover:border-amber-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold mb-1">المسابقات</p>
                    <p class="text-3xl font-black text-amber-600"><?php echo e($stats['competitions_count'] ?? 0); ?></p>
                    <p class="text-xs text-slate-400 mt-1">مسابقة نشطة</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
            </div>
        </a>
        <a href="<?php echo e(route('community.data.index')); ?>" class="group block bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold mb-1">مجموعات البيانات</p>
                    <p class="text-3xl font-black text-blue-600"><?php echo e($stats['datasets_count'] ?? 0); ?></p>
                    <p class="text-xs text-slate-400 mt-1">مجموعة متاحة</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
        </a>
        <a href="<?php echo e(route('community.models.index')); ?>" class="group block bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-xl hover:border-amber-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold mb-1">مكتبة النماذج</p>
                    <p class="text-3xl font-black text-amber-600"><?php echo e($stats['models_count'] ?? 0); ?></p>
                    <p class="text-xs text-slate-400 mt-1">نموذج معتمد</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fas fa-brain text-xl"></i>
                </div>
            </div>
        </a>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-bold mb-1">المناقشات</p>
                    <p class="text-3xl font-black text-emerald-600">0</p>
                    <p class="text-xs text-slate-400 mt-1">قريباً</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                    <i class="fas fa-comments text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center"><i class="fas fa-rocket"></i></span>
            كيف تبدأ اليوم
        </h2>
        <p class="text-slate-600 mb-6">اختر تركيزك: تصفح المسابقات، مجموعات البيانات أو مكتبة النماذج، أو انتقل إلى الكورسات.</p>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo e(route('community.competitions.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-trophy"></i>
                <span>المسابقات</span>
            </a>
            <a href="<?php echo e(route('community.data.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-database"></i>
                <span>مجموعات البيانات</span>
            </a>
            <a href="<?php echo e(route('community.models.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-brain"></i>
                <span>مكتبة النماذج</span>
            </a>
            <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                <i class="fas fa-book"></i>
                <span>تصفح الكورسات</span>
            </a>
            <a href="<?php echo e(route('public.community.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 text-slate-700 font-bold hover:border-slate-300 hover:bg-slate-50 transition-colors">
                <i class="fas fa-users"></i>
                <span>عن المجتمع</span>
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-trophy text-amber-600"></i>
                    مسابقات المجتمع
                </h2>
                <a href="<?php echo e(route('community.competitions.index')); ?>" class="text-sm font-bold text-amber-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4">
                <?php if($recentCompetitions->isNotEmpty()): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $recentCompetitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/80 hover:bg-amber-50/80 border border-transparent hover:border-amber-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-trophy text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate text-sm"><?php echo e($c->title); ?></p>
                                    <?php if($c->start_at || $c->end_at): ?>
                                        <p class="text-xs text-slate-500">
                                            <?php if($c->start_at): ?> <?php echo e($c->start_at->translatedFormat('Y-m-d')); ?> <?php endif; ?>
                                            <?php if($c->end_at): ?> — <?php echo e($c->end_at->translatedFormat('Y-m-d')); ?> <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('community.competitions.index')); ?>" class="text-amber-600 hover:text-amber-700 text-xs font-semibold flex-shrink-0">عرض</a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-8 text-slate-500">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-trophy text-xl text-slate-400"></i>
                        </div>
                        <p class="text-sm font-semibold">لا توجد مسابقات نشطة</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-database text-blue-600"></i>
                    مجموعات البيانات
                </h2>
                <a href="<?php echo e(route('community.data.index')); ?>" class="text-sm font-bold text-blue-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4">
                <?php if($recentDatasets->isNotEmpty()): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $recentDatasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/80 hover:bg-blue-50/80 border border-transparent hover:border-blue-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-database text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate text-sm"><?php echo e($d->title); ?></p>
                                    <?php if($d->file_size): ?>
                                        <p class="text-xs text-slate-500"><?php echo e($d->file_size); ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('community.data.show', $d)); ?>" class="text-blue-600 hover:text-blue-700 text-xs font-semibold flex-shrink-0">عرض</a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-8 text-slate-500">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-database text-xl text-slate-400"></i>
                        </div>
                        <p class="text-sm font-semibold">لا توجد مجموعات بيانات</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-brain text-amber-600"></i>
                    مكتبة النماذج
                </h2>
                <a href="<?php echo e(route('community.models.index')); ?>" class="text-sm font-bold text-amber-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4">
                <?php if(isset($recentModels) && $recentModels->isNotEmpty()): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $recentModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/80 hover:bg-amber-50/80 border border-transparent hover:border-amber-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-brain text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate text-sm"><?php echo e($m->title); ?></p>
                                    <?php if($m->license): ?>
                                        <p class="text-xs text-slate-500"><?php echo e(Str::limit($m->license, 20)); ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('community.models.show', $m)); ?>" class="text-amber-600 hover:text-amber-700 text-xs font-semibold flex-shrink-0">عرض</a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-8 text-slate-500">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-brain text-xl text-slate-400"></i>
                        </div>
                        <p class="text-sm font-semibold">لا توجد نماذج معتمدة</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-white p-6 text-center">
        <p class="text-slate-600 text-sm">هل لديك اقتراح أو سؤال؟ تواصل معنا من <a href="<?php echo e(route('public.contact')); ?>" class="text-cyan-600 font-bold hover:underline">صفحة التواصل</a>.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/community/dashboard/index.blade.php ENDPATH**/ ?>