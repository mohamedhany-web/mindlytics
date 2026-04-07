<?php $__env->startSection('title', 'الاشتراكات'); ?>
<?php $__env->startSection('header', 'الاشتراكات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusColors = [
        'active' => 'bg-emerald-100 text-emerald-700',
        'expired' => 'bg-rose-100 text-rose-700',
        'cancelled' => 'bg-amber-100 text-amber-700',
    ];
?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm">
            <?php echo e(htmlspecialchars(session('success'))); ?>

        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة الاشتراكات</h2>
                    <p class="text-sm text-slate-600 mt-1">راقب أداء الاشتراكات، الإيرادات المتجددة، وحالات التجديد التلقائي.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-plus"></i>
                إضافة اشتراك جديد
            </a>
        </div>
        <div class="px-6 py-5">
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    نشطة: <?php echo e(number_format($stats['active'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    منتهية: <?php echo e(number_format($stats['expired'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    ملغاة: <?php echo e(number_format($stats['cancelled'] ?? 0)); ?>

                </span>
            </div>
        </div>
    </section>

    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 rounded-2xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">لوحة الاشتراكات</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-layer-group text-xs"></i>
                        إجمالي <?php echo e(number_format($stats['total'] ?? 0)); ?> اشتراك
                    </span>
                </div>
                <p class="mt-3 text-white/70 max-w-2xl">
                    راقب أداء الاشتراكات، الإيرادات المتجددة، وحالات التجديد التلقائي مع رؤية سريعة للحسابات التي أوشكت على الانتهاء.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                        نشطة: <?php echo e(number_format($stats['active'] ?? 0)); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-rose-300"></span>
                        منتهية: <?php echo e(number_format($stats['expired'] ?? 0)); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-amber-300"></span>
                        ملغاة: <?php echo e(number_format($stats['cancelled'] ?? 0)); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars('إيراد الاشتراكات النشطة')); ?></p>
                        <p class="text-2xl font-black text-slate-900"><?php echo e(number_format($stats['active_revenue'] ?? 0, 2)); ?> ج.م</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 shadow-sm flex items-center justify-center">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars('قيمة الخطط المفعلة حالياً.')); ?></p>
            </div>
        <div class="rounded-2xl bg-white shadow-lg border border-emerald-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-500">تجديد تلقائي</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($stats['auto_renew'] ?? 0)); ?></p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-sync text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">اشتراكات محددة للتجديد التلقائي.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-purple-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-500">اشتراكات هذا الشهر</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($monthlyNew ?? 0)); ?></p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600">
                    <i class="fas fa-calendar-plus text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">تم تفعيلها منذ بداية الشهر.</p>
        </div>
        <div class="rounded-2xl bg-white shadow-lg border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-500">إيراد الشهر الحالي</p>
                    <p class="mt-2 text-3xl font-black text-gray-900"><?php echo e(number_format($monthlyRevenue ?? 0, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-600">
                    <i class="fas fa-chart-line text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">إجمالي قيمة الاشتراكات الجديدة هذا الشهر.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">توزيع الاشتراكات حسب النوع</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                        <i class="fas fa-th-large text-xs"></i>
                        <?php echo e($planDistribution->sum('subscriptions_count')); ?> إجمالي
                    </span>
                </div>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $planDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                                    <i class="fas fa-tags"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e(htmlspecialchars($distribution['label'])); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(number_format($distribution['subscriptions_count'])); ?> اشتراك</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-blue-600"><?php echo e(number_format($distribution['total_price'], 2)); ?> ج.م</p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-gray-500">لا توجد بيانات كافية حالياً.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">اشتراكات يقترب انتهاءها</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-rose-100 text-rose-700">
                        <i class="fas fa-hourglass-half text-xs"></i>
                        خلال 30 يوم
                    </span>
                </div>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $expiringSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/60">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($upcoming->plan_name); ?></p>
                                <span class="text-xs text-gray-500"><?php echo e(optional($upcoming->end_date)->diffForHumans()); ?></span>
                            </div>
                            <p class="text-xs text-blue-600 mt-1"><?php echo e($upcoming->user->name ?? 'غير معروف'); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($upcoming->start_date?->format('Y-m-d')); ?> → <?php echo e($upcoming->end_date?->format('Y-m-d')); ?></p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($upcoming->price, 2)); ?> ج.م</span>
                                <a href="<?php echo e(route('admin.subscriptions.show', $upcoming)); ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-500">لا توجد اشتراكات على وشك الانتهاء.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-lg font-black text-gray-900 mb-4">أحدث الاشتراكات</h2>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recentSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e(htmlspecialchars($recent->plan_name)); ?></p>
                            <span class="text-xs text-gray-500"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1"><?php echo e(htmlspecialchars($recent->user->name ?? 'غير مرتبط')); ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e(htmlspecialchars(\App\Models\Subscription::typeLabel($recent->subscription_type))); ?></p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($recent->price, 2)); ?> ج.م</span>
                            <a href="<?php echo e(route('admin.subscriptions.show', $recent)); ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                عرض سريع <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-sm text-gray-500">لا توجد اشتراكات حديثة.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-900">قائمة الاشتراكات</h2>
                <p class="text-sm text-gray-500 mt-1">كل الاشتراكات الحالية مع تفاصيل المستخدم والحالة.</p>
            </div>
        </div>

        <?php if($subscriptions->count()): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-3xl border border-gray-100 bg-white shadow-lg hover:shadow-xl transition-all p-6 flex flex-col gap-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-gray-900"><?php echo e(htmlspecialchars($subscription->plan_name)); ?></h3>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border <?php echo e($statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-700 border-gray-200'); ?> <?php echo e($subscription->status === 'active' ? 'border-emerald-200' : ($subscription->status === 'expired' ? 'border-rose-200' : 'border-amber-200')); ?>">
                                        <span class="w-2 h-2 rounded-full <?php echo e($subscription->status === 'active' ? 'bg-emerald-500' : ($subscription->status === 'expired' ? 'bg-rose-500' : 'bg-amber-500')); ?>"></span>
                                        <?php echo e(htmlspecialchars($subscription->status === 'active' ? 'نشط' : ($subscription->status === 'expired' ? 'منتهي' : 'ملغي'))); ?>

                                    </span>
                                </div>
                                <p class="text-xs text-blue-600 mt-2"><?php echo e(htmlspecialchars(\App\Models\Subscription::typeLabel($subscription->subscription_type))); ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e(htmlspecialchars($subscription->user->name ?? 'غير معروف')); ?> · <?php echo e(htmlspecialchars($subscription->user->phone ?? 'بدون رقم')); ?></p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">السعر</p>
                                <p class="mt-1 text-base font-black text-gray-900"><?php echo e(number_format($subscription->price, 2)); ?> ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">دورة الفوترة</p>
                                <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e(htmlspecialchars(\App\Models\Subscription::billingCycleLabel($subscription->billing_cycle))); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">تاريخ البداية</p>
                                <p class="mt-1 font-semibold text-gray-900"><?php echo e($subscription->start_date?->format('Y-m-d') ?? 'غير محدد'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">تاريخ الانتهاء</p>
                                <p class="mt-1 font-semibold text-gray-900"><?php echo e($subscription->end_date?->format('Y-m-d') ?? 'غير محدد'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>أضيف <?php echo e(optional($subscription->created_at)->diffForHumans()); ?></span>
                            <span>تحديث <?php echo e(optional($subscription->updated_at)->diffForHumans()); ?></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="<?php echo e(route('admin.subscriptions.show', $subscription)); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-blue-100 text-blue-600 font-semibold hover:bg-blue-200 transition-all duration-200">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <a href="<?php echo e(route('admin.subscriptions.edit', $subscription)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.subscriptions.destroy', $subscription)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟')" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-rose-600 hover:bg-rose-50 transition-all" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="px-6 py-4 border-t border-slate-200">
                <?php echo e($subscriptions->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                        <i class="fas fa-layer-group text-2xl text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">لا توجد اشتراكات</p>
                        <p class="text-xs text-slate-500 mt-1">لم يتم إنشاء أي اشتراكات بعد</p>
                    </div>
                    <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        إضافة اشتراك جديد
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function sanitizeInput(input) {
    input.value = input.value.replace(/[<>'"&]/g, '');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/subscriptions/index.blade.php ENDPATH**/ ?>