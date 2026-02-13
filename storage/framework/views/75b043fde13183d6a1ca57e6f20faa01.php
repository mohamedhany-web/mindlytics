

<?php $__env->startSection('title', 'خطط التقسيط - Mindlytics'); ?>
<?php $__env->startSection('header', 'خطط التقسيط والاشتراكات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusColors = [
        true => 'bg-emerald-100 text-emerald-700',
        false => 'bg-rose-100 text-rose-700',
    ];
?>
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">لوحة خطط التقسيط</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-layer-group text-xs"></i>
                        إجمالي <?php echo e(number_format($stats['total'] ?? 0)); ?> خطة
                    </span>
                </div>
                <p class="mt-3 text-white/70 max-w-2xl">
                    تعرّف على أداء خطط الدفع بالتقسيط، قيمها الإجمالية، وعدد الاتفاقيات المرتبطة بها مع متابعة الخطط التي تستخدم التجديد الآلي عند تسجيل الطلاب.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                        نشطة: <?php echo e(number_format($stats['active'] ?? 0)); ?>

                    </span>
                    <span class="inline-flex items-center_gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-rose-300"></span>
                        غير نشطة: <?php echo e(number_format($stats['inactive'] ?? 0)); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-robot text-xs"></i>
                        توليد تلقائي: <?php echo e(number_format($stats['auto_generate'] ?? 0)); ?>

                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="<?php echo e(route('admin.installments.plans.create')); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-plus"></i>
                    إضافة خطة جديدة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white shadow-lg border border-sky-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-sky-500">إجمالي القيم الممولة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($stats['total_amount'] ?? 0, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                    <i class="fas fa-coins text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">إجمالي المبالغ التي تغطيها الخطط الحالية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-500">إجمالي الدفعات المقدمة</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($stats['total_deposit'] ?? 0, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-piggy-bank text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">قيمة الدفعات المقدمة المطلوبة عند الاشتراك.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-500">متوسط عدد الأقساط</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($stats['average_installments'] ?? 0, 1)); ?></p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                    <i class="fas fa-chart-area text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">متوسط الأقساط لكل خطة تمويلية.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-500">خطط جديدة هذا الشهر</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($monthlyNew ?? 0)); ?></p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-calendar-plus text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">بقيمة <?php echo e(number_format($monthlyAmount ?? 0, 2)); ?> ج.م منذ بداية الشهر.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">توزيع حسب دورية السداد</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-sky-100 text-sky-700">
                        <i class="fas fa-clock text-xs"></i>
                        <?php echo e($frequencyBreakdown->sum('plans_count')); ?> خطة
                    </span>
                </div>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $frequencyBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-100 text-sky-600">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($unitLabels[$item->frequency_unit] ?? $item->frequency_unit); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(number_format($item->plans_count)); ?> خطة</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-sky-600"><?php echo e(number_format($item->total_amount, 2)); ?> ج.م</p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-gray-500">لا توجد بيانات للتوزيع حالياً.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">أعلى الخطط قيمة</h2>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $highValuePlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($plan->name); ?></p>
                                <span class="text-xs text-gray-500"><?php echo e(optional($plan->created_at)->diffForHumans()); ?></span>
                            </div>
                            <p class="text-xs text-sky-600 mt-1"><?php echo e($plan->course->title ?? 'خطة عامة'); ?></p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($plan->total_amount ?? 0, 2)); ?> ج.م</span>
                                <a href="<?php echo e(route('admin.installments.plans.show', $plan)); ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                                    تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-500">لا توجد خطط مميزة بعد.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-lg font-black text-gray-900 mb-4">أحدث الخطط</h2>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recentPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($recent->name); ?></p>
                            <span class="text-xs text-gray-500"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs text-sky-600 mt-1"><?php echo e($recent->course->title ?? 'خطة عامة'); ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e(number_format($recent->installments_count)); ?> دفعة · كل <?php echo e($recent->frequency_interval); ?> <?php echo e($unitLabels[$recent->frequency_unit] ?? $recent->frequency_unit); ?></p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($recent->total_amount ?? 0, 2)); ?> ج.م</span>
                            <a href="<?php echo e(route('admin.installments.plans.show', $recent)); ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                                عرض سريع <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-sm text-gray-500">لا توجد خطط حديثة.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-900">قائمة خطط التقسيط</h2>
                <p class="text-sm text-gray-500 mt-1">كل الخطط المتاحة مع تفاصيل المبالغ، الدورية، وعدد الاتفاقيات المرتبطة.</p>
            </div>
        </div>

        <?php if($plans->count()): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-3xl border border-gray-100 bg-white shadow-lg hover:shadow-xl transition-all p-6 flex flex-col gap-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-gray-900"><?php echo e($plan->name); ?></h3>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusColors[$plan->is_active] ?? 'bg-gray-100 text-gray-700'); ?>">
                                        <span class="w-2 h-2 rounded-full <?php echo e($plan->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                                        <?php echo e($plan->is_active ? 'نشطة' : 'معطلة'); ?>

                                    </span>
                                    <?php if($plan->auto_generate_on_enrollment): ?>
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-600">
                                            <i class="fas fa-robot"></i>
                                            توليد تلقائي
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-sky-600 mt-2"><?php echo e($plan->course->title ?? 'خطة عامة'); ?></p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">إجمالي المبلغ</p>
                                <p class="mt-1 text-base font-black text-gray-900"><?php echo e(number_format($plan->total_amount ?? 0, 2)); ?> ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">دفعة مقدمة</p>
                                <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e(number_format($plan->deposit_amount ?? 0, 2)); ?> ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">عدد الأقساط</p>
                                <p class="mt-1 font-semibold text-gray-900"><?php echo e($plan->installments_count); ?> دفعة</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">الدورية</p>
                                <p class="mt-1 font-semibold text-gray-900">كل <?php echo e($plan->frequency_interval); ?> <?php echo e($unitLabels[$plan->frequency_unit] ?? $plan->frequency_unit); ?></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>أضيفت <?php echo e(optional($plan->created_at)->diffForHumans()); ?></span>
                            <span>اتفاقيات مرتبطة: <?php echo e(number_format($plan->agreements_count ?? 0)); ?></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="<?php echo e(route('admin.installments.plans.show', $plan)); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-sky-100 text-sky-600 font-semibold hover:bg-sky-200 transition-all">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <a href="<?php echo e(route('admin.installments.plans.edit', $plan)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.installments.plans.destroy', $plan)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-rose-600 hover:bg-rose-50 transition-all" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div>
                <?php echo e($plans->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-4"></i>
                <p class="font-semibold">لا توجد خطط تقسيط بعد</p>
                <p class="text-sm text-gray-400 mt-2">ابدأ بإنشاء أول خطة لتفعيل نظام الأقساط.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/installments/plans/index.blade.php ENDPATH**/ ?>