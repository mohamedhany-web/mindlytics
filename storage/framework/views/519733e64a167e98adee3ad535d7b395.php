<?php $__env->startSection('title', 'المحافظ الذكية'); ?>
<?php $__env->startSection('header', 'المحافظ الذكية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $recentWallet = ($recentWallets ?? collect())->first();
    $netMonth = ($currentMonthDeposits ?? 0) - ($currentMonthWithdrawals ?? 0);
?>
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة المحافظ الذكية</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة محافظ الدفع المربوطة بالطلاب مع متابعة الأرصدة، المعاملات، وأنواع القنوات المالية المختلفة.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('admin.wallets.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    إضافة محفظة جديدة
                </a>
                <?php if($recentWallet): ?>
                    <a href="<?php echo e(route('admin.wallets.reports', $recentWallet)); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all duration-200">
                        <i class="fas fa-chart-pie"></i>
                        تقارير سريعة
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="px-6 py-5">
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    نشطة: <?php echo e(htmlspecialchars($stats['active'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    غير نشطة: <?php echo e(htmlspecialchars($stats['inactive'] ?? 0)); ?>

                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                    <i class="fas fa-chart-line text-xs"></i>
                    المعاملات المسجلة: <?php echo e(number_format($totalTransactions ?? 0)); ?>

                </span>
            </div>
        </div>
    </section>

    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 rounded-2xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-2/5 pointer-events-none opacity-20">
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/asfalt-dark.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">المحافظ الذكية</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                        <i class="fas fa-layer-group text-xs"></i>
                        إجمالي <?php echo e($stats['total'] ?? 0); ?> محفظة
                    </span>
                </div>
                <p class="mt-3 text-white/70 max-w-xl">
                    إدارة محافظ الدفع المربوطة بالطلاب مع متابعة الأرصدة، المعاملات، وأنواع القنوات المالية المختلفة.
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                        نشطة: <?php echo e($stats['active'] ?? 0); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <span class="w-2 h-2 rounded-full bg-rose-300"></span>
                        غير نشطة: <?php echo e($stats['inactive'] ?? 0); ?>

                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-chart-line text-xs"></i>
                        المعاملات المسجلة: <?php echo e(number_format($totalTransactions ?? 0)); ?>

                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="<?php echo e($recentWallet ? route('admin.wallets.reports', $recentWallet) : '#'); ?>"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all <?php echo e($recentWallet ? '' : 'opacity-60 pointer-events-none'); ?>">
                    <i class="fas fa-chart-pie"></i>
                    تقارير سريعة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars('إجمالي المحافظ')); ?></p>
                    <p class="text-2xl font-black text-slate-900"><?php echo e(number_format($stats['total'] ?? 0)); ?></p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 text-blue-600 shadow-sm">
                    <i class="fas fa-wallet text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars('يشمل كل المحافظ المربوطة بالطلاب.')); ?></p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars('الرصيد المتاح')); ?></p>
                    <p class="text-2xl font-black text-slate-900"><?php echo e(number_format($stats['total_balance'] ?? 0, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 shadow-sm">
                    <i class="fas fa-coins text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars('إجمالي الأرصدة الحالية بكل المحافظ.')); ?></p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars('الرصيد المعلّق')); ?></p>
                    <p class="text-2xl font-black text-slate-900"><?php echo e(number_format($stats['pending_balance'] ?? 0, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-amber-100 text-amber-600 shadow-sm">
                    <i class="fas fa-hourglass-half text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars('المبالغ المعلّقة أو قيد المراجعة.')); ?></p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-slate-200 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e(htmlspecialchars('صافي تدفقات الشهر')); ?></p>
                    <p class="text-2xl font-black text-slate-900"><?php echo e(number_format($netMonth, 2)); ?> ج.م</p>
                </div>
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-purple-100 text-purple-600 shadow-sm">
                    <i class="fas fa-wave-square text-lg"></i>
                </span>
            </div>
            <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars('الإيداعات ناقص السحوبات خلال ' . \Carbon\Carbon::now()->translatedFormat('F'))); ?>.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-black text-gray-900">نشاط الشهر الحالي</h2>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                        <i class="fas fa-calendar-alt text-xs"></i>
                        <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?>

                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/80">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-emerald-600">الإيداعات</p>
                            <i class="fas fa-arrow-down text-emerald-500"></i>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">
                            <?php echo e(number_format($currentMonthDeposits ?? 0, 2)); ?> ج.م
                        </p>
                    </div>
                    <div class="p-4 rounded-2xl border border-rose-100 bg-rose-50/80">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-rose-600">السحوبات</p>
                            <i class="fas fa-arrow-up text-rose-500"></i>
                        </div>
                        <p class="mt-3 text-2xl font-black text-gray-900">
                            <?php echo e(number_format($currentMonthWithdrawals ?? 0, 2)); ?> ج.م
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">توزيع حسب النوع</h2>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $typeDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-100 text-sky-600">
                                    <i class="fas fa-signal"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo e($type['label']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(number_format($type['wallets_count'])); ?> محفظة</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-sky-600">
                                <?php echo e(number_format($type['total_balance'], 2)); ?> ج.م
                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-gray-500">لا توجد بيانات كافية حالياً.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 class="text-lg font-black text-gray-900 mb-4">أحدث المحافظ المضافة</h2>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recentWallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($recent->name ?? 'محفظة بدون اسم'); ?></p>
                            <span class="text-xs text-gray-500"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs text-sky-600 mt-1"><?php echo e($recent->type_name); ?></p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo e($recent->user?->name ?? 'غير مرتبط'); ?> · <?php echo e($recent->user?->phone ?? 'بدون رقم'); ?>

                        </p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900">
                                <?php echo e(number_format($recent->balance, 2)); ?> ج.م
                            </span>
                            <a href="<?php echo e(route('admin.wallets.show', $recent)); ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-800">
                                تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-sm text-gray-500">لا توجد محافظ حديثة.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-900">جميع المحافظ</h2>
                <p class="text-sm text-gray-500 mt-1">قائمة المحافظ المفعلة وغير المفعلة مع تفاصيل الاتصال والرصيد.</p>
            </div>
        </div>

        <?php if($wallets->count()): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-3xl border border-gray-100 bg-white shadow-lg hover:shadow-xl transition-all p-6 flex flex-col gap-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-gray-900"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h3>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold 
                                        <?php if($wallet->is_active): ?> bg-emerald-100 text-emerald-600
                                        <?php else: ?> bg-rose-100 text-rose-600
                                        <?php endif; ?>">
                                        <span class="w-2 h-2 rounded-full <?php echo e($wallet->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                                        <?php echo e($wallet->is_active ? 'نشطة' : 'غير نشطة'); ?>

                                    </span>
                                </div>
                                <p class="text-xs text-sky-600 mt-2"><?php echo e($wallet->type_name); ?></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo e($wallet->user?->name ?? $wallet->account_holder ?? 'غير محدد'); ?> · <?php echo e($wallet->user?->phone ?? 'بدون رقم'); ?>

                                </p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-sky-100 text-sky-600">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">الرصيد الحالي</p>
                                <p class="mt-1 text-base font-black text-gray-900"><?php echo e(number_format($wallet->balance, 2)); ?> ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">الرصيد المعلّق</p>
                                <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e(number_format($wallet->pending_balance ?? 0, 2)); ?> ج.م</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">رقم الحساب</p>
                                <p class="mt-1 font-semibold text-gray-900"><?php echo e($wallet->account_number ?? 'غير متوفر'); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">البنك / القناة</p>
                                <p class="mt-1 font-semibold text-gray-900"><?php echo e($wallet->bank_name ?? 'غير محدد'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>أضيفت <?php echo e(optional($wallet->created_at)->diffForHumans()); ?></span>
                            <span>آخر تحديث <?php echo e(optional($wallet->updated_at)->diffForHumans()); ?></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-sky-100 text-sky-600 font-semibold hover:bg-sky-200 transition-all">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.wallets.transactions', $wallet)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="سجل المعاملات">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                <a href="<?php echo e(route('admin.wallets.reports', $wallet)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="التقارير">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <a href="<?php echo e(route('admin.wallets.edit', $wallet)); ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div>
                <?php echo e($wallets->withQueryString()->links()); ?>

            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-12 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <p>لا توجد محافظ حتى الآن. يمكنك إنشاء محفظة جديدة من خلال الزر العلوي.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/wallets/index.blade.php ENDPATH**/ ?>