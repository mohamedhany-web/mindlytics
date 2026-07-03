<?php $__env->startSection('title', 'منحي - Mindlytics'); ?>
<?php $__env->startSection('header', 'المنح الدراسية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('instructor.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- الهيدر -->
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-award text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">لوحة المدرب</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">المنح الدراسية</h1>
                    <p class="text-sm text-slate-500 mt-0.5">المنح المعيّنة لك — إدارة الطلاب والتفعيل.</p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.scholarships.students.index', ['status' => 'registered'])); ?>"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors flex-shrink-0">
                <i class="fas fa-user-clock"></i>
                <span>طلبات بانتظار التفعيل</span>
            </a>
        </div>
    </div>

    <?php echo $__env->make('instructor.scholarships._nav', ['active' => 'programs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">المنح</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-award text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">إجمالي المسجّلين</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['registrations'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-users text-violet-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">بانتظار التفعيل</p>
                <p class="text-2xl sm:text-3xl font-bold text-amber-600"><?php echo e($stats['pending'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مفعّلون</p>
                <p class="text-2xl sm:text-3xl font-bold text-emerald-600"><?php echo e($stats['activated'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- قائمة المنح -->
    <?php if($programs->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-bold text-slate-800 leading-snug"><?php echo e($program->name); ?></h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 <?php echo e($program->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                                <i class="fas <?php echo e($program->is_active ? 'fa-check-circle' : 'fa-ban'); ?>"></i>
                                <?php echo e($program->is_active ? 'نشطة' : 'غير نشطة'); ?>

                            </span>
                        </div>
                    </div>

                    <div class="px-5 py-4 space-y-3">
                        <?php if($program->description): ?>
                            <p class="text-sm text-slate-600 line-clamp-2"><?php echo e(Str::limit($program->description, 120)); ?></p>
                        <?php endif; ?>

                        <?php if($program->course): ?>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-book-open text-sky-600 text-xs"></i>
                                </div>
                                <span class="text-slate-500">الكورس:</span>
                                <span class="text-slate-800 font-medium truncate"><?php echo e($program->course->title); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-200">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-lg font-bold text-slate-800"><?php echo e($program->registrations_count ?? 0); ?></div>
                                <div class="text-xs text-slate-500 font-medium">مسجّل</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-amber-600"><?php echo e($program->pending_count ?? 0); ?></div>
                                <div class="text-xs text-slate-500 font-medium">بانتظار</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-emerald-600"><?php echo e($program->activated_count ?? 0); ?></div>
                                <div class="text-xs text-slate-500 font-medium">مفعّل</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-200 flex flex-col sm:flex-row gap-2">
                        <a href="<?php echo e(route('instructor.scholarships.show', $program)); ?>"
                           class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-user-graduate"></i>
                            <span>إدارة الطلاب</span>
                        </a>
                        <?php if($program->course): ?>
                            <a href="<?php echo e(route('instructor.courses.show', $program->course)); ?>"
                               class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors">
                                <i class="fas fa-book"></i>
                                <span>الكورس</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="rounded-2xl p-12 sm:p-16 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-sky-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-award text-4xl text-sky-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">لا توجد منح معيّنة لك</h3>
            <p class="text-slate-500 max-w-md mx-auto">عند تعيينك كمدرب لمنحة دراسية ستظهر هنا ويمكنك إدارة طلابها وتفعيلهم.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\scholarships\index.blade.php ENDPATH**/ ?>